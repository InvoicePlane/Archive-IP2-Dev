<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Users
 * @package Modules\Users\Controllers
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Custom_Fields $mdl_custom_fields
 * @property Mdl_Users $mdl_users
 * @property Mdl_User_Clients $mdl_user_clients
 * @property Mdl_User_Custom $mdl_user_custom
 */
class Users extends Admin_Controller
{
    /**
     * Users constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_users');
    }

    /**
     * Index page, returns all users
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_users->paginate(site_url('users/index'), $page);
        $users = $this->mdl_users->result();

        $this->layout->set('users', $users);
        $this->layout->set('user_types', $this->mdl_users->user_types());
        $this->layout->buffer('content', 'users/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the user
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        if ($this->mdl_users->run_validation(($id) ? 'validation_rules_existing' : 'validation_rules')) {
            $id = $this->mdl_users->save($id);

            $this->load->model('custom_fields/mdl_user_custom');

            $this->mdl_user_custom->save_custom($id, $this->input->post('custom'));

            redirect('users');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_users->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_user_custom');

            $user_custom = $this->mdl_user_custom->where('user_id', $id)->get();

            if ($user_custom->num_rows()) {
                $user_custom = $user_custom->row();

                unset($user_custom->user_id, $user_custom->user_custom_id);

                foreach ($user_custom as $key => $val) {
                    $this->mdl_users->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_users->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('users/mdl_user_clients');
        $this->load->model('clients/mdl_clients');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->helper('country');

        $this->layout->set(
            array(
                'id' => $id,
                'user_types' => $this->mdl_users->user_types(),
                'user_clients' => $this->mdl_user_clients
                    ->where('user_clients.user_id', $id)->get()->result(),
                'custom_fields' => $this->mdl_custom_fields->by_table('custom_user')->get()->result(),
                'countries' => get_country_list(lang('cldr')),
                'selected_country' => $this->mdl_users->form_value('user_country') ?:
                    $this->mdl_settings->setting('default_country')
            )
        );

        $this->layout->buffer('user_client_table', 'users/partial_user_client_table');
        $this->layout->buffer('modal_user_client', 'users/modal_user_client');
        $this->layout->buffer('content', 'users/form');
        $this->layout->render();
    }

    /**
     * Change the password for a user
     * @param $user_id
     */
    public function change_password($user_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        if ($this->mdl_users->run_validation('validation_rules_change_password')) {
            $this->mdl_users->save_change_password($user_id, $this->input->post('user_password'));
            redirect('users/form/' . $user_id);
        }

        $this->layout->buffer('content', 'users/form_change_password');
        $this->layout->render();
    }

    /**
     * Deletes an user from the database
     * @param $id
     */
    public function delete($id)
    {
        if ($id <> 1) {
            $this->mdl_users->delete($id);
        }
        redirect('users');
    }

    /**
     * Removes a client from an user
     * @param $user_id
     * @param $user_client_id
     */
    public function delete_user_client($user_id, $user_client_id)
    {
        $this->load->model('mdl_user_clients');

        $this->mdl_user_clients->delete($user_client_id);

        redirect('users/form/' . $user_id);
    }
}
