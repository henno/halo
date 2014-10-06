<?php

class modules extends Controller
{

    function index()
    {
        $this->modules = get_all("SELECT * FROM module");
    }

    function view()
    {
        $module_id = $this->params[0];
        $this->module = get_all("SELECT * FROM module WHERE module_id = '{$module_id}'");
    }

    function edit()
    {
        $module_id = $this->params[0];
        $this->module = get_all("SELECT * FROM module WHERE module_id = '{$module_id}'");
    }

    function edit_post()
    {
        $data = $_POST['data'];
        insert('module', $data);
    }

}