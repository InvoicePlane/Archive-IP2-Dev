<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_User_Clients
 * @package Modules\Users\Models
 */
class Mdl_User_Clients extends MY_Model
{
    public $table = 'ip_user_clients';
    public $primary_key = 'ip_user_clients.user_client_id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('ip_user_clients.*, ip_users.user_name, ip_clients.client_name');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_clients.user_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_user_clients.client_id');
    }

    /**
     * The default oder by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name');
    }

    /**
     * Query to get all clients that are assigned to a user
     * @param $user_id
     * @return $this
     */
    public function assigned_to($user_id)
    {
        $this->filter_where('ip_user_clients.user_id', $user_id);
        return $this;
    }
}
