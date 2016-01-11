<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Quote_Tax_Rates
 * @package Modules\Quotes\Models
 */
class Mdl_Quote_Tax_Rates extends Response_Model
{
    public $table = 'ip_quote_tax_rates';
    public $primary_key = 'ip_quote_tax_rates.quote_tax_rate_id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS quote_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS quote_tax_rate_percent');
        $this->db->select('ip_quote_tax_rates.*');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_quote_tax_rates.tax_rate_id');
    }

    /**
     * Saves a quote tax rate to the database
     * @param int|null $quote_id
     * @param null $id
     * @param null $db_array
     * @return void
     */
    public function save($quote_id, $id = null, $db_array = null)
    {
        parent::save($id, $db_array);

        $this->load->model('quotes/mdl_quote_amounts');
        $this->mdl_quote_amounts->calculate($quote_id);
    }

    /**
     * Returns the validation rules for quote tax rates
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'quote_id' => array(
                'field' => 'quote_id',
                'label' => lang('quote'),
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
