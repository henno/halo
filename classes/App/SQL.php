<?php namespace App;


class SQL
{

    static function getWhere($criteria, $id_field = null): string
    {
        $where = '';
        if (!empty($criteria)) {
            if (is_array($criteria)) {
                $where = "WHERE " . implode(' AND ', escape($criteria));
            } else if (is_numeric($criteria)) {
                $where = "WHERE $id_field = $criteria";
            } else {
                $where = "WHERE $criteria";
            }
        }

        return $where;
    }
}