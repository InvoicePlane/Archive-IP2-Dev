<?php
/**
 * @package Helpers
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Checks if the PHP Mailer was configured
 * @return bool
 */
function mailer_configured()
{
    $CI = &get_instance();

    return (($CI->mdl_settings->setting('email_send_method') == 'phpmail') OR
        ($CI->mdl_settings->setting('email_send_method') == 'sendmail') OR
        (($CI->mdl_settings->setting('email_send_method') == 'smtp') AND ($CI->mdl_settings->setting('smtp_server_address')))
    );
}

/**
 * Sends an invoice based on the given variables
 * @param $invoice_id
 * @param $invoice_template
 * @param $from
 * @param $to
 * @param $subject
 * @param $body
 * @param null $cc
 * @param null $bcc
 * @param null $attachments
 * @param $send_pdf
 * @param $send_attachments
 * @return bool
 */
function email_invoice(
    $invoice_id,
    $invoice_template,
    $from,
    $to,
    $subject,
    $body,
    $cc = null,
    $bcc = null,
    $attachments = null,
    $send_pdf,
    $send_attachments
) {
    $CI = &get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('invoice');
    $CI->load->helper('pdf');

    $invoice = null;
    if ($send_pdf) {
        $invoice = generate_invoice_pdf($invoice_id, false, $invoice_template);
    }

    if (!$send_attachments) {
        $attachments = null;
    }

    $db_invoice = $CI->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->get()->row();

    $to = parse_template($db_invoice, $to);
    $message = parse_template($db_invoice, $body);
    $subject = parse_template($db_invoice, $subject);
    $cc = parse_template($db_invoice, $cc);
    $bcc = parse_template($db_invoice, $bcc);
    $from = array(parse_template($db_invoice, $from[0]), parse_template($db_invoice, $from[1]));

    return phpmail_send($from, $to, $subject, $message, $invoice, $cc, $bcc, $attachments);
}

/**
 * Sends a quote based on the given variables
 * @param $quote_id
 * @param $quote_template
 * @param $from
 * @param $to
 * @param $subject
 * @param $body
 * @param null $cc
 * @param null $bcc
 * @param null $attachments
 * @return bool
 */
function email_quote(
    $quote_id,
    $quote_template,
    $from,
    $to,
    $subject,
    $body,
    $cc = null,
    $bcc = null,
    $attachments = null
) {
    $CI = &get_instance();

    $CI->load->helper('mailer/phpmailer');
    $CI->load->helper('template');
    $CI->load->helper('pdf');

    $quote = generate_quote_pdf($quote_id, false, $quote_template);

    $db_quote = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();

    $message = parse_template($db_quote, $body);
    $subject = parse_template($db_quote, $subject);
    $cc = parse_template($db_quote, $cc);
    $bcc = parse_template($db_quote, $bcc);
    $from = array(parse_template($db_quote, $from[0]), parse_template($db_quote, $from[1]));

    return phpmail_send($from, $to, $subject, $message, $quote, $cc, $bcc, $attachments);
}

/**
 * @param $quote_id
 * @param $status string "accepted" or "rejected"
 * @return bool if the email was sent
 */
function email_quote_status($quote_id, $status)
{
    ini_set('display_errors', 'on');
    error_reporting(E_ALL);

    if (!mailer_configured()) {
        return false;
    }

    $CI = &get_instance();
    $CI->load->helper('mailer/phpmailer');

    $quote = $CI->mdl_quotes->where('ip_quotes.quote_id', $quote_id)->get()->row();
    $base_url = base_url('/quotes/view/' . $quote_id);

    $user_email = $quote->user_email;
    $subject = sprintf(lang('quote_status_email_subject'),
        $quote->client_name,
        strtolower(lang($status)),
        $quote->quote_number
    );
    $body = sprintf(nl2br(lang('quote_status_email_body')),
        $quote->client_name,
        strtolower(lang($status)),
        $quote->quote_number,
        '<a href="' . $base_url . '">' . $base_url . '</a>'
    );

    return phpmail_send($user_email, $user_email, $subject, $body);
}
