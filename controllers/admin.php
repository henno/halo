<?php namespace App;

use DateTime;

class admin extends Controller
{
    public $requires_auth = true;
    public $requires_admin = true;
    public $template = 'admin';
    public $selected_county_id;
    public $userId;
    public $userName;

    public function __construct()
    {
        $this->selected_county_id = !isset($_GET['county_id']) || !is_numeric($_GET['county_id'])
            ? (empty($_SESSION['county_id']) ? 0 : $_SESSION['county_id']) : $_GET['county_id'];
        $this->county_id = empty($_POST['county_id']) || !is_numeric($_POST['county_id']) ? false : $_POST['county_id'];
        $this->event_id = empty($_POST['event_id']) || !is_numeric($_POST['event_id']) ? false : $_POST['event_id'];
        $this->userId = empty($_POST['userId']) || !is_numeric($_POST['userId']) ? false : $_POST['userId'];
        $this->userName = empty($_POST['userName']) ? false : $_POST['userName'];
        $this->event_name = empty($_POST['event_name']) ? false : $_POST['event_name'];
        $this->event_start = empty($_POST['event_start']) ? false : $_POST['event_start'];
        $this->event_end = empty($_POST['event_end']) ? false : $_POST['event_end'];
        $_SESSION['county_id']=$this->selected_county_id;
    }

    function index()
    {
        header('Location: ' . BASE_URL . 'admin/users');
        exit();
    }

    function users()
    {
        $this->users = User::get($this->selected_county_id);

    }

    function new_user()
    {
        if (!$this->county_id || !$this->userName) {
            stop(400, __('Invalid argument(s)'));
        }
        if (get_one("SELECT userId FROM users WHERE userDeleted=0 AND userName = '" . addslashes($this->userName) . "'")) {
            stop(409, __('User already exists'));
        }
        insert('users', [
            'county_id' => $this->county_id,
            'name' => $this->userName,
        ]);

        stop(200);
    }

    function delete_user()
    {
        if (!$this->userId) {
            stop(400, __('Invalid argument'));
        }

        if ($this->userId == $this->auth->userId) {
            stop(403, __('You cannot delete yourself'));
        }

        update('users', [
            'deleted' => 1
        ], "userId=$this->userId");

        stop(200);
    }

    function import_users()
    {
        if (empty($this->county_id) || !in_array($this->county_id, County::getValidIds())) {
            stop(400, __('Invalid county_id'));
        }
        $existing_users = User::import(
            $_FILES["xlsxFile"]["name"],
            $_FILES["xlsxFile"]["tmp_name"],
            $this->county_id);
        empty($existing_users) ?
            stop(200) :
            stop(409, __("Some users already existed: ") . implode(', ', $existing_users));
    }


    function logs()
    {
        $this->log = Activity::logs($this->selected_county_id);
    }

}