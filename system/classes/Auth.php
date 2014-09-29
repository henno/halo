<?php
/**
 * Class auth authenticates user and permits to check if the user has been logged in
 * Automatically loaded when the controller has $requires_auth property.
 */
class Auth
{

    public $logged_in = FALSE;
    public $is_admin = FALSE;

    function __construct()
    {
        if (isset($_SESSION['user_id'])) {
            $this->logged_in = TRUE;
            $user = get_first("SELECT *
                                       FROM user
                                       WHERE user_id = '{$_SESSION['user_id']}'");

            // Dynamically add all user table fields as object properties to auth object
            foreach($user as $user_attr => $value){
                $this->$user_attr = $value;
            }
        }
    }

    /**
     * Verifies if the user is logged in and authenticates if not and POST contains username, else displays the login form
     * @return bool Returns true when the user has been logged in
     */
    function require_auth()
    {
        global $errors;

        // If user has already logged in...
        if ($this->logged_in) {
            return TRUE;
        }

        // Authenticate by POST data
        if (isset($_POST['username'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = get_first("SELECT user_id, is_admin FROM user
                                WHERE username = '$username'
                                  AND password = '$password'
                                  AND  deleted = 0");
            if (! empty($user['user_id'])) {
                $_SESSION['user_id'] = $user['user_id'];
                return true;
            } else {
                $errors[] = "Vale kasutajanimi või parool";
            }
        }

        // Display the login form
        require 'templates/auth_template.php';

        // Prevent loading the requested controller (not authenticated)
        exit();
    }
}
