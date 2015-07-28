<?php

class welcome extends Controller
{

    function index()
    {
        $this->users = get_all("SELECT * FROM user");
    }

    function AJAX_index()
    {
        echo "\$_POST:<br>";
        var_dump($_POST);
    }

    function POST_index()
    {
        echo "\$_POST:<br>";
        var_dump($_POST);
    }
}