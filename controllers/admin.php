<?php namespace App;

use DateTime;

class admin extends Controller
{
    public $requires_auth = true;
    public $requires_admin = true;
    public $template = 'admin';

    function index()
    {
        header('Location: ' . BASE_URL . 'admin/users');
        exit();
    }

    function users()
    {
        $this->users = User::get(null, 'userId DESC');
    }

    function logs()
    {
        $this->log = Activity::logs();
    }

    function AJAX_addUser()
    {
        if (empty($_POST['userName'])) {
            stop(400, __('Invalid username'));
        }
        if (empty($_POST['userPassword'])) {
            stop(400, __('Invalid password'));
        }

        $userName = addslashes($_POST['userName']);
        if (User::get(["userName = '$userName'"])) {
            stop(409, __('User already exists'));
        }

        User::register($_POST['userName'], $_POST['userEmail'], $_POST['userPassword']);

        stop(200);
    }

    function AJAX_editUser()
    {
        if (empty($_POST['userId']) || !is_numeric($_POST['userId'])) {
            stop(400, __('Invalid userId'));
        }

        // Remove empty password from $_POST or hash it
        if (empty($_POST['userPassword'])) {
            unset($_POST['userPassword']);
        } else {
            $_POST['userPassword'] = password_hash($_POST['userPassword'], PASSWORD_DEFAULT);
        }

        User::edit($_POST['userId'], $_POST);


        stop(200);
    }

    function AJAX_deleteUser()
    {
        if (empty($_POST['userId']) || !is_numeric($_POST['userId'])) {
            stop(400, __('Invalid userId'));
        }

        if ($_POST['userId'] == $this->auth->userId) {
            stop(403, __('You cannot delete yourself'));
        }

        User::delete($_POST['userId']);

        stop(200);
    }

    function AJAX_getUser()
    {
        if (empty($_POST['userId']) || !is_numeric($_POST['userId'])) {
            stop(400, __('Invalid userId'));
        }

        stop(200, get_first("SELECT userIsAdmin,userEmail,userName FROM users WHERE userId = $_POST[userId]"));
    }

}