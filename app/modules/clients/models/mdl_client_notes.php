<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Client_Notes
 * @package Modules\Clients\Models
 *
 * @property CI_DB_query_builder $db
 */
class Mdl_Client_Notes extends Response_Model
{
    public $table = 'notes';
    public $primary_key = 'notes.id';
    public $date_created_field = 'date_created';

    /**
     * Default order directive that will be used on every query
     */
    public function default_order_by()
    {
        $this->db->order_by('notes.date_created DESC');
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
            'note' => array(
                'field' => 'note',
                'label' => lang('note'),
                'rules' => 'required'
            )
        );
    }
}
