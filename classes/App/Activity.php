<?php namespace App;


class Activity
{

    public static function create($activityId, $userId = 0)
    {
        // Use the currently logged in user's ID when not supplied
        $userId = $userId ? $userId : $_SESSION['userId'];

        // Insert the activity into DB
        insert('activityLog', [
            'userId' => $userId,
            'activityId' => $activityId,
            'activityLogTimestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public static function getUserLatestActivityTime($userId, $activityId)
    {
        $userId = (int)$userId;
        $activityId = (int)$activityId;
        return get_one("SELECT MAX(activityLogTimestamp) FROM activityLog WHERE userId = $userId and activityId = $activityId ORDER BY activityLogId");
    }

    public static function logs($criteria = null)
    {
        $where = SQL::getWhere($criteria);
        return get_all("
            SELECT *, DATE_FORMAT(activityLogTimestamp, '%Y-%m-%d %H:%i') activityLogTimestamp 
            FROM activityLog JOIN users USING (userId) JOIN activities USING (activityId)
            $where");
    }

}