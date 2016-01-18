<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Welcome
 * @package Modules\Start\Controllers
 */
class Start extends CI_Controller
{
    /**
     * Returns the welcome page
     */
    public function index()
    {
        $this->load->helper('url');
        $this->load->view('start');
    }
}