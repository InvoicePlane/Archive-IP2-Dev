<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Guest
 * @package Modules\Guest\Controllers
 * @property Layout $layout
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Quotes $mdl_quotes
 */
class Guest extends Guest_Controller
{
    /**
     * Returns the guest dashboard that displays his quotes and invoices
     */
    public function index()
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('invoices/mdl_invoices');

        $this->layout->set(
            array(
                'open_quotes' => $this->mdl_quotes->is_open()->where_in('quotes.client_id',
                    $this->user_clients)->get()->result(),
                'overdue_invoices' => $this->mdl_invoices->is_overdue()->where_in('invoices.client_id',
                    $this->user_clients)->get()->result(),
                'open_invoices' => $this->mdl_invoices->is_open()->where_in('invoices.client_id',
                    $this->user_clients)->get()->result(),
            )
        );

        $this->layout->buffer('content', 'guest/index');
        $this->layout->render('layout_guest');
    }
}
