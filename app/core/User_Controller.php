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
            set_alert('danger', lang('permissions_not_allowed'));
            redirect('sessions/login');
        }
    }

}
