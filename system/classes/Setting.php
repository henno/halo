<?php namespace App;

class Setting
{
    static function get($settingName)
    {
        $settingName = addslashes($settingName);
        return get_one("SELECT settingValue from settings WHERE settingName = '$settingName'");
    }

    public static function set(string $settingName, string $settingValue)
    {
        insert('settings', [
            'settingName' => $settingName,
            'settingValue' => $settingValue], true);
    }
}