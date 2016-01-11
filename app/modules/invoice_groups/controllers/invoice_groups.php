<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Invoice_Groups
 * @package Modules\Invoice_Groups\Controllers
 */
class Invoice_Groups extends Admin_Controller
{
    /**
     * Invoice_Groups constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoice_groups');
    }

    /**
     * Index page, returns all invoice groups
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_invoice_groups->paginate(site_url('invoice_groups/index'), $page);
        $invoice_groups = $this->mdl_invoice_groups->result();

        $this->layout->set('invoice_groups', $invoice_groups);
        $this->layout->buffer('content', 'invoice_groups/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the invoice group
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoice_groups');
        }

        if ($this->mdl_invoice_groups->run_validation()) {
            $this->mdl_invoice_groups->save($id);
            redirect('invoice_groups');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_invoice_groups->prep_form($id)) {
                show_404();
            }
        } elseif (!$id) {
            $this->mdl_invoice_groups->set_form_value('invoice_group_left_pad', 0);
            $this->mdl_invoice_groups->set_form_value('invoice_group_next_id', 1);
        }

        $this->layout->buffer('content', 'invoice_groups/form');
        $this->layout->render();
    }

    /**
     * Deletes a client from the database based on the given ID
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_invoice_groups->delete($id);
        redirect('invoice_groups');
    }
}
