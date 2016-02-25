<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Tax_Rates
 * @package Modules\TaskRates\Controllers
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Tax_Rates $mdl_tax_rates
 *
 */
class Tax_Rates extends Admin_Controller
{
    /**
     * Tax_Rates constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_tax_rates');
    }

    /**
     * Index page, returns all tax rates
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_tax_rates->paginate(site_url('tax_rates/index'), $page);
        $tax_rates = $this->mdl_tax_rates->result();

        $this->layout->set('tax_rates', $tax_rates);
        $this->layout->buffer('content', 'tax_rates/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the tax rate
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('tax_rates');
        }

        if ($this->mdl_tax_rates->run_validation()) {
            $this->mdl_tax_rates->form_values['percent'] = standardize_amount($this->mdl_tax_rates->form_values['percent']);


            // We need to use the correct decimal point for sql IPT-310
            $db_array = $this->mdl_tax_rates->db_array();
            $db_array['percent'] = standardize_amount($this->input->post('percent'));

            $this->mdl_tax_rates->save($id, $db_array);

            redirect('tax_rates');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_tax_rates->prep_form($id)) {
                show_404();
            }
        }

        $this->layout->buffer('content', 'tax_rates/form');
        $this->layout->render();
    }

    /**
     * Deletes a tax rate from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_tax_rates->delete($id);
        redirect('tax_rates');
    }
}
