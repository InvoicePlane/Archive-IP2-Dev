<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Invoice_Custom
 * @package Modules\CustomFields\Models
 */
class Mdl_Invoice_Custom extends MY_Model
{
    public $table = 'custom_invoice';
    public $primary_key = 'custom_invoice.id';

    /**
     * Saves a custom field for invoices to the database
     * @param $invoice_id
     * @param $db_array
     */
    public function save_custom($invoice_id, $db_array)
    {
        $invoice_custom_id = null;

        $db_array['invoice_id'] = $invoice_id;

        $invoice_custom = $this->where('invoice_id', $invoice_id)->get();

        if ($invoice_custom->num_rows()) {
            $invoice_custom_id = $invoice_custom->row()->id;
        }

        parent::save($invoice_custom_id, $db_array);
    }
}
