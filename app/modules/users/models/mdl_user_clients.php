<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_User_Clients
 * @package Modules\Users\Models
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 */
class Mdl_User_Clients extends MY_Model
{
    public $table = 'user_clients';
    public $primary_key = 'user_clients.id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('user_clients.*, users.name, clients.name');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('users', 'users.id = user_clients.user_id');
        $this->db->join('clients', 'clients.id = user_clients.client_id');
    }

    /**
     * The default oder by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('clients.name');
    }

    /**
     * Query to get all clients that are assigned to a user
     * @param $user_id
     * @return $this
     */
    public function assigned_to($user_id)
    {
        $this->filter_where('user_clients.user_id', $user_id);
        return $this;
    }
}
