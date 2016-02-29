<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Quote_Tax_Rates
 * @package Modules\Quotes\Models
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Quote_Amounts $mdl_quote_amounts
 */
class Mdl_Quote_Tax_Rates extends Response_Model
{
    public $table = 'quote_tax_rates';
    public $primary_key = 'quote_tax_rates.id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('tax_rates.name AS quote_tax_rate_name');
        $this->db->select('tax_rates.percent AS quote_tax_rate_percent');
        $this->db->select('quote_tax_rates.*');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('tax_rates', 'tax_rates.id = quote_tax_rates.tax_rate_id');
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
                'rules' => 'required',
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'required',
            ),
            'include_item_tax' => array(
                'field' => 'include_item_tax',
                'label' => lang('tax_rate_placement'),
                'rules' => 'required',
            ),
            'amount' => array(
                'field' => 'amount',
                'label' => lang('amount'),
            ),
        );
    }
}
