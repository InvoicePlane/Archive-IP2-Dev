<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Sessions
 * @package Modules\Sessions\Models
 */
class Mdl_Sessions extends CI_Model
{
    /**
     * Handles the user authentification
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function auth($email, $password)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users');

        if ($query->num_rows()) {
            $user = $query->row();

            $this->load->library('crypt');

            if ($this->crypt->check_password($user->password, $password)) {

                // Get the user role information
                $this->db->where('id', $user->user_role_id);
                $role = $this->db->get('user_roles')->row_array();

                $session_data = array(
                    'user' => array(
                        'id' => $user->id,
                        'name' => $user->name,
                        'company' => $user->company,
                        'email' => $user->email,
                        'role' => $role['name'],
                        'permissions' => json_decode($role['permissions']),
                        'is_client' => $role['is_client'],
                    )
                );

                $this->session->set_userdata($session_data);

                return true;
            }
        }

        return false;
    }
}
