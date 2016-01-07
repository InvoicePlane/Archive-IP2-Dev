<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function invoice_logo()
{
    $CI = & get_instance();
	
    if ($CI->mdl_settings->setting('invoice_logo'))
    {
        return '<img src="' . base_url() . 'uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
    }
    return '';
}

function invoice_logo_pdf()
{
    $CI = & get_instance();

    if ($CI->mdl_settings->setting('invoice_logo'))
    {
        return '<img src="' . getcwd() . '/uploads/' . $CI->mdl_settings->setting('invoice_logo') . '" id="invoice-logo">';
    }
    return '';
}
