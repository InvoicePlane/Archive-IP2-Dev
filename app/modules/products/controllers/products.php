<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Products
 * @package Modules\Products\Controllers
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Products $mdl_products
 * @property Mdl_Product_Families $mdl_product_families
 * @property Mdl_Tax_Rates $mdl_tax_rates
 */
class Products extends Admin_Controller
{
    /**
     * Products constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_products');
    }

    /**
     * Index page, returns all products
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_products->paginate(site_url('products/index'), $page);
        $products = $this->mdl_products->result();

        $this->layout->set('products', $products);
        $this->layout->buffer('content', 'products/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the product
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('products');
        }

        if ($this->mdl_products->run_validation()) {

            // We need to use the correct decimal point for sql IPT-310
            $db_array = $this->mdl_products->db_array();
            $db_array['price'] = standardize_amount($db_array['price']);
            $db_array['purchase_price'] = standardize_amount($db_array['purchase_price']);

            $this->mdl_products->save($id, $db_array);
            redirect('products');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_products->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('product_families/mdl_product_families');
        $this->load->model('tax_rates/mdl_tax_rates');

        $this->layout->set(
            array(
                'families' => $this->mdl_product_families->get()->result(),
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
            )
        );

        $this->layout->buffer('content', 'products/form');
        $this->layout->render();
    }

    /**
     * Deletes a product from the database
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_products->delete($id);
        redirect('products');
    }
}
