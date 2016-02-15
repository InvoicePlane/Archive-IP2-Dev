<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Guest_Payments
 * @package Modules\Guest\Controllers
 * @property Layout $layout
 * @property Mdl_Payments $mdl_payments
 */
class Guest_Payments extends Guest_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('payments/mdl_payments');
    }

    /**
     * Index page, returns all payments that were added for the client
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_payments->where('(payments.invoice_id IN (SELECT id FROM ip_invoices WHERE client_id IN (' . implode(',',
                $this->user_clients) . ')))');
        $this->mdl_payments->paginate(site_url('guest/payments/index'), $page);
        $payments = $this->mdl_payments->result();

        $this->layout->set(
            array(
                'payments' => $payments,
                'filter_display' => true,
                'filter_placeholder' => lang('filter_payments'),
                'filter_method' => 'filter_payments'
            )
        );

        $this->layout->buffer('content', 'guest/payments_index');
        $this->layout->render('layout_guest');
    }
}
