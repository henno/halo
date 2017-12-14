<?php namespace App;

class welcome extends Controller
{

    /**
     * This is a normal action which will be called when user visits /welcome/index URL. Since index is the default
     * action name, it may be omitted (URL can be /welcome). Since welcome is by default the default controller, it may
     * be omitted (URL can be /). After calling the action, a view from views/controller-name/controller-name_action-name.php
     * is loaded (it must exist, unless the function ends with stop() call.
     */
    function index()
    {

        $this->users = get_all("SELECT * FROM users");
    }

    /**
     * This function will only be ran in case of an AJAX request. No view will be attempted to load after this function.
     */
    function AJAX_success()
    {


        stop(201,'Everything is awesome');
    }

    /**
     * This function will only be ran in case of an AJAX request. No view will be attempted to load after this function.
     */
    function AJAX_error()
    {

        // Test sending emails
        Mail::send(DEVELOPER_EMAIL, 'test', 'test');

        echo "This text comes from the server and will be shown only in development environment for debugging purposes. ";
        echo "Here is a nice exception for you to debug:";

        // Generate error for testing
        throw new \Exception('This is a test');


    }

    /**
     * This function will only be ran in case of POST request
     */
    function POST_index()
    {
        echo "\$_POST:<br>";
        var_dump($_POST);
    }
}