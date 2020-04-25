<?php namespace App;


class Activity
{

    public static function create($activity_id, $user_id = 0)
    {
        // Use the currently logged in user's ID when not supplied
        $user_id = $user_id ? $user_id : $_SESSION['user_id'];

        // Insert the activity into DB
        insert('activity_log', [
            'user_id' => $user_id,
            'activity_id' => $activity_id,
            'activity_log_timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public static function getUserLatestActivityTime($user_id, $activity_id)
    {
        $user_id = (int)$user_id;
        $activity_id = (int)$activity_id;
        return get_one("SELECT MAX(activity_log_timestamp) FROM activity_log WHERE user_id = $user_id and activity_id = $activity_id ORDER BY activity_log_id");
    }

    public static function logs($criteria)
    {
        $criteria = $criteria ? 'WHERE ' . implode("AND", $criteria) : '';
        return get_all("
            SELECT *, DATE_FORMAT(activity_log_timestamp, '%Y-%m-%d %H:%i') activity_log_timestamp 
            FROM activity_log JOIN users USING (user_id) JOIN activities USING (activity_id)
            WHERE $criteria");
    }

}