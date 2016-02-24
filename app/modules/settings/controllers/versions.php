<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Versions
 * @package Modules\Settings\Controllers
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Versions $mdl_versions
 */
class Versions extends Admin_Controller
{
    /**
     * Versions constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_versions');
    }

    /**
     * index page, returns all versions
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_versions->paginate(site_url('versions/index'), $page);
        $versions = $this->mdl_versions->result();

        $this->layout->set('versions', $versions);
        $this->layout->buffer('content', 'settings/versions');
        $this->layout->render();
    }
}
