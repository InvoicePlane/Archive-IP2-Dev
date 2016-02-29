<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Users
 * @package Modules\Users\Models
 *
 * @property Crypt $crypt
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_User_Clients $mdl_user_clients
 */
class Mdl_Users extends Response_Model
{
    public $table = 'users';
    public $primary_key = 'users.id';
    public $date_created_field = 'date_created';
    public $date_modified_field = 'date_modified';

    /**
     * Returns an array with all available user types
     * @TODO IP-366 - User roles
     * @return array
     */
    public function user_types()
    {
        return array(
            '1' => lang('administrator'),
            '2' => lang('guest_read_only')
        );
    }

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS user_custom.*, users.*', false);
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('user_custom', 'custom_user.user_id = users.id', 'left');
    }

    /**
     * The default oder by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('users.name');
    }

    /**
     * Returns the validation rules for users
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_role_id' => array(
                'field' => 'user_role_id',
                'label' => lang('user_role'),
                'rules' => 'required'
            ),
            'email' => array(
                'field' => 'email',
                'label' => lang('email'),
                'rules' => 'required|valid_email|is_unique[users.email]'
            ),
            'company' => array(
                'field' => 'company'
            ),
            'name' => array(
                'field' => 'name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'password' => array(
                'field' => 'password',
                'label' => lang('password'),
                'rules' => 'required|min_length[8]'
            ),
            'passwordv' => array(
                'field' => 'passwordv',
                'label' => lang('verify_password'),
                'rules' => 'required|matches[password]'
            ),
            'address_1' => array(
                'field' => 'address_1'
            ),
            'address_2' => array(
                'field' => 'address_2'
            ),
            'city' => array(
                'field' => 'city'
            ),
            'state' => array(
                'field' => 'state'
            ),
            'zip' => array(
                'field' => 'zip'
            ),
            'country' => array(
                'field' => 'country',
                'label' => lang('country'),
            ),
            'phone' => array(
                'field' => 'phone'
            ),
            'fax' => array(
                'field' => 'fax'
            ),
            'mobile' => array(
                'field' => 'mobile'
            ),
            'web' => array(
                'field' => 'web'
            ),
            'vat_id' => array(
                'field' => 'vat_id'
            ),
            'tax_code' => array(
                'field' => 'tax_code'
            )
        );
    }

    /**
     * Returns the validation rules for existing users
     * @return array
     */
    public function validation_rules_existing()
    {
        return array(
            'user_role_id' => array(
                'field' => 'user_role_id',
                'label' => lang('user_role'),
                'rules' => 'required'
            ),
            'email' => array(
                'field' => 'email',
                'label' => lang('email'),
                'rules' => 'required|valid_email'
            ),
            'company' => array(
                'field' => 'company'
            ),
            'name' => array(
                'field' => 'name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'password' => array(
                'field' => 'password',
                'label' => lang('password'),
                'rules' => 'required|min_length[8]'
            ),
            'passwordv' => array(
                'field' => 'passwordv',
                'label' => lang('verify_password'),
                'rules' => 'required|matches[password]'
            ),
            'address_1' => array(
                'field' => 'address_1'
            ),
            'address_2' => array(
                'field' => 'address_2'
            ),
            'city' => array(
                'field' => 'city'
            ),
            'state' => array(
                'field' => 'state'
            ),
            'zip' => array(
                'field' => 'zip'
            ),
            'country' => array(
                'field' => 'country',
                'label' => lang('country'),
            ),
            'phone' => array(
                'field' => 'phone'
            ),
            'fax' => array(
                'field' => 'fax'
            ),
            'mobile' => array(
                'field' => 'mobile'
            ),
            'web' => array(
                'field' => 'web'
            ),
            'vat_id' => array(
                'field' => 'vat_id'
            ),
            'tax_code' => array(
                'field' => 'tax_code'
            )
        );
    }

    /**
     * Returns the validation rules for a password change for an user
     * @return array
     */
    public function validation_rules_change_password()
    {
        return array(
            'password' => array(
                'field' => 'password',
                'label' => lang('password'),
                'rules' => 'required'
            ),
            'passwordv' => array(
                'field' => 'passwordv',
                'label' => lang('verify_password'),
                'rules' => 'required|matches[password]'
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

        if (isset($db_array['password'])) {
            unset($db_array['passwordv']);

            $this->load->library('crypt');

            $user_psalt = $this->crypt->salt();

            $db_array['psalt'] = $user_psalt;
            $db_array['password'] = $this->crypt->generate_password($db_array['password'], $user_psalt);
        }

        return $db_array;
    }

    /**
     * Saves the changed password to the database
     * @TODO Success alert needs language string
     * @param $user_id
     * @param $password
     */
    public function save_change_password($user_id, $password)
    {
        $this->load->library('crypt');

        $user_psalt = $this->crypt->salt();
        $user_password = $this->crypt->generate_password($password, $user_psalt);

        $db_array = array(
            'psalt' => $user_psalt,
            'password' => $user_password
        );

        $this->db->where('id', $user_id);
        $this->db->update('users', $db_array);

        $this->session->set_flashdata('alert_success', 'Password Successfully Changed');
    }

    /**
     * Saves an user to the database
     * @param null $id
     * @param null $db_array
     * @return int|null
     */
    public function save($id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        if ($user_clients = $this->session->userdata('user_clients')) {
            $this->load->model('users/mdl_user_clients');

            foreach ($user_clients as $user_client) {
                $this->mdl_user_clients->save(null, array('user_id' => $id, 'client_id' => $user_client));
            }

            $this->session->unset_userdata('user_clients');
        }

        return $id;
    }

    /**
     * Deletes an user from the database
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }
}
