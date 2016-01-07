<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class Guest_Controller
 * @package Core
 */
class Guest_Controller extends User_Controller
{
    public $user_clients = array();

    /**
     * Guest_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct('user_type', 2);

        $this->load->model('users/mdl_user_clients');

        $user_clients = $this->mdl_user_clients->assigned_to($this->session->userdata('user_id'))->get()->result();

        if (!$user_clients) {
            die(lang('guest_account_denied'));
        }

        foreach ($user_clients as $user_client) {
            $this->user_clients[$user_client->client_id] = $user_client->client_id;
        }
    }

}

?>