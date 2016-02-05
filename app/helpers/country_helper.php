<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Returns an array list of cldr => country, translated in the language $cldr.
 * If there is no translated country list, return the english one
 * @param $cldr
 * @return string
 */
function get_country_list($cldr)
{
    $cl_path = FCPATH . 'vendor/umpirsky/country-list/data/';
    if (file_exists($cl_path . $cldr . '/country.php')) {
        return (include $cl_path . $cldr . '/country.php');
    } else {
        return (include $cl_path . '/en/country.php');
    }

}

/**
 * Returns the countryname of a given $countrycode, translated in the language $cldr
 * @param string $cldr
 * @param string $countrycode
 * @return string
 */
function get_country_name($cldr, $countrycode)
{
    $countries = get_country_list($cldr);
    return (isset($countries[$countrycode]) ? $countries[$countrycode] : $countrycode);
}
