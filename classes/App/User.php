<?php namespace App;

/**
 * Created by PhpStorm.
 * User: henno
 * Date: 29/10/16
 * Time: 22:24
 */
class User
{
    static function register($userName, $userEmail, $userPassword, $userIsAdmin = 0)
    {
        // Get data to be inserted from function argument list
        $data = get_defined_vars();

        // Hash the password
        $data['userPassword'] = password_hash($userPassword, PASSWORD_DEFAULT);

        // Insert user into database
        $userId = insert('users', $data);

        // Return new user's ID
        return $userId;
    }

    public static function get($criteria = null, $orderBy = null)
    {
        
        $criteria = $criteria ? 'AND ' . implode("AND", $criteria) : '';
        $orderBy = $orderBy ? $orderBy : 'userName';
        return get_all("
            SELECT userId, userName, userEmail, userIsAdmin 
            FROM users
            WHERE userDeleted=0 $criteria 
            ORDER BY $orderBy");
    }

    public static function login($userId)
    {
        Activity::create(ACTIVITY_LOGIN, $userId);
        $_SESSION['userId'] = $userId;
    }

    public static function edit(int $userId, array $data)
    {
        if(!is_numeric($userId) || $userId < 0){
            throw new \Exception('Invalid userId');
        }

        update('users', $data, "userId = $userId");
    }

    public static function delete(int $userId)
    {
        global $db;

        if(!is_numeric($userId) || $userId < 0){
            throw new \Exception('Invalid userId');
        }

        // Attempt to delete user from the database (works if user does not have related records in other tables)
        $result = mysqli_query($db, "DELETE FROM users WHERE userId = $userId");

        // If removing user did not work due to foreign key constraints then mark the user as deleted
        if(!$result){
            update('users', [
                'userDeleted' => 1
            ], "userId=$userId");
        }

    }

}