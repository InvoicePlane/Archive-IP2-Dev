<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Payments
 * @package Modules\Payments\Models
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Invoice_Amounts $mdl_invoice_amounts
 */
class Mdl_Payments extends Response_Model
{
    public $table = 'payments';
    public $primary_key = 'payments.id';
    public $validation_rules = 'validation_rules';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS custom_payment.*,
            payment_methods.*,
            invoice_amounts.*,
            clients.name,
        	clients.id,
            invoices.invoice_number,
            invoices.date_created,
            payments.*", false);
    }

    /**
     * The default order by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('payments.payment_date DESC');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('invoices', 'invoices.id = payments.invoice_id');
        $this->db->join('clients', 'clients.id = invoices.client_id');
        $this->db->join('invoice_amounts', 'invoice_amounts.invoice_id = invoices.id');
        $this->db->join('payment_methods', 'payment_methods.id = payments.payment_method_id',
            'left');
        $this->db->join('custom_payment', 'custom_payment.payment_id = payments.id', 'left');
    }

    /**
     * Returns the validation rules for payments
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ),
            'payment_method_id' => array(
                'field' => 'payment_method_id',
                'label' => lang('payment_method')
            ),
            'amount' => array(
                'field' => 'amount',
                'label' => lang('payment'),
                'rules' => 'required|callback_validate_payment_amount'
            ),
            'note' => array(
                'field' => 'payment_note',
                'label' => lang('note')
            ),
            'payment_date' => array(
                'field' => 'payment_date',
                'label' => lang('date'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * Validates the payment amount against the invoice balance
     * @uses $_POST['invoice_id']
     * @uses $_POST['payment_id']
     * @param $amount
     * @return bool
     */
    public function validate_payment_amount($amount)
    {
        $invoice_id = $this->input->post('invoice_id');
        $payment_id = $this->input->post('payment_id');

        $invoice_balance = $this->db->where('invoice_id',
            $invoice_id)->get('invoice_amounts')->row()->balance;

        if ($payment_id) {
            $payment = $this->db->where('id', $payment_id)->get('payments')->row();

            $invoice_balance = $invoice_balance + $payment->amount;
        }

        if ($amount > $invoice_balance) {
            $this->form_validation->set_message('validate_payment_amount', lang('payment_cannot_exceed_balance'));
            return false;
        }

        return true;
    }

    /**
     * Saves a payment ot the database
     * @param null $id
     * @param null $db_array
     * @return int|null
     */
    public function save($id = null, $db_array = null)
    {
        $db_array = ($db_array) ? $db_array : $this->db_array();

        // Save the payment
        $id = parent::save($id, $db_array);

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id']);

        return $id;
    }

    /**
     * Deletes a payment from the database
     * @param null $id
     */
    public function delete($id = null)
    {
        // Get the invoice id before deleting payment
        $this->db->select('invoice_id');
        $this->db->where('id', $id);
        $invoice_id = $this->db->get('payments')->row()->invoice_id;

        // Delete the payment
        parent::delete($id);

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        // Change invoice status back to sent
        $this->db->select('status_id');
        $this->db->where('id', $invoice_id);
        $invoice = $this->db->get('invoices')->row();

        if ($invoice->invoice_status_id == 4) {
            $this->db->where('id', $invoice_id);
            $this->db->set('status_id', 2);
            $this->db->update('invoices');
        }

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns the prepared database array
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['payment_date'] = date_to_mysql($db_array['payment_date']);
        $db_array['amount'] = standardize_amount($db_array['amount']);

        return $db_array;
    }

    /**
     * Prepares the form with the payment date
     * @param null $id
     * @return bool
     */
    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('payment_date', date('Y-m-d'));
        }

        return true;
    }

    /**
     * Query to get the payments by client
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('clients.id', $client_id);
        return $this;
    }
}
