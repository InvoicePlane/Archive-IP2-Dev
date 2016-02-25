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

/**
 * Generates a new app key
 * @return string
 */
function generate_app_key()
{
    return hash('sha1', time() . microtime());
}

/**
 * Generates a new encryption key
 * @return string
 */
function generate_encryption_key()
{
    return hash('sha1', microtime()) . hash('sha1', time());
}

/**
 * Generates a new API key
 * @return string
 */
function generate_api_key()
{
    return substr(hash('md5', microtime()), 0, 20);
}
