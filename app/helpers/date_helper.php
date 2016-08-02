<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Returns an array with all available date formats
 * @return array
 */
function date_formats()
{
    return array(
        'm/d/Y' => array(
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yyyy'
        ),
        'm-d-Y' => array(
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yyyy'
        ),
        'm.d.Y' => array(
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yyyy'
        ),
        'Y/m/d' => array(
            'setting' => 'Y/m/d',
            'datepicker' => 'yyyy/mm/dd'
        ),
        'Y-m-d' => array(
            'setting' => 'Y-m-d',
            'datepicker' => 'yyyy-mm-dd'
        ),
        'Y.m.d' => array(
            'setting' => 'Y.m.d',
            'datepicker' => 'yyyy.mm.dd'
        ),
        'd/m/Y' => array(
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yyyy'
        ),
        'd-m-Y' => array(
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yyyy'
        ),
        'd-M-Y' => array(
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yyyy'
        ),
        'd.m.Y' => array(
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yyyy'
        ),
        'j.n.Y' => array(
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yyyy'
        )
    );
}

/**
 * Converts a given MySQL date to a formatted date
 * @param $date
 * @param bool $ignore_post_check
 * @return DateTime|string
 */
function date_from_mysql($date, $ignore_post_check = false)
{
    $CI = &get_instance();
    return date($CI->mdl_settings->setting('date_format'), strtotime($date));

    if ($date <> '0000-00-00') {
        if (!$_POST or $ignore_post_check) {
            $CI = &get_instance();

            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format($CI->mdl_settings->setting('date_format'));
        }
        return $date;
    }
    return '';
}

/**
 * Converts a given MySQL date to a formatted date
 *
 * @TODO H:i:s needs proper setting value
 *
 * @param $date
 * @param bool $ignore_post_check
 * @return DateTime|string
 */
function datetime_from_mysql($date, $ignore_post_check = false)
{
    if ($date <> '0000-00-00 00:00:00') {
        if (!$_POST || $ignore_post_check) {
            $CI = &get_instance();

            $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
            return $date->format($CI->mdl_settings->setting('date_format') . ' H:i');
        }
        return $date;
    }
    return '';
}

/**
 * Converts a given tmestamp date to a formatted date
 * @param $timestamp
 * @return string
 */
function date_from_timestamp($timestamp)
{
    $CI = &get_instance();

    $date = new DateTime();
    $date->setTimestamp($timestamp);
    return $date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * Converts a given date date to a MySQL date
 * @param $date
 * @return string
 */
function date_to_mysql($date)
{
    return date('Y-m-d H:i:s', strtotime($date));
}

/**
 * Returns the date format set by the user in the settings
 * @return string
 */
function date_format_setting()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['setting'];
}

/**
 * Returns the date format set by the user in the settings for the datepicker
 * @return string
 */
function date_format_datepicker()
{
    $CI = &get_instance();

    $date_format = $CI->mdl_settings->setting('date_format');

    $date_formats = date_formats();

    return $date_formats[$date_format]['datepicker'];
}

/**
 * Adds interval to user formatted date and returns user formatted date
 * To be used when date is being output back to user
 * @param $date - user formatted date
 * @param $increment - interval (1D, 2M, 1Y, etc)
 * @return DateTime
 */
function increment_user_date($date, $increment)
{
    $CI = &get_instance();

    $mysql_date = date_to_mysql($date);

    $new_date = new DateTime($mysql_date);
    $new_date->add(new DateInterval('P' . $increment));

    return $new_date->format($CI->mdl_settings->setting('date_format'));
}

/**
 * Adds interval to yyyy-mm-dd date and returns in same format
 * @param $date
 * @param $increment
 * @return DateTime
 */
function increment_date($date, $increment)
{
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');
}
