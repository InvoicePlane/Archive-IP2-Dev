<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Tax_Rates
 * @package Modules\TaskRates\Models
 *
 * @property CI_DB_query_builder $db
 */
class Mdl_Tax_Rates extends Response_Model
{
    public $table = 'tax_rates';
    public $primary_key = 'tax_rates.id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * The default order by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('tax_rates.percent');
    }

    /**
     * Returns the validation rules for tax rates
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'name' => array(
                'field' => 'name',
                'label' => lang('tax_rate_name'),
                'rules' => 'required'
            ),
            'percent' => array(
                'field' => 'percent',
                'label' => lang('tax_rate_percent'),
                'rules' => 'required'
            )
        );
    }
}
