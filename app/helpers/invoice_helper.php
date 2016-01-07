<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Returns the invoice logo as HTML string
 * @return string
 */
function invoice_logo()
{
    $CI = & get_instance();

    if ($CI->mdl_settings->setting('invoice_logo'))
    {
        return '<img src="' . base_url() . 'uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
    }
    return '';
}

/**
 * Returns the invoice logo as HTML string for PDF files
 * @return string
 */
function invoice_logo_pdf()
{
    $CI = & get_instance();

    if ($CI->mdl_settings->setting('invoice_logo'))
    {
        return '<img src="' . getcwd() . '/uploads/' . $CI->mdl_settings->setting('invoice_logo') . '" id="invoice-logo">';
    }
    return '';
}
