<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Admin_Controller
 * @package Core
 */
class Admin_Controller extends Base_Controller
{
    /**
     * Admin_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Check if user is admin
        if (!user_is_admin()) {
            $this->session->set_flashdata('alert_error', lang('permissions_not_allowed'));
            redirect('dashboard');
        }
    }

}
