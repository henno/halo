<?php namespace App;

use Doctrine\SqlFormatter\SqlFormatter;
use JetBrains\PhpStorm\NoReturn;


class Db
{
    private static ?Db $instance = null;
    private \mysqli $conn;
    public array $debugLog = [];

    const GET_RESULT = 1;
    const AFFECTED_ROWS = 2;

    private function __construct(string $host, string $user, string $password, string $dbname)
    {
        $this->conn = new \mysqli($host, $user, $password, $dbname);

        // Set error reporting level
        $this->conn->report_mode = MYSQLI_REPORT_ALL;

        if ($this->conn->connect_error) {
            throw new \Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance(): Db
    {
        if (self::$instance === null) {
            try {
                self::$instance = new self(
                    DATABASE_HOSTNAME,
                    DATABASE_USERNAME,
                    DATABASE_PASSWORD,
                    DATABASE_DATABASE
                );
            } catch (\Exception $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    private function debugQuery($query, $params)
    {
        foreach ($params as $param) {
            $query = preg_replace('/\?/', "'{$this->conn->real_escape_string($param)}'", $query, 1);
        }

        // If this query already exists in the debug log, update its count
        if (isset($this->debugLog[$query])) {

            $this->debugLog[$query]['count'] += 1;

            // Store the existing debug info
            $existingDebugInfo = $this->debugLog[$query];

            // Remove the existing debug info to move it to the end
            unset($this->debugLog[$query]);

            // Re-add the debug info to move it to the end
            $this->debugLog[$query] = $existingDebugInfo;

        } else {
            // If it's a new query, append to debug log
            $this->debugLog[$query] = [
                'query' => $query,
                'count' => 1,  // Initialize counter
                'cumulative_time' => 0.0  // Initialize cumulative time
            ];
        }

        return $query;
    }

    #[NoReturn] public static function displayError($e): void
    {
        // Remove previous output
        ob_clean();

        // Get the last query from the debug log
        $lastQuery = end(self::getInstance()->debugLog)['query'];

        // Get debug log
        $highlightedQuery = (new SqlFormatter())->format($lastQuery);
        echo("Error: {$e->getMessage()}<br><br><strong>Query:</strong><br><code>$highlightedQuery</code>");

        // Show full stack trace (HTML formatted)
        $trace = $e->getTrace();

        // Get the directory of the project root
        $rootDir = dirname(__DIR__, 2);

        // Remove the root directory from the file paths
        $trace = array_map(function ($item) use ($rootDir) {
            $item['file'] = str_replace($rootDir, '', $item['file']);
            return $item;
        }, $trace);


        echo '<br><br><strong>Stack trace:</strong><br>';
        echo '<pre>';
        foreach ($trace as $item) {
            $file = $item['file'] ?? '';
            $line = $item['line'] ?? '';
            $function = $item['function'] ?? '';
            $class = $item['class'] ?? '';
            $type = $item['type'] ?? '';
            echo "$file:$line <b>$class$type$function</b>\n";
        }
        echo '</pre>';

        // Display debug log
        echo '<br><br><strong>Debug log:</strong><br>';

        foreach (self::getDebugLog() as $logItem) {
            echo $logItem . '<br>';
        }

        // Display total query time
        echo '<br><strong>Aggregate Query Execution Time:</strong> ' . self::getTotalQueryTime() . ' seconds<br>';

    }

    private static function getTypeString(array $params): string
    {
        return implode('', array_map(function ($param) {
            $type = gettype($param);
            switch ($type) {
                case 'boolean':
                case 'integer':
                    return 'i';
                case 'double':
                    return 'd';
                case 'NULL':
                case 'string':
                    return 's';
                default:
                    throw new \Exception("Unsupported data type: {$type}");
            }
        }, $params));
    }

    private function executePrepared($query, $params = [], $returnType = self::GET_RESULT): bool|\mysqli_result
    {
        $types = self::getTypeString($params);
        $debugQuery = $this->debugQuery($query, $params);

        // Replace ? with NULL for null values and adjust types and params
        $newParams = [];
        $newTypes = '';
        for ($i = 0, $len = strlen($types); $i < $len; ++$i) {
            if ($types[$i] === 's' && $params[$i] === NULL) {
                $query = preg_replace('/\?/', 'NULL', $query, 1);
            } else {
                $newParams[] = $params[$i];
                $newTypes .= $types[$i];
            }
        }

        // Prepare the query
        $stmt = $this->conn->prepare($query);

        // Bind params if there are any
        $newTypes && $stmt->bind_param($newTypes, ...$newParams);

        // Start timer
        $startTime = microtime(true);

        // Execute the query
        $stmt->execute();

        // Stop timer
        $timeTaken = microtime(true) - $startTime;

        // Log the time taken
        $this->debugLog[$debugQuery]['cumulative_time'] += $timeTaken;

        // Return the affected rows if requested, else return the result
        return $returnType === self::AFFECTED_ROWS ? $stmt->affected_rows : $stmt->get_result();
    }

    public static function getOne($query, array $params = [])
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callingFunction = $backtrace[1]['function'] ?? 'Global Scope';
        return self::getInstance()->executePrepared($query, $params, $callingFunction)->fetch_array(MYSQLI_NUM)[0] ?? null;
    }

    public static function getCol($query, $params = [])
    {
        $result = self::getInstance()->executePrepared($query, $params);
        $output = [];
        while ($row = $result->fetch_array(MYSQLI_NUM)) $output[] = $row[0];
        return $output;
    }

    public static function getFirst($query, $params = [])
    {
        return self::getInstance()->executePrepared($query, $params)->fetch_assoc();
    }

    public static function getAll($query, $params = [])
    {
        $result = self::getInstance()->executePrepared($query, $params);
        $output = [];
        while ($row = $result->fetch_assoc()) $output[] = $row;
        return $output;
    }

    public static function q($query, $params = [])
    {
        return self::getInstance()->executePrepared($query, $params, self::AFFECTED_ROWS);
    }

    public static function insert($table, $data)
    {
        // Build field and placeholder lists
        $fields = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        // Prepare query
        $query = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

        self::getInstance()->executePrepared($query, array_values($data));
        return self::getInstance()->conn->insert_id;  // Return last insert ID
    }

    public static function delete($table, $whereClause, $whereParams = [])
    {
        // Prepare query
        $query = "DELETE FROM {$table} WHERE {$whereClause}";

        self::getInstance()->executePrepared($query, $whereParams);
        return self::getInstance()->conn->affected_rows;  // Return the number of affected rows
    }

    public static function update($table, $data, $whereClause, $whereParams = [])
    {
        // Building field updates
        $fields = array_keys($data);
        $fieldPlaceholders = implode(" = ?, ", $fields) . " = ?";

        // Prepare query
        $query = "UPDATE {$table} SET {$fieldPlaceholders} WHERE {$whereClause}";

        // Merging all values
        $values = array_merge(array_values($data), $whereParams);

        self::getInstance()->executePrepared($query, $values);
        return self::getInstance()->conn->affected_rows;  // Return the number of affected rows
    }

    public static function getDebugLog(): array
    {
        $result = [];
        $debugLog = self::getInstance()->debugLog;
        foreach ($debugLog as $item) {
            $time = number_format($item['cumulative_time'], 4);
            $item['query'] = preg_replace('/\s+/', ' ', $item['query']);
            $result[] = "$item[count] x  $time $item[query]";
        }

        // Reverse sort result array so that the last query is on top
        krsort($result);

        return $result;
    }

    public static function upsert($table, $data)
    {
        // Query the schema to determine the unique or primary key fields
        $describeQuery = "SHOW INDEX FROM {$table} WHERE Key_name = 'PRIMARY' OR Non_unique = 0";
        $uniqueFields = [];

        $columns = self::getAll($describeQuery);
        foreach ($columns as $column) {
            $uniqueFields[] = $column['Column_name'];
        }

        // Prepare the WHERE clause and parameters based on unique fields
        $whereClauseParts = [];
        $whereParams = [];
        foreach ($uniqueFields as $field) {
            if (isset($data[$field])) {
                $whereClauseParts[] = "{$field} = ?";
                $whereParams[] = $data[$field];
            }
        }

        $whereClause = implode(' OR ', $whereClauseParts);
        $selectQuery = "SELECT COUNT(*) FROM {$table} WHERE {$whereClause}";

        $existingRowCount = self::getOne($selectQuery, $whereParams);

        if ($existingRowCount === 0) {
            return self::insert($table, $data);
        } else {
            // We'll use the first unique field for the where clause. For more complex
            // scenarios, custom logic will be needed to determine which row(s) to update.
            return self::update($table, $data, "{$uniqueFields[0]} = ?", [$data[$uniqueFields[0]]]);
        }
    }

    public static function getTotalQueryTime(): float
    {
        $totalTime = 0.0;
        $debugLog = self::getInstance()->debugLog;
        foreach ($debugLog as $item) {
            $totalTime += $item['cumulative_time'];
        }
        return $totalTime;
    }
}