<?php

$cfg['DATABASE_HOSTNAME'] = '127.0.0.1';
$cfg['DATABASE_USERNAME'] = 'root';
$cfg['DATABASE_PASSWORD'] = 'root';
$cfg['DATABASE_DATABASE'] = 'test';


require 'system/database.php';


$stmt = $db->prepare("SELECT
                                  products.id       product_id,
                                  orders.created_at created_at,
                                  products.id       products___product_id__id,
                                  products.name     products___product_id__name,
                                  categories.id     products___product_id__category__id,
                                  categories.name   products___product_id__category__name
                                FROM order_rows
                                  LEFT JOIN orders ON (orders.id = order_id)
                                  LEFT JOIN products ON (products.id = product_id)
                                  LEFT JOIN categories ON (categories.id = products.category_id)");
$stmt->execute();

$results = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    foreach ($row as $field_usv => $value) {

        $fields = explode('__', $field_usv);

        $pointer = &$results;

        // Build path (keys) to value
        for ($i = 0; $i < sizeof($fields); $i++) {


            // Dynamic key names
            if (substr($fields[$i], 0, 1) == '_') {

                $field_name = $row[ltrim($fields[$i], '_')];

            } else {

                $field_name = $fields[$i];

            }

            // Create empty array
            if (!isset($pointer[$field_name]) && sizeof($fields) - 1 > $i){

                $pointer[$field_name] = array();



            }

            // Point $results to sub-array
            $pointer =& $pointer[$field_name];


        } // for

        // Assign value
        $pointer = $value;


    }
}

var_dump($results);