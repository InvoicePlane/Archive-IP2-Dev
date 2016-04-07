<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Invoices_Ajax
 * @package Modules\Invoices\Controllers
 * @property CI_DB_query_builder $db
 * @property CI_Form_validation $form_validation
 * @property Layout $layout
 * @property Mdl_Clients $mdl_clients
 * @property Mdl_Invoice_Custom $mdl_invoice_custom
 * @property Mdl_Invoice_Amounts $mdl_invoice_amounts
 * @property Mdl_Invoice_Groups $mdl_invoice_groups
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Invoices_Recurring $mdl_invoices_recurring
 * @property Mdl_Items $mdl_items
 * @property Mdl_Tasks $mdl_tasks
 * @property Mdl_Tax_Rates $mdl_tax_rates
 */
class Invoices_Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Saves a note to a given client ID and returns the result
     * @uses $_POST['invoice_id']
     * @uses $_POST['items']
     * @uses $_POST['invoice_status_id']
     * @uses $_POST['invoice_discount_amount']
     * @uses $_POST['invoice_discount_percent']
     * @uses $_POST['invoice_number']
     * @uses $_POST['invoice_terms']
     * @uses $_POST['invoice_date_created']
     * @uses $_POST['invoice_date_due']
     * @uses $_POST['invoice_password']
     * @uses $_POST['payment_method']
     * @uses $_POST['invoice_custom']
     * @see Mdl_Items::validation_rules()
     */
    public function save()
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoices');

        $invoice_id = $this->input->post('invoice_id');

        $this->mdl_invoices->set_id($invoice_id);

        if ($this->mdl_invoices->run_validation('validation_rules_save_invoice')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                // Check if an item has either a quantity + price or name or description
                if (!empty($item->quantity) && !empty($item->price)
                    || !empty($item->name)
                    || !empty($item->description)
                ) {
                    $item->quantity = standardize_amount($item->quantity);
                    $item->price = standardize_amount($item->price);
                    $item->discount_amount = standardize_amount($item->discount_amount);

                    $item_id = ($item->id) ?: null;
                    if (!$item->task_id) {
                        unset($item->task_id);
                    } else {
                        $this->load->model('tasks/mdl_tasks');
                        $this->mdl_tasks->update_status(4, $item->task_id);
                    }
                    $this->mdl_items->save($invoice_id, $item_id, $item);
                } else {
                    // Throw an error message and use the form validation for that
                    $this->load->library('form_validation');
                    $this->form_validation->set_rules('name', lang('item'), 'required');
                    $this->form_validation->set_rules('description', lang('description'), 'required');
                    $this->form_validation->set_rules('quantity', lang('quantity'), 'required');
                    $this->form_validation->set_rules('price', lang('price'), 'required');
                    $this->form_validation->run();

                    $response = array(
                        'success' => 0,
                        'validation_errors' => array(
                            'name' => form_error('name', '', ''),
                            'description' => form_error('description', '', ''),
                            'quantity' => form_error('quantity', '', ''),
                            'price' => form_error('price', '', ''),
                        )
                    );
                    echo json_encode($response);
                    exit;
                }
            }

            $invoice_status = $this->input->post('invoice_status_id');

            if ($this->input->post('invoice_discount_amount') === '') {
                $invoice_discount_amount = floatval(0);
            } else {
                $invoice_discount_amount = $this->input->post('invoice_discount_amount');
            }

            if ($this->input->post('invoice_discount_percent') === '') {
                $invoice_discount_percent = floatval(0);
            } else {
                $invoice_discount_percent = $this->input->post('invoice_discount_percent');
            }

            $db_array = array(
                'status_id' => $invoice_status,
                'payment_method_id' => $this->input->post('payment_method'),
                'invoice_number' => $this->input->post('invoice_number'),
                'date_due' => date_to_mysql($this->input->post('invoice_date_due')),
                'discount_amount' => $invoice_discount_amount,
                'discount_percent' => $invoice_discount_percent,
                'terms' => $this->input->post('invoice_terms'),
                'password' => $this->input->post('invoice_password'),
                'date_created' => date_to_mysql($this->input->post('invoice_date_created')),
            );

            // check if status changed to sent, the feature is enabled and settings is set to sent
            if ($invoice_status == 2 && $this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 'sent') {
                $db_array['is_read_only'] = 1;
            }

            // check if status changed to viewed, the feature is enabled and settings is set to viewed
            if ($invoice_status == 3 && $this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 'viewed') {
                $db_array['is_read_only'] = 1;
            }

            // check if status changed to paid and the feature is enabled
            if ($invoice_status == 4 && $this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 'paid') {
                $db_array['is_read_only'] = 1;
            }

            $this->mdl_invoices->save($invoice_id, $db_array);

            // Recalculate for discounts
            $this->load->model('invoices/mdl_invoice_amounts');
            $this->mdl_invoice_amounts->calculate($invoice_id);

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

            $this->load->model('custom_fields/mdl_invoice_custom');
            $this->mdl_invoice_custom->save_custom($invoice_id, $db_array);
        }

        echo json_encode($response);
    }

    /**
     * Saves the tax rate for an invoice and returns the result
     * @uses $_POST['invoice_id']
     * @uses $_POST['tax_rate_name']
     * @uses $_POST['tax_rate_percent']
     */
    public function save_invoice_tax_rate()
    {
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoice_tax_rates->run_validation()) {
            $this->mdl_invoice_tax_rates->save($this->input->post('invoice_id'));

            $response = array(
                'success' => 1
            );
        } else {
            $response = array(
                'success' => 0,
                'validation_errors' => $this->mdl_invoice_tax_rates->validation_errors
            );
        }

        echo json_encode($response);
    }

    /**
     * Creates a new invoice to the database that can be used to store items
     * @uses $_POST['client_name']
     * @uses $_POST['invoice_date_created']
     * @uses $_POST['invoice_time_created']
     * @uses $_POST['invoice_group_id']
     * @uses $_POST['invoice_password']
     * @uses $_POST['user_id']
     * @uses $_POST['payment_method']
     * @see Mdl_Invoices::validation_rules()
     */
    public function create()
    {
        $this->load->model('invoices/mdl_invoices');

        if ($this->mdl_invoices->run_validation()) {
            $invoice_id = $this->mdl_invoices->create();

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

    /**
     * Creates a recurring invoice for an invoice and returns the result
     * @uses $_POST['invoice_id']
     * @uses $_POST['recur_start_date']
     * @uses $_POST['recur_end_date']
     * @uses $_POST['recur_frequency']
     * @uses $_POST['recur_invoices_due_after']
     * @uses $_POST['recur_email_invoice_template']
     * @see Mdl_Invoices_Recurring::validation_rules()
     */
    public function create_recurring()
    {
        $this->load->model('invoices/mdl_invoices_recurring');

        if ($this->mdl_invoices_recurring->run_validation()) {
            $this->mdl_invoices_recurring->save();

            $response = array(
                'success' => 1,
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
     * Returns an item based on the given item ID
     * @uses $_POST['item_id']
     */
    public function get_item()
    {
        $this->load->model('invoices/mdl_items');

        $item = $this->mdl_items->get_by_id($this->input->post('item_id'));

        echo json_encode($item);
    }

    /**
     * Returns the modal that can be used to create an invoice
     */
    public function modal_create_invoice()
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

        $this->layout->load_view('invoices/modal_create_invoice', $data);
    }

    /**
     * Returns the modal that can be used to make an invoice recurring
     * @uses $_POST['invoice_id']
     */
    public function modal_create_recurring()
    {
        $this->load->module('layout');

        $this->load->model('mdl_invoices_recurring');
        $this->load->model('email_templates/mdl_email_templates');

        $data = array(
            'invoice_id' => $this->input->post('invoice_id'),
            'recur_frequencies' => $this->mdl_invoices_recurring->recur_frequencies,
            'email_templates_invoice' => $this->mdl_email_templates->where('email_template_type', 'invoice')->get()->result()
        );

        $this->layout->load_view('invoices/modal_create_recurring', $data);
    }

    /**
     * Returns the start date for an invoice
     * @uses $_POST['invoice_date']
     * @uses $_POST['recur_frequency']
     */
    public function get_recur_start_date()
    {
        $invoice_date = $this->input->post('invoice_date');
        $recur_frequency = $this->input->post('recur_frequency');

        echo increment_user_date($invoice_date, $recur_frequency);
    }

    /**
     * Returns the modal that can be used to change the client for an invoice
     * @uses $_POST['client_name']
     * @uses $_POST['invoice_id']
     */
    public function modal_change_client()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = array(
            'client_name' => $this->input->post('client_name'),
            'invoice_id' => $this->input->post('invoice_id'),
            'clients' => $this->mdl_clients->get()->result(),
        );

        $this->layout->load_view('invoices/modal_change_client', $data);
    }

    /**
     * Changes the client for an invoice and returns the result
     * @uses $_POST['client_name']
     * @uses $_POST['invoice_id']
     */
    public function change_client()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_name = $this->input->post('client_name');
        $client = $this->mdl_clients->where('client_name', $this->db->escape_str($client_name))
            ->get()->row();

        if (!empty($client)) {
            $client_id = $client->id;
            $invoice_id = $this->input->post('invoice_id');

            $db_array = array(
                'client_id' => $client_id,
            );
            $this->db->where('id', $invoice_id);
            $this->db->update('invoices', $db_array);

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

    /**
     * Returns the modal that can be used to copy an invoice
     * @uses $_POST['invoice_id']
     */
    public function modal_copy_invoice()
    {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->mdl_invoices->where('invoices.id',
                $this->input->post('invoice_id'))->get()->row()
        );

        $this->layout->load_view('invoices/modal_copy_invoice', $data);
    }

    /**
     * Creates a duplicate / copy of a given invoice and returns the result
     * @uses $_POST['invoice_id']
     * @see Mdl_Invoices::validation_rules()
     */
    public function copy_invoice()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->input->post('invoice_id');

            $this->mdl_invoices->copy_invoice($source_id, $target_id);

            $response = array(
                'success' => 1,
                'invoice_id' => $target_id
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
     * Returns the modal that can be used to create a credit invoice from an invoice
     * @uses $_POST['invoice_id']
     * @see Mdl_Invoices::validation_rules()
     */
    public function modal_create_credit()
    {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoice_groups/mdl_invoice_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = array(
            'invoice_groups' => $this->mdl_invoice_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'invoice_id' => $this->input->post('invoice_id'),
            'invoice' => $this->mdl_invoices->where('invoices.id',
                $this->input->post('invoice_id'))->get()->row()
        );

        $this->layout->load_view('invoices/modal_create_credit', $data);
    }

    /**
     * Creates a credit invoice for a given invoice and returns the result
     * @uses $_POST['invoice_id']
     * @see Mdl_Invoices::validation_rules()
     * @see Mdl_Items::validation_rules()
     * @see Mdl_Invoice_Tax_Rates::validation_rules()
     */
    public function create_credit()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->input->post('invoice_id');

            $this->mdl_invoices->copy_credit_invoice($source_id, $target_id);

            // Set source invoice to read-only
            if ($this->config->item('disable_read_only') == false) {
                $this->mdl_invoices->where('id', $source_id);
                $this->mdl_invoices->update('invoices', array('is_read_only' => '1'));
            }

            // Set target invoice to credit invoice
            $this->mdl_invoices->where('id', $target_id);
            $this->mdl_invoices->update('invoices', array('credit_parent_id' => $source_id));

            $this->mdl_invoices->where('id', $target_id);
            $this->mdl_invoices->update('invoice_amounts', array('sign' => '-1'));

            $response = array(
                'success' => 1,
                'invoice_id' => $target_id
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
