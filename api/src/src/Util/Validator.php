<?php

namespace App\Util;

class Validator
{
    public function __construct()
    {
    }

    /**
     * Generic link validator
     *
     * @param string $link
     */
    public static function validateLink(string $link): bool
    {
        return !!preg_match("/^(?:http(?:s)?:\/\/)?[a-z0-9]+(?:\.[\w.\-]+)+[\w-.,:;'~\/&#@[\]\(\)\!\?\=\$\*\+]+$/mi", $link);
    }

    /**
     * Generic email validator
     *
     * @param string $email
     */
    public static function validateEmail(string $email): bool
    {
        return false !== \filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Generic password validator
     *
     * @param string $password
     */
    public static function validatePassword(string $password): bool
    {
        return !!preg_match("/^(?=.{6,})(?=[^0-9]*[0-9])(?=[^a-z]*[a-z])(?=[^A-Z]*[A-Z]).*/", $password);
    }

    /**
     * Generic phone number validator
     *
     * @param string $phoneNumbers
     */
    public static function validatePhoneNumber(string $phoneNumbers): bool
    {
        return !!preg_match("/^(?:[+]*[(]{0,1}[\d]{1,4}[)]{0,1})?[-\s\d]{4,}$/mi", $phoneNumbers);
    }

    /**
     * Generic username validator
     *
     * @param string $username
     */
    public static function validateUsername(string $username): bool
    {
        return !!preg_match("/^(?=.{6,}).*/", $username);
    }
}
