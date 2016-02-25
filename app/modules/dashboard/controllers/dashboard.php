<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Dashboard
 * @package Modules\Dashboard\Controllers
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Invoice_Amounts $mdl_invoice_amounts
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Quote_Amounts $mdl_quote_amounts
 * @property Mdl_Quotes $mdl_quotes
 */
class Dashboard extends User_Controller
{
    /**
     * Loads the dashboard
     */
    public function index()
    {
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->load->model('quotes/mdl_quote_amounts');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');

        $quote_overview_period = $this->mdl_settings->setting('quote_overview_period');
        $invoice_overview_period = $this->mdl_settings->setting('invoice_overview_period');

        $this->layout->set(
            array(
                'invoice_status_totals' => $this->mdl_invoice_amounts->get_status_totals($invoice_overview_period),
                'quote_status_totals' => $this->mdl_quote_amounts->get_status_totals($quote_overview_period),
                'invoice_status_period' => str_replace('-', '_', $invoice_overview_period),
                'quote_status_period' => str_replace('-', '_', $quote_overview_period),
                'invoices' => $this->mdl_invoices->limit(10)->get()->result(),
                'quotes' => $this->mdl_quotes->limit(10)->get()->result(),
                'invoice_statuses' => $this->mdl_invoices->statuses(),
                'quote_statuses' => $this->mdl_quotes->statuses(),
                'overdue_invoices' => $this->mdl_invoices->is_overdue()->limit(10)->get()->result()
            )
        );

        $this->layout->buffer('content', 'dashboard/index');
        $this->layout->render();
    }
}
