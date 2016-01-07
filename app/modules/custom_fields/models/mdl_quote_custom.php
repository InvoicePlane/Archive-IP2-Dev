<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Mdl_Quote_Custom extends MY_Model
{
    public $table = 'ip_quote_custom';
    public $primary_key = 'ip_quote_custom.quote_custom_id';

    public function save_custom($quote_id, $db_array)
    {
        $quote_custom_id = NULL;

        $db_array['quote_id'] = $quote_id;

        $quote_custom = $this->where('quote_id', $quote_id)->get();

        if ($quote_custom->num_rows()) {
            $quote_custom_id = $quote_custom->row()->quote_custom_id;
        }

        parent::save($quote_custom_id, $db_array);
    }

}
