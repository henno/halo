<?php namespace App;

class modules extends Controller
{

    function index()
    {
        $this->modules = Db::getAll("SELECT * FROM modules");
    }

    function view()
    {
        $moduleId = $this->getId();
        $this->module = Db::getFirst("SELECT * FROM modules WHERE moduleId = '{$moduleId}'");
    }

}
