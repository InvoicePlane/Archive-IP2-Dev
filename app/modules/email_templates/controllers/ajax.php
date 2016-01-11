<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Ajax
 * @package Modules\EmailTemplates\Controllers
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Returns the content of the email template based on the given ID
     * @uses $_POST['email_template_id']
     */
    public function get_content()
    {
        $this->load->model('email_templates/mdl_email_templates');

        $id = $this->input->post('email_template_id');
        echo json_encode($this->mdl_email_templates->get_by_id($id));
    }
}
