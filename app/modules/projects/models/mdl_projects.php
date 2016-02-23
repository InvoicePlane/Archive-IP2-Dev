<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Projects
 * @package Modules\Projects\Models
 * @property CI_DB_query_builder $db
 */
class Mdl_Projects extends Response_Model
{
    public $table = 'projects';
    public $primary_key = 'projects.id';
    public $date_created_field = 'date_created';
    public $date_modified_field = 'date_modified';

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
        $this->db->order_by('projects.project_id');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('clients', 'clients.id = projects.client_id', 'left');
        $this->db->join('users', 'users.id = projects.user_id', 'left');
    }

    /**
     * Returns the validation rules for projects
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client'),
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => lang('user'),
            ),
            'project_name' => array(
                'field' => 'project_name',
                'label' => lang('project_name'),
                'rules' => 'required'
            ),
        );
    }
}
