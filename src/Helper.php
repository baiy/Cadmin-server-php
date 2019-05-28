<?php

namespace Baiy\Admin;

class Helper
{
    public static function checkPassword($inputPassword, $hashPassword)
    {
        return password_verify($inputPassword, $hashPassword);
    }

    public static function createPassword($inputPassword)
    {
        return password_hash($inputPassword, PASSWORD_DEFAULT);
    }
}