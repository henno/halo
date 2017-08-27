<?php
/**
 * Database functions
 */


require 'classes/rb.php';
global $cfg;

R::setAutoResolve(TRUE);
R::setup("mysql:host=$cfg[DATABASE_HOSTNAME];dbname=$cfg[DATABASE_DATABASE]", $cfg['DATABASE_USERNAME'], $cfg['DATABASE_PASSWORD']);
// R::exec("SET sql_mode = ''"); // Disable strict GROUP BY check

R::freeze(FALSE);

//R::fancyDebug( TRUE );

class db
{

    private $instance = null;

    function __construct($cfg)
    {
        $host = $cfg['DATABASE_HOSTNAME'];
        $db = $cfg['DATABASE_DATABASE'];
        $user = $cfg['DATABASE_USERNAME'];
        $pass = $cfg['DATABASE_PASSWORD'];
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->instance = new PDO($dsn, $user, $pass, $opt);
    }

    /**
     * @param $sql
     * @param String[] ...$bindings
     * @return mixed
     */
    function query($sql, Array $bindings = [])
    {

        foreach ($bindings as $binding) {
            $sql = preg_replace('/\?/', $binding, $sql, 1);
        }

        return $this->instance->query($sql);


    }

    /**
     * Perform SQL query and return results as an Redbean object
     * @param $sql String query to be executed
     * @param String[] $bindings Bound parameters
     * @return array
     * @throws Exception
     */
    function get_first($sql, Array $bindings)
    {

        $result = [];
       $sql = "SELECT
                  orders.created_at created_at,
                  products.id       products__id,
                  products.name     products__name,
                  categories.id     products__category__id,
                  categories.name   products__category__name
                FROM order_rows
                  LEFT JOIN orders ON (orders.id = order_id)
                  LEFT JOIN products ON (products.id = product_id)
                  LEFT JOIN categories ON (categories.id = products.category_id)";
       $result = $this->query($sql);
       var_dump($result);
       while($row = $result->fetch){

       var_dump($row);
       }
       return $result;
    }

}

function build_result($result){
    $fields = array_keys($result);
    foreach ($fields as $field_name) {

        while(strpos($field_name, '__')!== FALSE){
            $name = substr($field_name, 0 , strpos($field_name, '__'));
            $result[$name];
        }
    }
}
global $db;
$db = new db($cfg);

$order = get("SELECT * FROM `order`");

foreach ($order as $item) {
    var_dump($item->orderer->name);
}


function get($sql, String ...$bindings)
{
    global $db;
    return $db->get_first($sql, $bindings);
}


/*
 * $result[0]['order_id'] = 1
 * $result[0]['user']['name'] = 'Demo User'
 * $order->user->name
 */
exit();

/*

Return all records as an object:
$order->id == 1
$order->createdAt == '2017-08-15 11:22:33'
$order->user_id = 1

When accessing a nonexistent field like $order->user, a magic __get method will be invoked and determining that this
field does not exist, it is tested whether a field with the same name but _id appended exists (a foreign key) and if
it does, a db query is performed (SELECT * FROM X WHERE id = Y, where X is the accessed field's name and Y is the value
of the field with the same name but _id appended in the first object) and query result is converted to object and a copy
of it is kept in a $this->cache[tag][key] where tag is a table name and key is what was in where.


When a field name with _id appended exists but a table with field name does not exist, foreign key constraints for
parent table are looked at if they contain an FK for accessed field name with _id appended to it, foreign table is
queried with that id in WHERE and result is again stored in cache and returned as an object



 */