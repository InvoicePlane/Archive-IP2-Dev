<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Admin_Controller
 * @package Core
 * @property CI_Session $session
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
        if (!user_logged_in() || !user_is_admin()) {
            redirect('dashboard');
        }
    }

}
