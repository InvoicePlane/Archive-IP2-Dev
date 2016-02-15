<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Invoices
 * @package Modules\Invoices\Controllers
 * @property CI_DB_query_builder $db
 * @property Layout $layout
 * @property Mdl_Custom_Fields $mdl_custom_fields
 * @property Mdl_Invoice_Custom $mdl_invoice_custom
 * @property Mdl_Invoice_Amounts $mdl_invoice_amounts
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Items $mdl_items
 * @property Mdl_Payment_Methods $mdl_payment_methods
 * @property Mdl_Tasks $mdl_tasks
 * @property Mdl_Tax_Rates $mdl_tax_rates
 */
class Invoices extends Admin_Controller
{
    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoices');
    }

    /**
     * Index page, redirects to invoices/status/all
     */
    public function index()
    {
        // Display all invoices by default
        redirect('invoices/status/all');
    }

    /**
     * Returns all invoices based on the given status and page no.
     * Example: 'invoices/status/sent' returns all sent invoices
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'draft':
                $this->mdl_invoices->is_draft();
                break;
            case 'sent':
                $this->mdl_invoices->is_sent();
                break;
            case 'viewed':
                $this->mdl_invoices->is_viewed();
                break;
            case 'paid':
                $this->mdl_invoices->is_paid();
                break;
            case 'overdue':
                $this->mdl_invoices->is_overdue();
                break;
        }

        $this->mdl_invoices->paginate(site_url('invoices/status/' . $status), $page);
        $invoices = $this->mdl_invoices->result();

        $this->layout->set(
            array(
                'invoices' => $invoices,
                'status' => $status,
                'filter_display' => true,
                'filter_placeholder' => lang('filter_invoices'),
                'filter_method' => 'filter_invoices',
                'invoice_statuses' => $this->mdl_invoices->statuses()
            )
        );

        $this->layout->buffer('content', 'invoices/index');
        $this->layout->render();
    }

    /**
     * Displays the invoice archive
     */
    public function archive()
    {
        $invoice_array = array();
        
        if (isset($_POST['invoice_number'])) {
            $invoiceNumber = $_POST['invoice_number'];
            $invoice_array = glob('./uploads/archive/*' . '_' . $invoiceNumber . '.pdf');
            $this->layout->set(
                array(
                    'invoices_archive' => $invoice_array
                ));
            $this->layout->buffer('content', 'invoices/archive');
            $this->layout->render();
        } else {
            foreach (glob('./uploads/archive/*.pdf') as $file) {
                array_push($invoice_array, $file);
            }
            rsort($invoice_array);
            $this->layout->set(
                array(
                    'invoices_archive' => $invoice_array
                ));
            $this->layout->buffer('content', 'invoices/archive');
            $this->layout->render();
        }
    }

    /**
     * Downloads the PDF from the invoice archive
     * @param $invoice
     */
    public function download($invoice)
    {
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename=' . basename($invoice));
        readfile('./uploads/archive/' . urldecode(basename($invoice)));
    }

    /**
     * Returns the details page for an invoice for the given ID
     * @param $invoice_id
     */
    public function view($invoice_id)
    {
        $this->load->model(
            array(
                'mdl_items',
                'tax_rates/mdl_tax_rates',
                'payment_methods/mdl_payment_methods',
                'mdl_invoice_tax_rates',
                'custom_fields/mdl_custom_fields',
            )
        );

        $this->load->module('payments');

        $this->load->model('custom_fields/mdl_invoice_custom');

        $invoice_custom = $this->mdl_invoice_custom->where('invoice_id', $invoice_id)->get();

        if ($invoice_custom->num_rows()) {
            $invoice_custom = $invoice_custom->row();

            unset($invoice_custom->invoice_id, $invoice_custom->id);

            foreach ($invoice_custom as $key => $val) {
                $this->mdl_invoices->set_form_value('custom[' . $key . ']', $val);
            }
        }

        $invoice = $this->mdl_invoices->get_by_id($invoice_id);


        if (!$invoice) {
            show_404();
        }

        $this->layout->set(
            array(
                'invoice' => $invoice,
                'items' => $this->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
                'invoice_id' => $invoice_id,
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'invoice_tax_rates' => $this->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'custom_fields' => $this->mdl_custom_fields->by_table('custom_invoice')->get()->result(),
                'custom_js_vars' => array(
                    'currency_symbol' => $this->mdl_settings->setting('currency_symbol'),
                    'currency_symbol_placement' => $this->mdl_settings->setting('currency_symbol_placement'),
                    'decimal_point' => $this->mdl_settings->setting('decimal_point')
                ),
                'invoice_statuses' => $this->mdl_invoices->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array('modal_delete_invoice', 'invoices/modal_delete_invoice'),
                array('modal_add_invoice_tax', 'invoices/modal_add_invoice_tax'),
                array('modal_add_payment', 'payments/modal_add_payment'),
                array('content', 'invoices/view')
            )
        );

        $this->layout->render();
    }

    /**
     * Deletes an invoice from the database based on the given ID
     * @param $invoice_id
     */
    public function delete($invoice_id)
    {
        // Get the status of the invoice
        $invoice = $this->mdl_invoices->get_by_id($invoice_id);
        $invoice_status = $invoice->invoice_status_id;

        if ($invoice_status == 1 || $this->config->item('enable_invoice_deletion') === true) {
            // If invoice refers to tasks, mark those tasks back to 'Complete'
            $this->load->model('tasks/mdl_tasks');
            $tasks = $this->mdl_tasks->update_on_invoice_delete($invoice_id);

            // Delete the invoice
            $this->mdl_invoices->delete($invoice_id);
        } else {
            // Add alert that invoices can't be deleted
            $this->session->set_flashdata('alert_error', lang('invoice_deletion_forbidden'));
        }

        // Redirect to invoice index
        redirect('invoices/index');
    }

    /**
     * Deletes an invoice item from the database based on the given ID
     * @param $invoice_id
     * @param $item_id
     */
    public function delete_item($invoice_id, $item_id)
    {
        // Delete invoice item
        $this->load->model('mdl_items');
        $item = $this->mdl_items->delete($item_id);

        // Mark item back to complete:
        if ($item && $item->item_task_id) {
            $this->load->model('tasks/mdl_tasks');
            $this->mdl_tasks->update_status(3, $item->item_task_id);
        }

        // Redirect to invoice view
        redirect('invoices/view/' . $invoice_id);
    }

    /**
     * Generates the PDF for an invoice based on the given ID
     * @param $invoice_id
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_pdf($invoice_id, $stream = true, $invoice_template = null)
    {
        $this->load->helper('pdf');

        if ($this->mdl_settings->setting('mark_invoices_sent_pdf') == 1) {
            $this->mdl_invoices->mark_sent($invoice_id);
        }

        generate_invoice_pdf($invoice_id, $stream, $invoice_template);
    }

    /**
     * Removes the invoice tax from an invoice based on the given ID
     * @param $invoice_id
     * @param $invoice_tax_rate_id
     */
    public function delete_invoice_tax($invoice_id, $invoice_tax_rate_id)
    {
        $this->load->model('mdl_invoice_tax_rates');
        $this->mdl_invoice_tax_rates->delete($invoice_tax_rate_id);

        $this->load->model('mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        redirect('invoices/view/' . $invoice_id);
    }

    /**
     * Recalculates the amounts for all invoices
     */
    public function recalculate_all_invoices()
    {
        $this->db->select('invoice_id');
        $invoice_ids = $this->db->get('invoices')->result();

        $this->load->model('mdl_invoice_amounts');

        foreach ($invoice_ids as $invoice_id) {
            $this->mdl_invoice_amounts->calculate($invoice_id->id);
        }
    }
}
