<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Formats an amount based on the format set in the settings with the currency
 *
 * @param $amount
 * @param bool $is_quantity
 * @return string
 */
function format_currency($amount, $is_quantity = false)
{
    $CI =& get_instance();

    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    if ($is_quantity) {
        $decimal_places = $CI->mdl_settings->setting('quantity_decimal_places');
    } else {
        $decimal_places = $CI->mdl_settings->setting('amount_decimal_places');
    }

    $number = number_format($amount, $decimal_places, $decimal_point, $thousands_separator);

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . $number;
    } elseif ($currency_symbol_placement == 'afterspace') {
        return $number . '&nbsp;' . $currency_symbol;
    } else {
        return $number . $currency_symbol;
    }
}

/**
 * Formats an amount based on the format set in the settings
 *
 * @param float $amount
 * @param bool $is_quantity
 * @return null|string
 */
function format_amount($amount, $is_quantity = false)
{
    $CI =& get_instance();

    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    if ($is_quantity) {
        $decimal_places = $CI->mdl_settings->setting('quantity_decimal_places');
    } else {
        $decimal_places = $CI->mdl_settings->setting('amount_decimal_places');
    }

    return number_format($amount, $decimal_places, $decimal_point, $thousands_separator);
}

/**
 * Standardized an amount based on the set number format
 *
 * @param $amount
 * @return mixed
 */
function standardize_amount($amount)
{
    $CI =& get_instance();
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}
