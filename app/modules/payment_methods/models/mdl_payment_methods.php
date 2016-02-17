<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Payment_Methods
 * @package Modules\PaymentMethods\Controllers
 */
class Mdl_Payment_Methods extends Response_Model
{
    public $table = 'payment_methods';
    public $primary_key = 'payment_methods.id';

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
    public function order_by()
    {
        $this->db->order_by('payment_methods.method_name');
    }

    /**
     * Returns the validation rules for payment methods
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'method_name' => array(
                'field' => 'method_name',
                'label' => lang('payment_method'),
                'rules' => 'required'
            )
        );
    }
}
