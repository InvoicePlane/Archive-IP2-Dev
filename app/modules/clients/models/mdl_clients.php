<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Clients
 * @package Modules\Clients\Models
 *
 * @property CI_DB_query_builder $db
 * @property Mdl_Clients $mdl_clients
 */
class Mdl_Clients extends Response_Model
{
    public $table = 'clients';
    public $primary_key = 'clients.id';
    public $date_created_field = 'date_created';
    public $date_modified_field = 'date_modified';
    public $date_deleted_field = 'date_deleted';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('custom_client.*, clients.*', false);
        $this->db->where('date_deleted', null);
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('custom_client', 'custom_client.client_id = clients.id', 'left');
    }

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('clients.name');
    }

    /**
     * Returns the validation rules for clients
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'name' => array(
                'field' => 'name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'is_active' => array(
                'field' => 'is_active'
            ),
            'address_1' => array(
                'field' => 'address_1'
            ),
            'address_2' => array(
                'field' => 'address_2'
            ),
            'city' => array(
                'field' => 'city'
            ),
            'state' => array(
                'field' => 'state'
            ),
            'zip' => array(
                'field' => 'zip'
            ),
            'country' => array(
                'field' => 'country'
            ),
            'phone' => array(
                'field' => 'phone'
            ),
            'fax' => array(
                'field' => 'fax'
            ),
            'mobile' => array(
                'field' => 'mobile'
            ),
            'email' => array(
                'field' => 'email'
            ),
            'web' => array(
                'field' => 'web'
            ),
            'vat_id' => array(
                'field' => 'vat_id'
            ),
            'tax_code' => array(
                'field' => 'tax_code'
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

        if (!isset($db_array['is_active'])) {
            $db_array['is_active'] = 0;
        }

        return $db_array;
    }

    /**
     * Deletes the client form the database and all orphaned entries
     * @param $id
     */
    public function delete($id)
    {
        $this->db->set('date_deleted', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->update('clients');
    }

    /**
     * Returns id of existing or new record
     *
     * @param $name
     * @return int
     */
    public function client_lookup($name)
    {
        $client = $this->mdl_clients->where('name', $name)->get();

        if ($client->num_rows()) {
            $id = $client->row()->id;
        } else {
            $db_array = array(
                'name' => $name
            );

            $id = parent::save(null, $db_array);
        }

        return $id;
    }

    /**
     * Query to get the total amount for this client
     * @return $this
     */
    public function with_total()
    {
        $this->filter_select('IFNULL((SELECT SUM(total) FROM invoice_amounts WHERE invoice_id IN (SELECT id FROM invoices WHERE invoices.client_id = clients.id)), 0) AS client_invoice_total',
            false);
        return $this;
    }

    /**
     * Query to get the total paid amount for this client
     * @return $this
     */
    public function with_total_paid()
    {
        $this->filter_select('IFNULL((SELECT SUM(paid) FROM invoice_amounts WHERE invoice_id IN (SELECT id FROM invoices WHERE invoices.client_id = clients.id)), 0) AS client_invoice_paid',
            false);
        return $this;
    }

    /**
     * Query to get the total balance for this client
     * @return $this
     */
    public function with_total_balance()
    {
        $this->filter_select('IFNULL((SELECT SUM(balance) FROM invoice_amounts WHERE invoice_id IN (SELECT id FROM invoices WHERE invoices.client_id = clients.id)), 0) AS client_invoice_balance',
            false);
        return $this;
    }

    /**
     * Query filter used ot determine if the client is active
     * @return $this
     */
    public function is_active()
    {
        $this->filter_where('is_active', 1);
        return $this;
    }

    /**
     * Query filter used to determine if the client is inactive
     * @return $this
     */
    public function is_inactive()
    {
        $this->filter_where('is_active', 0);
        return $this;
    }
    
}
