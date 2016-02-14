<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Client_Custom
 * @package Modules\CustomFields\Models
 */
class Mdl_Client_Custom extends MY_Model
{
    public $table = 'custom_client';
    public $primary_key = 'custom_client.id';

    /**
     * Saves a custom field for clients to the database
     * @param $client_id
     * @param $db_array
     */
    public function save_custom($client_id, $db_array)
    {
        $client_custom_id = null;

        $db_array['client_id'] = $client_id;

        $client_custom = $this->where('client_id', $client_id)->get();

        if ($client_custom->num_rows()) {
            $client_custom_id = $client_custom->row()->client_custom_id;
        }

        parent::save($client_custom_id, $db_array);
    }
}
