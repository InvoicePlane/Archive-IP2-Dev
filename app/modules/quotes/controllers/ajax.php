<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Quotes_Ajax
 * @package Modules\Quotes\Controllers
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Clients $mdl_clients
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Invoice_Groups $mdl_invoice_groups
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 * @property Mdl_Items $mdl_items
 * @property Mdl_Quotes $mdl_quotes
 * @property Mdl_Quote_Amounts mdl_quote_amounts
 * @property Mdl_Quote_Custom mdl_quote_custom
 * @property Mdl_Quote_Item_Amounts $mdl_quote_item_amounts
 * @property Mdl_Quote_Items mdl_quote_items
 * @property Mdl_Quote_Tax_Rates mdl_quote_tax_rates
 * @property Mdl_Tax_Rates $mdl_tax_rates
 */
class Quotes_Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Saves a quote and returns the result
     * @uses $_POST['quote_id']
     * @uses $_POST['items']
     * @uses $_POST['quote_discount_amount']
     * @uses $_POST['quote_discount_percent']
     * @uses $_POST['quote_number']
     * @uses $_POST['quote_date_created']
     * @uses $_POST['quote_date_expires']
     * @uses $_POST['quote_status_id']
     * @uses $_POST['quote_password']
     * @uses $_POST['notes']
     * @uses $_POST['custom']
     * @see Mdl_Quotes::validation_rules()
     * @see Mdl_Quote_Items::validation_rules()
     */
    public function save()
    {
        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quotes');
        $this->load->library('encrypt');

        $quote_id = $this->input->post('quote_id');

        $this->mdl_quotes->set_id($quote_id);

        if ($this->mdl_quotes->run_validation('validation_rules_save_quote')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                if ($item->name) {
                    $item->quantity = standardize_amount($item->quantity);
                    $item->price = standardize_amount($item->price);
                    $item->discount_amount = standardize_amount($item->discount_amount);

                    $item_id = ($item->id) ?: null;

                    $this->mdl_quote_items->save($quote_id, $item_id, $item);
                }
            }

            if ($this->input->post('discount_amount') === '') {
                $discount_amount = floatval(0);
            } else {
                $discount_amount = $this->input->post('discount_amount');
            }

            if ($this->input->post('discount_percent') === '') {
                $discount_percent = floatval(0);
            } else {
                $discount_percent = $this->input->post('discount_percent');
            }

            $db_array = array(
                'quote_number' => $this->input->post('quote_number'),
                'date_created' => date_to_mysql($this->input->post('date_created')),
                'date_expires' => date_to_mysql($this->input->post('date_expires')),
                'status_id' => $this->input->post('status_id'),
                'password' => $this->input->post('password'),
                'notes' => $this->input->post('notes'),
                'discount_amount' => $discount_amount,
                'discount_percent' => $discount_percent,
            );

            $this->mdl_quotes->save($quote_id, $db_array);

            // Recalculate for discounts
            $this->load->model('quotes/mdl_quote_amounts');
            $this->mdl_quote_amounts->calculate($quote_id);

            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        if ($this->input->post('custom')) {
            $db_array = array();

            foreach ($this->input->post('custom') as $custom) {
                $db_array[str_replace(']', '', str_replace('custom[', '', $custom['name']))] = $custom['value'];
            }

            $this->load->model('custom_fields/mdl_quote_custom');
            $this->mdl_quote_custom->save_custom($quote_id, $db_array);
        }

        echo json_encode($response);
    }

    /**
     * Saves the tax rate for a quote and returns the redirect
     * @see Mdl_Quote_Tax_Rates::validation_rules()
     */
    public function save_quote_tax_rate()
    {
        $this->load->model('quotes/mdl_quote_tax_rates');

        if ($this->mdl_quote_tax_rates->run_validation()) {
            $this->mdl_quote_tax_rates->save($this->input->post('quote_id'));

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_quote_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    /**
     * Creates a new quote to the database that can be used to store items
     * @see Mdl_Quotes::validation_rules()
     */
    public function create()
    {
        $this->load->model('quotes/mdl_quotes');

        if ($this->mdl_quotes->run_validation()) {
            $quote_id = $this->mdl_quotes->create();

            $response = array(
                'success' => 1,
                'quote_id' => $quote_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    /**
     * Returns the modal that can be used to change the client for a quote
     * @uses $_POST['client_name']
     * @uses $_POST['quote_id']
     */
    public function modal_change_client()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'name' => $this->input->post('name'),
            'quote_id' => $this->input->post('quote_id'),
            'clients' => $this->mdl_clients->get()->result(),
        );

        $this->layout->load_view('quotes/modal_change_client', $data);
    }

    /**
     * Changes the cleint for a quote
     * @uses $_POST['client_name']
     * @uses $_POST['quote_id']
     */
    public function change_client()
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $name = $this->input->post('name');
        $client = $this->mdl_clients->where('name', $this->db->escape_str($name))
            ->get()->row();

        if (!empty($client)) {
            $client_id = $client->id;
            $quote_id = $this->input->post('quote_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('quote_id', $quote_id);
            $this->db->update('quotes', $db_array);

            $response = array(
                'success' => 1,
                'quote_id' => $quote_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    /**
     * Get an item by its ID
     * @uses $_POST['item_id']
     */
    public function get_item()
    {
        $this->load->model('quotes/mdl_quote_items');

        $item = $this->mdl_quote_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    /**
     * Returns the modal that can be used to create a quote
     */
    public function modal_create_quote()
    {
        $this->load->module('layout');

        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'client_name' => $this->input->post('client_name'),
            'clients' => $this->mdl_clients->get()->result(),
        );

        $this->layout->load_view('quotes/modal_create_quote', $data);
    }

    /**
     * Returns the modal that can be used to copy a quote
     */
    public function modal_copy_quote()
    {
        $this->load->module('layout');

        $this->load->model('quotes/mdl_quotes');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'quote_id' => $this->input->post('quote_id'),
            'quote' => $this->mdl_quotes->where('quotes.id', $this->input->post('quote_id'))->get()->row()
        );

        $this->layout->load_view('quotes/modal_copy_quote', $data);
    }

    /**
     * Creates a duplicate / copy of a given quote and returns the result
     * @uses $_POST['quote_id']
     */
    public function copy_quote()
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');

        if ($this->mdl_quotes->run_validation()) {
            $target_id = $this->mdl_quotes->save();
            $source_id = $this->input->post('quote_id');

            $this->mdl_quotes->copy_quote($source_id, $target_id);

            $response = array(
                'success' => 1,
                'quote_id' => $target_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    /**
     * Returns the modal that can be used to create an invoice from a quote 
     * @param $quote_id
     */
    public function modal_quote_to_invoice($quote_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('quotes/mdl_quotes');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'quote_id' => $quote_id,
            'quote' => $this->mdl_quotes->where('quotes.id', $quote_id)->get()->row()
        );

        $this->load->view('quotes/modal_quote_to_invoice', $data);
    }

    /**
     * Creates an invoice based on a quote and returns the resul
     * @uses $_POST['quote_id']
     */
    public function quote_to_invoice()
    {
        $this->load->model(
            array(
                'invoices/mdl_invoices',
                'invoices/mdl_items',
                'quotes/mdl_quotes',
                'quotes/mdl_quote_items',
                'invoices/mdl_invoice_tax_rates',
                'quotes/mdl_quote_tax_rates'
            )
        );

        if ($this->mdl_invoices->run_validation()) {
            $invoice_id = $this->mdl_invoices->create(null, false);

            $this->db->where('id', $this->input->post('quote_id'));
            $this->db->set('invoice_id', $invoice_id);
            $this->db->update('quotes');

            $quote_items = $this->mdl_quote_items->where('quote_id', $this->input->post('quote_id'))->get()->result();

            foreach ($quote_items as $quote_item) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => $quote_item->tax_rate_id,
                    'name' => $quote_item->name,
                    'description' => $quote_item->description,
                    'quantity' => $quote_item->quantity,
                    'price' => $quote_item->price,
                    'item_order' => $quote_item->item_order
                );

                $this->mdl_items->save($invoice_id, null, $db_array);
            }

            $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id',
                $this->input->post('quote_id'))->get()->result();

            foreach ($quote_tax_rates as $quote_tax_rate) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => $quote_tax_rate->tax_rate_id,
                    'include_item_tax' => $quote_tax_rate->include_item_tax,
                    'amount' => $quote_tax_rate->amount
                );

                $this->mdl_invoice_tax_rates->save($invoice_id, null, $db_array);
            }

            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }
}
