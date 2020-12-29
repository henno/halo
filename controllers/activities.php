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
        $activityId = $this->getId();
        $this->activity = get_first("SELECT * FROM activities WHERE activityId = '{$activityId}'");
    }

    function AJAX_create()
    {
        $activityId = (int)self::$activities[$this->params[0]];

        // Validate activity
        if ($activityId == 0) {
            stop('Invalid activity');
        }

        // Only log video cursor position change once per minute
        if ($activityId == ACTIVITY_VIDEO_PROGRESS || $activityId == ACTIVITY_VIDEO_TIMEUPDATE) {
            $time = Activity::getUserLatestActivityTime($this->auth->userId, $activityId);
            if (substr($time, 0, 16) == date('Y-m-d H:i')) {
                return;
            }
        }

        Activity::create(self::$activities[$this->params[0]]);
    }

}