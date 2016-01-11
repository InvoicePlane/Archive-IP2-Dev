<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Recurring
 * @package Modules\Invoices\Controllers
 */
class Recurring extends Admin_Controller
{
    /**
     * Recurring constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoices_recurring');
    }

    /**
     * Index page, returns all recurring invoices
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_invoices_recurring->paginate(site_url('invoices/recurring'), $page);
        $recurring_invoices = $this->mdl_invoices_recurring->result();

        $this->layout->set('recur_frequencies', $this->mdl_invoices_recurring->recur_frequencies);
        $this->layout->set('recurring_invoices', $recurring_invoices);
        $this->layout->buffer('content', 'invoices/index_recurring');
        $this->layout->render();
    }

    /**
     * Stops a recurring invoice
     * @param $invoice_recurring_id
     */
    public function stop($invoice_recurring_id)
    {
        $this->mdl_invoices_recurring->stop($invoice_recurring_id);
        redirect('invoices/recurring/index');
    }

    /**
     * Deletes a recurring invoice
     * @param $invoice_recurring_id
     */
    public function delete($invoice_recurring_id)
    {
        $this->mdl_invoices_recurring->delete($invoice_recurring_id);
        redirect('invoices/recurring/index');
    }
}
