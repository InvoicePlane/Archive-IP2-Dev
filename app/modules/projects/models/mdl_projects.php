<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Projects
 * @package Modules\Projects\Models
 */
class Mdl_Projects extends Response_Model
{
    public $table = 'ip_projects';
    public $primary_key = 'ip_projects.project_id';

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
        $this->db->order_by('ip_projects.project_id');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_projects.client_id', 'left');
    }

    /**
     * Returns the validation rules for projects
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'project_name' => array(
                'field' => 'project_name',
                'label' => lang('project_name'),
                'rules' => 'required'
            ),
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client'),
            )
        );
    }
}
