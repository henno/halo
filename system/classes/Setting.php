<?php namespace App;

class Setting
{
    static function get($settingName)
    {
        $settingName = addslashes($settingName);
        return Db::getOne("SELECT settingValue from settings WHERE settingName = '$settingName'");
    }

    public static function set(string $settingName, string $settingValue)
    {
        Db::upsert('settings', [
            'settingName' => $settingName,
            'settingValue' => $settingValue], true);
    }
}