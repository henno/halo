<?php namespace App;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Created by PhpStorm.
 * User: henno
 * Date: 29/10/16
 * Time: 22:24
 */
class User
{
    static function register($email, $password, $is_admin = false)
    {


        // Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);


        // Insert user into database
        $user_id = insert('users', ['email' => $email, 'password' => $password]);


        // Return new user's ID
        return $user_id;
    }

    public static function get($criteria)
    {
        $criteria = $criteria ? 'AND ' . implode("AND", $criteria) : '';
        return get_all("
            SELECT * 
            FROM users
            WHERE deleted=0 $criteria 
            ORDER BY name");
    }

    public static function import($filename, $filename_tmp)
    {
        $existing_users = [];
        $ext = pathinfo($filename)['extension'];
        if ($ext != "xlsx") {
            echo "Error: Please Upload only CSV File";
        }
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $worksheets = $reader->listWorksheetInfo($filename_tmp);

        foreach ($worksheets as $worksheet) {

            $sheetName = $worksheet['worksheetName'];
            $reader->setLoadSheetsOnly($sheetName);
            $spreadsheet = $reader->load($filename_tmp);
            $worksheet = $spreadsheet->getActiveSheet();
            $names = $worksheet->toArray();
            if ($names[0][0] != 'First name') {
                stop(400, __('Invalid .xlsx file'));
            }
            foreach (array_slice($names, 1) as $name) {
                $name = "$name[0] $name[1]";
                if (empty($name)) {
                    continue;
                }
                insert('users', [
                    'name' => $name,
                ]);
            }

            // Skip the rest of the sheets
            break;
        }
        return $existing_users;
    }

    public static function login($user_id)
    {
        Activity::create(ACTIVITY_LOGIN, $user_id);
        $_SESSION['user_id'] = $user_id;
    }
}