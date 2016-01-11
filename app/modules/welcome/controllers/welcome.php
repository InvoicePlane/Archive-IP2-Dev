<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Welcome
 * @package Modules\Welcome\Controllers
 */
class Welcome extends CI_Controller
{
    /**
     * Returns the welcome page
     */
    public function index()
    {
        $this->load->view('welcome');
    }
}