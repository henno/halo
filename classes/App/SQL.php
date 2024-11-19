<?php namespace App;


class SQL
{

    static function getWhere($criteria, $id_field = null): string
    {
        $where = '';
        if (!empty($criteria)) {
            if (is_array($criteria)) {
                $conditions = [];
                foreach ($criteria as $key => $value) {
                    $field = $key;
                    $operator = '=';

                    // List of SQL operators
                    $operators = [
                        'IS NOT NULL', 'IS NULL', 'NOT LIKE', 'LIKE', 'BETWEEN',
                        '>=', '<=', '<>', '!=', '=', '<', '>', 'IN', 'NOT IN'
                    ];

                    // Sort operators by length to match longer operators first
                    usort($operators, function ($a, $b) {
                        return strlen($b) - strlen($a);
                    });

                    // Detect operator in the key
                    foreach ($operators as $op) {
                        if (stripos($field, ' ' . $op) !== false) {
                            $parts = explode(' ' . $op, $field);
                            $field = trim($parts[0]);
                            $operator = strtoupper($op);
                            break;
                        }
                    }

                    // Escape field name (ensure it's a valid identifier)
                    $escapedField = self::escapeIdentifier($field);

                    if (is_array($value)) {
                        if (in_array($operator, ['IN', 'NOT IN'])) {
                            // Handle IN and NOT IN clauses
                            $escapedValues = array_map(function ($v) {
                                return self::escapeValue($v);
                            }, $value);
                            $valueList = implode(',', $escapedValues);
                            $conditions[] = "$escapedField $operator ($valueList)";
                        } elseif ($operator === 'BETWEEN') {
                            // Handle BETWEEN with array values
                            if (count($value) === 2) {
                                $escapedValue1 = self::escapeValue($value[0]);
                                $escapedValue2 = self::escapeValue($value[1]);
                                $conditions[] = "$escapedField BETWEEN $escapedValue1 AND $escapedValue2";
                            } else {
                                throw new Exception("BETWEEN operator requires an array with exactly two values.");
                            }
                        } else {
                            // Default to IN clause if value is an array
                            $escapedValues = array_map(function ($v) {
                                return self::escapeValue($v);
                            }, $value);
                            $valueList = implode(',', $escapedValues);
                            $conditions[] = "$escapedField IN ($valueList)";
                        }
                    } else {
                        if (in_array($operator, ['IS NULL', 'IS NOT NULL'])) {
                            // IS NULL and IS NOT NULL do not require a value
                            $conditions[] = "$escapedField $operator";
                        } elseif ($operator === 'BETWEEN') {
                            // Handle BETWEEN with string value
                            $conditions[] = "$escapedField BETWEEN $value";
                        } elseif (preg_match('/\b(NOW\(\)|CURDATE\(\)|CURRENT_DATE|YEAR\(\)|MONTH\(\)|DAY\(\))\b/i', $value)) {
                            // SQL function in value
                            $conditions[] = "$escapedField $operator $value";
                        } elseif (is_numeric($value)) {
                            // Numeric value
                            $conditions[] = "$escapedField $operator $value";
                        } else {
                            // Escape and quote the value
                            $escapedValue = self::escapeValue($value);
                            $conditions[] = "$escapedField $operator $escapedValue";
                        }
                    }
                }
                $where = "WHERE " . implode(' AND ', $conditions);
            } elseif (is_numeric($criteria) && $id_field !== null) {
                $escapedField = self::escapeIdentifier($id_field);
                $where = "WHERE $escapedField = $criteria";
            } elseif (is_string($criteria)) {
                // Assume the criteria is a safe condition string
                $where = "WHERE $criteria";
            }
        }

        return $where;
    }

    private static function escapeValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {

            return "'" . addslashes($value) . "'";
        }

    }

    private static function escapeIdentifier($identifier): string
    {
        // Remove any invalid characters from identifier
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}