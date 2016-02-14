<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Email_Templates
 * @package Modules\EmailTemplates\Models
 */
class Mdl_Email_Templates extends Response_Model
{
    public $table = 'email_templates';
    public $primary_key = 'email_templates.id';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    /**
     * The default order by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('title');
    }

    /**
     * Returns the validation rules for email templates
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'title' => array(
                'field' => 'title',
                'label' => lang('title'),
                'rules' => 'required'
            ),
            'type' => array(
                'field' => 'pdf_quote_template',
                'label' => lang('type')
            ),
            'subject' => array(
                'field' => 'subject',
                'label' => lang('subject')
            ),
            'to_email' => array(
                'field' => 'to_email',
                'label' => lang('to_email')
            ),
            'from_name' => array(
                'field' => 'from_name',
                'label' => lang('from_name')
            ),
            'from_email' => array(
                'field' => 'from_email',
                'label' => lang('from_email')
            ),
            'cc' => array(
                'field' => 'cc',
                'label' => lang('cc')
            ),
            'bcc' => array(
                'field' => 'bcc',
                'label' => lang('bcc')
            ),
            'pdf_template' => array(
                'field' => 'pdf_template',
                'label' => lang('default_pdf_template')
            ),
            'body_template_file' => array(
                'field' => 'body_template_file',
                'label' => lang('body_template_file')
            ),
            'send_attachments' => array(
                'field' => 'send_attachments',
                'label' => lang('send_attachments')
            ),
            'send_pdf' => array(
                'field' => 'send_pdf',
                'label' => lang('send_pdf')
            ),
        );
    }
}
