<?php
/**
 * Created by PhpStorm.
 * User: henno
 * Date: 08/12/2017
 * Time: 22:46
 */

namespace App;


class email extends Controller
{
    function ajax_send_error_report()
    {
        Mail::send(
            DEVELOPER_EMAIL,
            'An error occurred in the project' . PROJECT_NAME,
            '<pre>' . date('Y-m-d H:i:s ') . print_r([
                'SERVER' => [
                    'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                    'HTTP_REFERER' => $_SERVER['HTTP_REFERER'],
                ],
                'POST' => $_POST,
                'SESSION' => $_SESSION,
                'COOKIE' => $_COOKIE,
                'THIS' => get_object_vars($this)
            ], 1) . '</pre>');


        stop(200);
    }

}