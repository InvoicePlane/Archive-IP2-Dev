<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Guest_Invoices
 * @package Modules\Guest\Controllers
 * @property Layout $layout
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 * @property Mdl_Items $mdl_items
 */
class Guest_Invoices extends Guest_Controller
{
    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/mdl_invoices');
    }

    /**
     * Index page, redirects to 'guest/invoices/status/open'
     */
    public function index()
    {
        // Display open invoices by default
        redirect('guest/invoices/status/open');
    }

    /**
     * Returns all invoices based on the given status and page no.
     * Example: 'guest/invoices/status/open' returns all open invoices
     * @param string $status
     * @param int $page
     */
    public function status($status = 'open', $page = 0)
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'paid':
                $this->mdl_invoices->is_paid()->where_in('invoices.client_id', $this->user_clients);
                break;
            default:
                $this->mdl_invoices->is_open()->where_in('invoices.client_id', $this->user_clients);
                break;
        }

        $this->mdl_invoices->paginate(site_url('guest/invoices/status/' . $status), $page);
        $invoices = $this->mdl_invoices->result();

        $this->layout->set(
            array(
                'invoices' => $invoices,
                'status' => $status
            )
        );

        $this->layout->buffer('content', 'guest/invoices_index');
        $this->layout->render('layout_guest');
    }

    /**
     * Returns the view page for the given invoice ID
     * @param $invoice_id
     */
    public function view($invoice_id)
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        $invoice = $this->mdl_invoices->where('invoices.id', $invoice_id)->where_in('invoices.client_id',
            $this->user_clients)->get()->row();

        if (!$invoice) {
            show_404();
        }

        $this->mdl_invoices->mark_viewed($invoice->invoice_id);

        $this->layout->set(
            array(
                'invoice' => $invoice,
                'items' => $this->mdl_items->where('id', $invoice_id)->get()->result(),
                'invoice_tax_rates' => $this->mdl_invoice_tax_rates->where('id', $invoice_id)->get()->result(),
                'invoice_id' => $invoice_id
            )
        );

        $this->layout->buffer(
            array(
                array('content', 'guest/invoices_view')
            )
        );

        $this->layout->render('layout_guest');
    }

    /**
     * Returns the generated PDF of the invoice based on the given ID
     * @param $invoice_id
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_pdf($invoice_id, $stream = true, $invoice_template = null)
    {
        $this->load->helper('pdf');

        $this->mdl_invoices->mark_viewed($invoice_id);

        generate_invoice_pdf($invoice_id, $stream, $invoice_template, 1);
    }
}
