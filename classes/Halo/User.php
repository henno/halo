<?php

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
}