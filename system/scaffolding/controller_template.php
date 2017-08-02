<?php namespace App;

class modules extends Controller
{

    function index()
    {
        $this->modules = get_all("SELECT * FROM modules");
    }

    function view()
    {
        $module_id = $this->getId();
        $this->module = get_first("SELECT * FROM modules WHERE module_id = '{$module_id}'");
    }

}