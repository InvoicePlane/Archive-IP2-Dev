<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class User_Controller
 * @package Core
 * @property CI_Session $session
 */
class User_Controller extends Base_Controller
{
    /**
     * User_Controller constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Check if user is not logged in or is client
        if (!user_logged_in() || user_is_client()) {
            $this->session->set_flashdata('alert_error', lang('permissions_not_allowed'));
            $this->session->keep_flashdata('alert_error');
            redirect('sessions/login');
        }
    }

}
