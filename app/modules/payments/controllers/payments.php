<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Payments
 * @package Modules\Payments\Controllers
 * @property Layout $layout
 * @property Mdl_Custom_Fields $mdl_custom_fields
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Payment_Custom $mdl_payment_custom
 * @property Mdl_Payment_Methods $mdl_payment_methods
 * @property Mdl_Payments $mdl_payments
 */
class Payments extends Admin_Controller
{
    /**
     * Payments constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_payments');
    }

    /**
     * Index page, returns all payments
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_payments->paginate(site_url('payments/index'), $page);
        $payments = $this->mdl_payments->result();

        $this->layout->set(
            array(
                'payments' => $payments,
                'filter_display' => true,
                'filter_placeholder' => lang('filter_payments'),
                'filter_method' => 'filter_payments'
            )
        );

        $this->layout->buffer('content', 'payments/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the payment
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('payments');
        }

        if ($this->mdl_payments->run_validation()) {
            $id = $this->mdl_payments->save($id);

            $this->load->model('custom_fields/mdl_payment_custom');

            $this->mdl_payment_custom->save_custom($id, $this->input->post('custom'));

            redirect('payments');
        }

        if (!$this->input->post('btn_submit')) {
            $prep_form = $this->mdl_payments->prep_form($id);

            if ($id and !$prep_form) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_payment_custom');

            $payment_custom = $this->mdl_payment_custom->where('payment_id', $id)->get();

            if ($payment_custom->num_rows()) {
                $payment_custom = $payment_custom->row();

                unset($payment_custom->payment_id, $payment_custom->payment_custom_id);

                foreach ($payment_custom as $key => $val) {
                    $this->mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } else {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_payments->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('payment_methods/mdl_payment_methods');
        $this->load->model('custom_fields/mdl_custom_fields');

        $open_invoices = $this->mdl_invoices->where('invoice_amounts.balance >', 0)->get()->result();

        $amounts = array();
        $invoice_payment_methods = array();
        foreach ($open_invoices as $open_invoice) {
            $amounts['invoice' . $open_invoice->invoice_id] = format_amount($open_invoice->invoice_balance);
            $invoice_payment_methods['invoice' . $open_invoice->invoice_id] = $open_invoice->payment_method;
        }

        $this->layout->set(
            array(
                'payment_id' => $id,
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'open_invoices' => $open_invoices,
                'custom_fields' => $this->mdl_custom_fields->by_table('payment_custom')->get()->result(),
                'amounts' => json_encode($amounts),
                'invoice_payment_methods' => json_encode($invoice_payment_methods)
            )
        );

        if ($id) {
            $this->layout->set('payment', $this->mdl_payments->where('payments.id', $id)->get()->row());
        }

        $this->layout->buffer('content', 'payments/form');
        $this->layout->render();
    }

    /**
     * Deletes the payment from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_payments->delete($id);
        redirect('payments');
    }
}
