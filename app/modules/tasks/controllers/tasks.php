<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Tasks
 * @package Modules\Tasks\Controllers
 */
class Tasks extends Admin_Controller
{
    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_tasks');
    }

    /**
     * Index page, returns all tasks
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_tasks->paginate(site_url('tasks/index'), $page);
        $tasks = $this->mdl_tasks->result();

        $this->layout->set('tasks', $tasks);
        $this->layout->set('task_statuses', $this->mdl_tasks->statuses());
        $this->layout->buffer('content', 'tasks/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the task
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('tasks');
        }

        if ($this->mdl_tasks->run_validation()) {
            $this->mdl_tasks->save($id);
            redirect('tasks');
        }

        if (!$this->input->post('btn_submit')) {
            $prep_form = $this->mdl_tasks->prep_form($id);
            if ($id and !$prep_form) {
                show_404();
            }
        }

        $this->load->model('projects/mdl_projects');
        $this->load->model('tax_rates/mdl_tax_rates');

        $this->layout->set(
            array(
                'projects' => $this->mdl_projects->get()->result(),
                'task_statuses' => $this->mdl_tasks->statuses(),
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
            )
        );
        $this->layout->buffer('content', 'tasks/form');
        $this->layout->render();
    }

    /**
     * Deletes a task from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_tasks->delete($id);
        redirect('tasks');
    }
}
