<?php namespace App;

class activities extends Controller
{
    private static $activities = [

        'login' => 1,
        'logout' => 2,
    ];

    function index()
    {
        $this->activities = get_all("SELECT * FROM activities");
    }

    function view()
    {
        $activity_id = $this->getId();
        $this->activity = get_first("SELECT * FROM activities WHERE activity_id = '{$activity_id}'");
    }

    function AJAX_create()
    {
        $activity_id = (int)self::$activities[$this->params[0]];

        // Validate activity
        if ($activity_id == 0) {
            stop('Invalid activity');
        }

        // Only log video cursor position change once per minute
        if ($activity_id == ACTIVITY_VIDEO_PROGRESS || $activity_id == ACTIVITY_VIDEO_TIMEUPDATE) {
            $time = Activity::getUserLatestActivityTime($this->auth->user_id, $activity_id);
            if (substr($time, 0, 16) == date('Y-m-d H:i')) {
                return;
            }
        }

        Activity::create(self::$activities[$this->params[0]]);
    }

}