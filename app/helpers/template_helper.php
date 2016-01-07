<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Parses a template for veriables and returns the parsed content
 * @param $object
 * @param $body
 * @return mixed
 */
function parse_template($object, $body)
{
    if (preg_match_all('/{{{([^{|}]*)}}}/', $body, $template_vars)) {
        foreach ($template_vars[1] as $var) {
            switch ($var) {
                case 'invoice_guest_url':
                    $replace = site_url('guest/view/invoice/' . $object->invoice_url_key);
                    break;
                case 'invoice_date_due':
                    $replace = date_from_mysql($object->invoice_date_due, TRUE);
                    break;
                case 'invoice_date_created':
                    $replace = date_from_mysql($object->invoice_date_created, TRUE);
                    break;
                case 'invoice_total':
                    $replace = format_currency($object->invoice_total);
                    break;
                case 'invoice_paid':
                    $replace = format_currency($object->invoice_paid);
                    break;
                case 'invoice_balance':
                    $replace = format_currency($object->invoice_balance);
                    break;
                case 'quote_total':
                    $replace = format_currency($object->quote_total);
                    break;
                case 'quote_date_created':
                    $replace = date_from_mysql($object->quote_date_created, TRUE);
                    break;
                case 'quote_date_expires':
                    $replace = date_from_mysql($object->quote_date_expires, TRUE);
                    break;
                case 'quote_guest_url':
                    $replace = site_url('guest/view/quote/' . $object->quote_url_key);
                    break;
                default:
                    $replace = $object->{$var};
            }

            $body = str_replace('{{{' . $var . '}}}', $replace, $body);
        }
    }

    return $body;
}

/**
 * Returns the invoice PDF template set in the settings
 * @param $invoice
 * @return mixed
 */
function select_pdf_invoice_template($invoice)
{
    $CI =& get_instance();

    if ($invoice->is_overdue) {
        // Use the overdue template
        return $CI->mdl_settings->setting('pdf_invoice_template_overdue');
    } elseif ($invoice->invoice_status_id == 4) {
        // Use the paid template
        return $CI->mdl_settings->setting('pdf_invoice_template_paid');
    } else {
        // Use the default template
        return $CI->mdl_settings->setting('pdf_invoice_template');
    }
}

/**
 * Returns the invoice email template set in the settings
 * @param $invoice
 * @return mixed
 */
function select_email_invoice_template($invoice)
{
    $CI =& get_instance();

    if ($invoice->is_overdue) {
        // Use the overdue template
        return $CI->mdl_settings->setting('email_invoice_template_overdue');
    } elseif ($invoice->invoice_status_id == 4) {
        // Use the paid template
        return $CI->mdl_settings->setting('email_invoice_template_paid');
    } else {
        // Use the default template
        return $CI->mdl_settings->setting('email_invoice_template');
    }
}
