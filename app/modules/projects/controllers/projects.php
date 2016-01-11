<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Projects
 * @package Modules\Projects\Controllers
 */
class Projects extends Admin_Controller
{
    /**
     * Projects constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_projects');
    }

    /**
     * Index page, returns all projects
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_projects->paginate(site_url('projects/index'), $page);
        $projects = $this->mdl_projects->result();

        $this->layout->set('projects', $projects);
        $this->layout->buffer('content', 'projects/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the project
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('projects');
        }

        if ($this->mdl_projects->run_validation()) {
            $this->mdl_projects->save($id);
            redirect('projects');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_projects->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('clients/mdl_clients');

        $this->layout->set(
            array(
                'clients' => $this->mdl_clients->where('client_active', 1)->get()->result()
            )
        );

        $this->layout->buffer('content', 'projects/form');
        $this->layout->render();
    }

    /**
     * Deletes a project from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_projects->delete($id);
        redirect('projects');
    }
}
