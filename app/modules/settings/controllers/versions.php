<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Versions extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_versions');
    }

    public function index($page = 0)
    {
        $this->mdl_versions->paginate(site_url('versions/index'), $page);
        $versions = $this->mdl_versions->result();

        $this->layout->set('versions', $versions);
        $this->layout->buffer('content', 'settings/versions');
        $this->layout->render();
    }

}
