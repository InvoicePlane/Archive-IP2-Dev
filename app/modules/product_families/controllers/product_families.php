<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Families
 * @package Modules\Product_Families\Controllers
 * @property CI_DB_query_builder $db
 * @property Layout $layout
 * @property Mdl_Product_Families $mdl_product_families
 */
class Product_Families extends User_Controller
{
    /**
     * Families constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_product_families');
    }

    /**
     * Index page, returns all product families
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_product_families->paginate(site_url('product-families/index'), $page);
        $families = $this->mdl_product_families->result();

        $this->layout->set('product-families', $families);
        $this->layout->buffer('content', 'product-families/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the product family
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('product-families');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('name') != '') {
            $check = $this->db->get_where('product_families',
                array('name' => $this->input->post('name')))->result();
            
            if (!empty($check)) {
                set_alert('danger', lang('product_family_already_exists'));
                redirect('product-families/form');
            }
        }

        if ($this->mdl_product_families->run_validation()) {
            $this->mdl_product_families->save($id);
            redirect('product-families');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_product_families->prep_form($id)) {
                show_404();
            }
            $this->mdl_product_families->set_form_value('is_update', true);
        }

        $this->layout->buffer('content', 'product-families/form');
        $this->layout->render();
    }

    /**
     * Deletes a client from the database based on the given ID
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_product_families->delete($id);
        redirect('product-families');
    }
}
