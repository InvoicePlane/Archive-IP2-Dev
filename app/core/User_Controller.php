<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class User_Controller extends Base_Controller
{

    public function __construct($required_key, $required_val)
    {
        parent::__construct();

        if ($this->session->userdata($required_key) <> $required_val) {
            redirect('sessions/login');
        }
    }

}
