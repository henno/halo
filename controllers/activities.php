<?php namespace App;

class activities extends Controller
{
    private static $activities = [

        'abort' => 1,
        'canplay' => 2,
        'canplaythrough' => 3,
        'durationchange' => 4,
        'emptied' => 5,
        'ended' => 6,
        'error' => 7,
        'loadeddata' => 8,
        'loadedmetadata' => 9,
        'loadstart' => 10,
        'pause' => 11,
        'play' => 12,
        'playing' => 13,
        'progress' => 14,
        'ratechange' => 15,
        'seeked' => 16,
        'seeking' => 17,
        'stalled' => 18,
        'suspend' => 19,
        'timeupdate' => 20,
        'volumechange' => 21,
        'waiting' => 22,
        'login' => 99,
        'leave' => 100,
        'logout' => 101,
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