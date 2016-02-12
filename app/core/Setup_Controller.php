<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Base_Controller
 * @package Core
 * @property CI_Config $config
 * @property CI_Input $input
 * @property CI_Lang $lang
 * @property Mdl_Settings $mdl_settings
 */
class Setup_Controller extends MX_Controller
{
    public $ajax_controller = false;

    /**
     * Base_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->config->load('invoice_plane');

        // Don't allow non-ajax requests to ajax controllers
        if ($this->ajax_controller and !$this->input->is_ajax_request()) {
            exit;
        }
        
        $this->load->helper('ip');
        $this->load->helper('url');
        $this->load->helper('redirect');

        $this->load->library('session');
        $this->load->library('form_validation');
        
        $this->load->model('settings/mdl_settings');

        define('THEME_URL', base_url() . 'themes/InvoicePlane/');

        // Load language strings
        $this->lang->load('ip', 'english');
        $this->load->helper('language');

        // Load the layout module
        $this->load->module('layout');
    }

}
