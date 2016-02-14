<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Add an alert that will be displayed on the next page
 * 
 * @param $type
 * @param $message
 */
function set_alert($type, $message)
{
    $CI = &get_instance();
    
    // check if there are already alerts
    if (isset($CI->session->alerts)) {
        $alerts = $CI->session->alerts;
    } else {
        $alerts = array();
    }
    
    // Add the new alert
    array_push($alerts, array(
        'type' => $type,
        'message' => $message,
    ));

    // Save the alerts
    $CI->session->set_userdata('alerts', $alerts);
}

/**
 * Returns false if no arrays were found or the array of alerts
 * @return bool|array
 */
function get_alerts()
{
    $CI = &get_instance();

    if (count($CI->session->alerts) > 0) {
        return $CI->session->alerts;
    }

    return false;
}

/**
 * Deletes all saved alerts
 */
function clear_alerts()
{
    $CI = &get_instance();
    $CI->session->unset_userdata('alerts');
}