<?php namespace Halo;


/**
 * Class auth authenticates user and permits to check if the user has been logged in
 * Automatically loaded when the controller has $requires_auth property.
 */
class Auth
{

    public $logged_in = FALSE;

    function __construct()
    {
        if (isset($_SESSION['user_id'])) {
            $this->logged_in = TRUE;
            $user = get_first("SELECT *
                               FROM users
                               WHERE user_id = '{$_SESSION['user_id']}'");
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
        if (!(isset($_POST['email']) && isset($_POST['password']))) {

            $this->show_login();

        }


        // Prevent SQL injection
        $email = mysqli_escape_string($db, $_POST['email']);


        // Attempt to retrieve user data from database
        $user = get_first("SELECT * 
                           FROM users
                           WHERE email = '$email'
                           AND deleted = 0");


        // No such user or wrong password
        if (empty($user['user_id']) || !password_verify($_POST['password'], $user['password'])) {
            $this->show_login([__("Wrong username or password")]);
        }


        // User has provided correct login data if we are here
        $_SESSION['user_id'] = $user['user_id'];


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
