<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Email_Templates_Ajax
 * @package Modules\EmailTemplates\Controllers
 * @property Mdl_Email_Templates $mdl_email_templates
 */
class Email_Templates_Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Returns the content of the email template based on the given ID
     * @uses $_POST['email_template_id']
     */
    public function get_content()
    {
        $this->load->model('email_templates/mdl_email_templates');

        $id = $this->input->post('id');
        echo json_encode($this->mdl_email_templates->get_by_id($id));
    }
}
