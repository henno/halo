<?php namespace App;

class Deployment
{

    public static function create()
    {
        // Extract commit meta data
        list($sha,$author,$commit_date, $message) = explode(':::', trim(exec('git log --oneline --format=%h:::%cn:::%ci:::%s -n1 HEAD')));

        // Insert new deployment to database
        Db::insert('deployments',[
            'deploymentCommitDate'=>substr($commit_date, 0,19),
            'deploymentDate'=>date('Y-m-d H:i:s'),
            'deploymentCommitMessage'=>$message,
            'deploymentCommitSha'=>$sha,
            'deploymentCommitAuthor'=>$author
        ], true);


    }
}