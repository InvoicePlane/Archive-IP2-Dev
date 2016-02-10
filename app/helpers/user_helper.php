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
function user_is_client() {
    $CI = &get_instance();
    $userdata = $CI->session->userdata();
    
    if (isset($userdata['user']['is_client'])) {
        return ($userdata['user']['is_client'] == 1 ? true : false);
    }
    
    return false;
}
