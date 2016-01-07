<?php
/**
 * @package Helpers
 */

/**
 * Redirects the user to a given URL
 * @param $fallback_url_string
 * @param bool $redirect
 * @return mixed
 */
function redirect_to($fallback_url_string, $redirect = TRUE)
{
    $CI = &get_instance();

    $redirect_url = ($CI->session->userdata('redirect_to')) ? $CI->session->userdata('redirect_to') : $fallback_url_string;

    if ($redirect) {
        redirect($redirect_url);
    }
    return $redirect_url;
}

/**
 *
 */
function redirect_to_set()
{
    $CI = &get_instance();
    $CI->session->set_userdata('redirect_to', $CI->uri->uri_string());
}
