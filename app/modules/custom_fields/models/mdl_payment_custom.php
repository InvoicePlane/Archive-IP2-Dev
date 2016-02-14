<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Payment_Custom
 * @package Modules\CustomFields\Models
 */
class Mdl_Payment_Custom extends MY_Model
{
    public $table = 'custom_payment';
    public $primary_key = 'custom_payment.id';

    /**
     * Saves a custom field for payments to the database
     * @param $payment_id
     * @param $db_array
     */
    public function save_custom($payment_id, $db_array)
    {
        $payment_custom_id = null;

        $db_array['payment_id'] = $payment_id;

        $payment_custom = $this->where('payment_id', $payment_id)->get();

        if ($payment_custom->num_rows()) {
            $payment_custom_id = $payment_custom->row()->payment_custom_id;
        }

        parent::save($payment_custom_id, $db_array);
    }
}
