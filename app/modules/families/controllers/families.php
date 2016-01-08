<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Families
 * @package Modules\Families\Controllers
 */
class Families extends Admin_Controller
{
    /**
     * Families constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_families');
    }

    /**
     * Index page, returns all product families
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_families->paginate(site_url('families/index'), $page);
        $families = $this->mdl_families->result();

        $this->layout->set('families', $families);
        $this->layout->buffer('content', 'families/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the product family
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('families');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('family_name') != '') {
            $check = $this->db->get_where('ip_families',
                array('family_name' => $this->input->post('family_name')))->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('family_already_exists'));
                redirect('families/form');
            }
        }

        if ($this->mdl_families->run_validation()) {
            $this->mdl_families->save($id);
            redirect('families');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_families->prep_form($id)) {
                show_404();
            }
            $this->mdl_families->set_form_value('is_update', true);
        }

        $this->layout->buffer('content', 'families/form');
        $this->layout->render();
    }

    /**
     * Deletes a client from the database based on the given ID
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_families->delete($id);
        redirect('families');
    }
}
