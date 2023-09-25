<?php namespace App;

class activities extends Controller
{
    private static $activities = [

        'login' => 1,
        'logout' => 2,
    ];

    function index()
    {
        $this->activities = Db::getAll("SELECT * FROM activities");
    }

    function view()
    {
        $activityId = $this->getId();
        $this->activity = Db::getFirst("SELECT * FROM activities WHERE activityId = '{$activityId}'");
    }

}