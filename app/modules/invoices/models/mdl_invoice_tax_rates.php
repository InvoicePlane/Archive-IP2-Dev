<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Invoice_Tax_Rates
 * @package Modules\Invoices\Models
 * @property Mdl_Invoice_Amounts $mdl_invoice_amounts
 */
class Mdl_Invoice_Tax_Rates extends Response_Model
{
    public $table = 'invoice_tax_rates';
    public $primary_key = 'invoice_tax_rates.id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('tax_rates.name AS name');
        $this->db->select('tax_rates.percent AS percent');
        $this->db->select('invoice_tax_rates.*');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('tax_rates', 'tax_rates.id = invoice_tax_rates.id');
    }

    /**
     * Saves the invoice tax rate to the database with preparred database array
     * @param int $invoice_id
     * @param int $id
     * @param array $db_array
     * @return void
     */
    public function save($invoice_id, $id = null, $db_array = null)
    {
        parent::save($id, $db_array);

        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);
    }

    /**
     * Returns the validation rules for invoice tax rates
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
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'required'
            ),
            'include_item_tax' => array(
                'field' => 'include_item_tax',
                'label' => lang('tax_rate_placement'),
                'rules' => 'required'
            )
        );
    }
}
