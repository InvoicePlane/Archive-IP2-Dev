<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Client_Notes
 * @package Modules\Clients\Models
 */
class Mdl_Client_Notes extends Response_Model
{
    public $table = 'ip_client_notes';
    public $primary_key = 'ip_client_notes.client_note_id';

    /**
     * Default order directive that will be used on every query
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_client_notes.client_note_date DESC');
    }

    /**
     * The validation rules for client notes
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client'),
                'rules' => 'required'
            ),
            'client_note' => array(
                'field' => 'client_note',
                'label' => lang('note'),
                'rules' => 'required'
            )
        );
    }

    /**
     * Returns the prepared database array
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['client_note_date'] = date('Y-m-d');

        return $db_array;
    }
}
