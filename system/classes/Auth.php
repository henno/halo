<?php namespace App;


/**
 * Class auth authenticates user and permits to check if the user has been logged in
 * Automatically loaded when the controller has $requires_auth property.
 */
class Auth
{

    public $logged_in = FALSE;

    function __construct()
    {
        if (isset($_SESSION['userId'])) {
            $this->logged_in = TRUE;
            $user = get_first("SELECT *
                               FROM users
                               WHERE userId = '{$_SESSION['userId']}'");
            $this->load_user_data($user);

        }
    }

    /**
     * Dynamically add all user table fields as object properties to auth object
     * @param $user
     */
    public
    function load_user_data($user)
    {


        foreach ($user as $user_attr => $value) {
            $this->$user_attr = $value;
        }
        $this->logged_in = TRUE;
    }

    /**
     * Verifies if the user is logged in and authenticates if not and POST contains username, else displays the login form
     * @return bool Returns true when the user has been logged in
     */
    function require_auth()
    {
        global $db;


        // If user has already logged in...
        if ($this->logged_in) {
            return TRUE;
        }


        // Not all credentials were provided
        if (!(isset($_POST['userEmail']) && isset($_POST['userPassword']))) {

            $this->show_login();

        }


        // Prevent SQL injection
        $email = mysqli_escape_string($db, $_POST['userEmail']);


        // Attempt to retrieve user data from database
        $user = get_first("SELECT * 
                           FROM users
                           WHERE userEmail = '$email'
                           AND userDeleted = 0");


        // No such user or wrong password
        if (empty($user['userId']) || !password_verify($_POST['userPassword'], $user['userPassword'])) {
            $this->show_login([__("Wrong username or password")]);
        }


        // User has provided correct login data if we are here
        User::login($user['userId']);


        // Load $this->auth with users table's field values
        $this->load_user_data($user);


        return true;

    }

    /**
     * @param $errors
     */
    protected function show_login($errors = null)
    {
        // Display the login form
        require 'templates/auth_template.php';

        // Prevent loading the requested controller (not authenticated)
        exit();
    }


}
