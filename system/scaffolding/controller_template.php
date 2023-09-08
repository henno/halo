<?php namespace App;

class modules extends Controller
{

    function index()
    {
        $this->modules = Db::getAll("SELECT * FROM modules");
    }

    function view()
    {
        $module_id = $this->getId();
        $this->module = Db::getFirst("SELECT * FROM modules WHERE module_id = '{$module_id}'");
    }

}