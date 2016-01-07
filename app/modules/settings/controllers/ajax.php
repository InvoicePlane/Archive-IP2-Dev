<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Ajax extends Admin_Controller
{
    public $ajax_controller = TRUE;

    public function get_cron_key()
    {
        echo random_string('alnum', 16);
    }

}
