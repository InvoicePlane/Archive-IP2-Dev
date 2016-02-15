<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Payment_Information
 * @package Modules\Guest\Controllers
 * @property Layout $layout
 * @property Mdl_Invoices $mdl_invoices
 */
class Payment_Information extends Guest_Controller
{
    /**
     * Payment_Information constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/mdl_invoices');
    }

    /**
     * Returns the form
     * The form will be filled with the data of the invoice for the given URL key
     * and redirects to the payment_handler controller
     * @param $url_key
     */
    public function form($url_key)
    {
        $disable_form = false;

        // Check if the invoice exists and is billable
        $invoice = $this->mdl_invoices->where('invoices.url_key', $url_key)->where_in('invoices.client_id',
            $this->user_clients)->get()->row();

        if (!$invoice) {
            show_404();
        }

        // Check if the invoice is payable
        if ($invoice->invoice_balance == 0) {
            set_alert('danger', lang('invoice_already_paid'));
            $disable_form = true;
        }

        // Get all payment gateways
        $this->load->model('mdl_settings');
        $omnipay = new \Omnipay\Omnipay();
        $this->config->load('payment_gateways');
        $allowed_drivers = $this->config->item('payment_gateways');
        $gateway_drivers = array_intersect($omnipay->getFactory()->getSupportedGateways(), $allowed_drivers);

        $available_drivers = array();
        foreach ($gateway_drivers as $driver) {

            $d = strtolower($driver);
            $setting = $this->mdl_settings->setting('gateway_' . $d);
            $invoice_payment_method = $invoice->payment_method;
            $driver_payment_method = $this->mdl_settings->setting('gateway_payment_method_' . $d);

            if ($setting == 1) {
                if ($invoice_payment_method == 0 || $driver_payment_method == $invoice_payment_method) {
                    array_push($available_drivers, $driver);
                }
            }
        }

        $this->layout->set(
            array(
                'disable_form' => $disable_form,
                'invoice' => $invoice,
                'gateways' => $available_drivers,
            )
        );

        $this->layout->buffer('content', 'guest/payment_information');
        $this->layout->render('layout_guest');

    }
}
