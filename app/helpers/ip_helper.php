<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Generates a random password
 * @return string
 */
function generate_password_suggestion()
{
    $chars = "abcdefghijklmnpqrstuvwxyzABCDEFGIHJKLMNPQRSTUVWXYZ123456789-_";
    $suggestion = substr(str_shuffle($chars), 0, 12);
    return $suggestion;
}
