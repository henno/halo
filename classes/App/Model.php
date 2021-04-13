<?php


namespace App;



class Model
{
    public static function get_all($criteria = null, $order_by = null, $primary_key = null): array
    {

        $result = [];

        // Get the name of the class which extended the Model
        $class_name = strtolower(preg_replace('/^(\w+\\\)*/', '', get_called_class()));

        // Generate database table name from class name
        $table = $class_name;

        // Use supplied primary key or derive it from class name
        $primary_key = $primary_key ? $primary_key : $class_name . 'Id' ;

        $where = SQL::getWhere($criteria, $primary_key);

        $rows = get_all("SELECT * FROM $table $where $order_by");

        // Return empty array if no result
        if (empty($rows)) {
            return [];
        }

        // Get table fields
        $fields = array_keys($rows[0]);

        // Organize result into a structured array by primary_key field
        foreach ($rows as $item) {
            foreach ($fields as $field) {
                $result[$item[$primary_key]][$field] = $item[$field];
            }
        }

        // Get the field to sort by
        $order_by = $order_by ? $order_by : (in_array($class_name . '_name', $fields) ? $class_name . '_name' : "");

        // Sort
        if (!empty($order_by)) {
            uasort($result, function ($a, $b) use ($order_by) {
                return $a[$order_by] <=> $b[$order_by];
            });
        }

        return $result;
    }

    public static function get($criteria = null): array
    {
        // Get all corresponding to criteria
        $result = self::get_all($criteria);

        // Return an empty string when nothing matched
        if (empty($result)) {
            return [];
        }

        // Return first
        return reset($result);
    }
}