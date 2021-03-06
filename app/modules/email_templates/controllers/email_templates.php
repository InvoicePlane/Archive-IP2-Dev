<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Email_Templates
 * @package Modules\EmailTemplates\Controllers
 * @property CI_DB_query_builder $db
 * @property Layout $layout
 * @property Mdl_Custom_Fields $mdl_custom_fields
 * @property Mdl_Email_Templates $mdl_email_templates
 * @property Mdl_Templates $mdl_templates
 */
class Email_Templates extends User_Controller
{
    /**
     * Email_Templates constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_email_templates');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_email_templates->paginate(site_url('email_templates/index'), $page);
        $email_templates = $this->mdl_email_templates->result();

        $this->layout->set('email_templates', $email_templates);
        $this->layout->buffer('content', 'email_templates/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the email template
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('email_templates');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('email_template_title') != '') {
            $check = $this->db->get_where('email_templates',
                array('title' => $this->input->post('email_template_title')))->result();
            if (!empty($check)) {
                set_alert('danger', lang('email_template_already_exists'));
                redirect('email_templates/form');
            }
        }

        if ($this->mdl_email_templates->run_validation()) {
            $this->mdl_email_templates->save($id);
            redirect('email_templates');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_email_templates->prep_form($id)) {
                show_404();
            }
            $this->mdl_email_templates->set_form_value('is_update', true);
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('invoices/mdl_templates');

        $custom_fields = array();
        foreach (array_keys($this->mdl_custom_fields->custom_tables()) as $table) {
            $custom_fields[$table] = $this->mdl_custom_fields->by_table($table)->get()->result();
        }

        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->set('invoice_templates', $this->mdl_templates->get_invoice_templates());
        $this->layout->set('quote_templates', $this->mdl_templates->get_quote_templates());
        $this->layout->set('selected_pdf_template',
            $this->mdl_email_templates->form_value('email_template_pdf_template'));
        $this->layout->buffer('content', 'email_templates/form');
        $this->layout->render();
    }

    /**
     * Deletes an email template from the database based on the given ID
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_email_templates->delete($id);
        redirect('email_templates');
    }
}
