<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Quote_Custom
 * @package Modules\CustomFields\Models
 */
class Mdl_Quote_Custom extends MY_Model
{
    public $table = 'ip_quote_custom';
    public $primary_key = 'ip_quote_custom.quote_custom_id';

    /**
     * Saves a custom field for quotes to the database
     * @param $quote_id
     * @param $db_array
     */
    public function save_custom($quote_id, $db_array)
    {
        $quote_custom_id = null;

        $db_array['quote_id'] = $quote_id;

        $quote_custom = $this->where('quote_id', $quote_id)->get();

        if ($quote_custom->num_rows()) {
            $quote_custom_id = $quote_custom->row()->quote_custom_id;
        }

        parent::save($quote_custom_id, $db_array);
    }
}
