<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Payment_Methods
 * @package Modules\PaymentMethods\Controllers
 */
class Payment_Methods extends Admin_Controller
{
    /**
     * Payment_Methods constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_payment_methods');
    }

    /**
     * Index page, returns all payment methods
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_payment_methods->paginate(site_url('payment_methods/index'), $page);
        $payment_methods = $this->mdl_payment_methods->result();

        $this->layout->set('payment_methods', $payment_methods);
        $this->layout->buffer('content', 'payment_methods/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the payment methods
     * for the given ID and can be used as an edit form.
     * @param null|int $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('payment_methods');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('payment_method_name') != '') {
            $check = $this->db->get_where('ip_payment_methods',
                array('payment_method_name' => $this->input->post('payment_method_name')))->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', lang('payment_method_already_exists'));
                redirect('payment_methods/form');
            }
        }

        if ($this->mdl_payment_methods->run_validation()) {
            $this->mdl_payment_methods->save($id);
            redirect('payment_methods');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_payment_methods->prep_form($id)) {
                show_404();
            }
            $this->mdl_payment_methods->set_form_value('is_update', true);
        }

        $this->layout->buffer('content', 'payment_methods/form');
        $this->layout->render();
    }

    /**
     * Deletes a payment method from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_payment_methods->delete($id);
        redirect('payment_methods');
    }
}
