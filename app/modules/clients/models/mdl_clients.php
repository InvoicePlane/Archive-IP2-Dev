<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Clients
 * @package Modules\Clients\Models
 */
class Mdl_Clients extends Response_Model
{
    public $table = 'ip_clients';
    public $primary_key = 'ip_clients.client_id';
    public $date_created_field = 'client_date_created';
    public $date_modified_field = 'client_date_modified';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_client_custom.*, ip_clients.*', false);
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('ip_client_custom', 'ip_client_custom.client_id = ip_clients.client_id', 'left');
    }

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name');
    }

    /**
     * Returns the validation rules for clients
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'client_name' => array(
                'field' => 'client_name',
                'label' => lang('client_name'),
                'rules' => 'required'
            ),
            'client_active' => array(
                'field' => 'client_active'
            ),
            'client_address_1' => array(
                'field' => 'client_address_1'
            ),
            'client_address_2' => array(
                'field' => 'client_address_2'
            ),
            'client_city' => array(
                'field' => 'client_city'
            ),
            'client_state' => array(
                'field' => 'client_state'
            ),
            'client_zip' => array(
                'field' => 'client_zip'
            ),
            'client_country' => array(
                'field' => 'client_country'
            ),
            'client_phone' => array(
                'field' => 'client_phone'
            ),
            'client_fax' => array(
                'field' => 'client_fax'
            ),
            'client_mobile' => array(
                'field' => 'client_mobile'
            ),
            'client_email' => array(
                'field' => 'client_email'
            ),
            'client_web' => array(
                'field' => 'client_web'
            ),
            'client_vat_id' => array(
                'field' => 'user_vat_id'
            ),
            'client_tax_code' => array(
                'field' => 'user_tax_code'
            )
        );
    }

    /**
     * Returns the prepared database array
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        if (!isset($db_array['client_active'])) {
            $db_array['client_active'] = 0;
        }

        return $db_array;
    }

    /**
     * Deletes the client form the database and all orphaned entries
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns client_id of existing or new record
     */
    public function client_lookup($client_name)
    {
        $client = $this->mdl_clients->where('client_name', $client_name)->get();

        if ($client->num_rows()) {
            $client_id = $client->row()->client_id;
        } else {
            $db_array = array(
                'client_name' => $client_name
            );

            $client_id = parent::save(null, $db_array);
        }

        return $client_id;
    }

    /**
     * Query to get the total amount for this client
     * @return $this
     */
    public function with_total()
    {
        $this->filter_select('IFNULL((SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_total',
            false);
        return $this;
    }

    /**
     * Query to get the total paid amount for this client
     * @return $this
     */
    public function with_total_paid()
    {
        $this->filter_select('IFNULL((SELECT SUM(invoice_paid) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_paid',
            false);
        return $this;
    }

    /**
     * Query to get the total balance for this client
     * @return $this
     */
    public function with_total_balance()
    {
        $this->filter_select('IFNULL((SELECT SUM(invoice_balance) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE ip_invoices.client_id = ip_clients.client_id)), 0) AS client_invoice_balance',
            false);
        return $this;
    }

    /**
     * Query filter used ot determine if the client is active
     * @return $this
     */
    public function is_active()
    {
        $this->filter_where('client_active', 1);
        return $this;
    }

    /**
     * Query filter used ot determine if the client is inactive
     * @return $this
     */
    public function is_inactive()
    {
        $this->filter_where('client_active', 0);
        return $this;
    }

}
