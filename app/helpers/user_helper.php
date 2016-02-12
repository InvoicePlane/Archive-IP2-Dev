<?php
/**
 * @package Helpers
 */

/**
 * Checks if the logged in user is either a system admin or has
 * the specified permission
 * 
 * @param string $permission_name
 * @return bool
 */
function check_permission($permission_name = '')
{
    // Get the user permissions
    $CI = &get_instance();
    $user_permissions = $CI->session->get_userdata();
    
    if (!isset($user_permissions['user']['permissions'])) {
        return false;
    }
    
    $permissions = $user_permissions['user']['permissions'];
    
    // Check if the user is system admin or has the specified permission
    if (in_array('all', $permissions) || in_array($permission_name, $permissions)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if the logged in user is a client
 *
 * @return mixed
 */
function user_logged_in()
{
    $CI = &get_instance();

    // Check if there is user data stored
    if (!isset($CI->session->user['app_key'])) {
        return false;
    }
    
    // Check if the instance key matches
    if (!defined('APP_KEY') || $CI->session->user['app_key'] != APP_KEY) {
        return false;
    }

    return true;
}

/**
 * Checks if the logged in user is a client
 *
 * @return mixed
 */
function user_is_client()
{
    $CI = &get_instance();

    if (isset($CI->session->user['is_client'])) {
        return ($CI->session->user['is_client'] == 1 ? true : false);
    }

    return false;
}

/**
 * Checks if the logged in user is an admin
 *
 * @return mixed
 */
function user_is_admin()
{
    $CI = &get_instance();

    if (isset($CI->session->user['permissions']) && is_array($CI->session->user['permissions'])) {
        return (in_array('all', $CI->session->user['permissions']) ? true : false);
    }

    return false;
}
