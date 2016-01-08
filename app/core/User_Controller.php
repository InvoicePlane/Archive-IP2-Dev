<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class User_Controller
 * @package Core
 */
class User_Controller extends Base_Controller
{
    /**
     * User_Controller constructor.
     * @param $required_key
     * @param $required_val
     */
    public function __construct($required_key, $required_val)
    {
        parent::__construct();

        if ($this->session->userdata($required_key) <> $required_val) {
            redirect('sessions/login');
        }
    }

}
