<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Notes
 * @package Modules\Notes\Models
 *
 * @property CI_DB_query_builder $db
 * @property Mdl_Notes $mdl_notes
 */
class Mdl_Notes extends Response_Model
{
    public $table = 'notes';
    public $primary_key = 'notes.id';
    public $date_created_field = 'date_created';

    public $available_types = ['client', 'quote', 'invoice', 'payment', 'task'];

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS notes.*,
            users.name AS user,
            users.id AS user_id,
            notes.*", false);
    }

    /**
     * The default select directive used in every query
     */
    public function default_where()
    {
        $this->db->where('notes.date_deleted', null);
    }

    /**
     * Default order directive that will be used on every query
     */
    public function default_order_by()
    {
        $this->db->order_by('notes.date_created DESC');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('users', 'notes.user_id = users.id', 'left');
    }

    /**
     * Returns all notes that are saved for a given type and id
     * @param $by
     * @param $id
     */
    public function get_notes($by, $id)
    {
        return $this->where($by . '_id', $id)->get()->result();
    }

    /**
     * The validation rules for client notes
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_id' => array(
                'field' => 'user_id',
                'label' => lang('user'),
                'rules' => 'required'
            ),
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client'),
            ),
            'quote_id' => array(
                'field' => 'quote_id',
                'label' => lang('quote'),
            ),
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
            ),
            'payment_id' => array(
                'field' => 'payment_id',
                'label' => lang('payment'),
            ),
            'task_id' => array(
                'field' => 'task_id',
                'label' => lang('task'),
            ),
            'note' => array(
                'field' => 'note',
                'label' => lang('note'),
                'rules' => 'required'
            )
        );
    }

    /**
     * Deletes the note from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->db->set('date_deleted', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->update($this->mdl_notes->table);
    }
}
