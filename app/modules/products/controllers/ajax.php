<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Products_Ajax
 * @package Modules\Products\Controllers
 * @property Layout $layout
 * @property Mdl_Products $mdl_products
 * @property Mdl_Product_Families $mdl_product_families
 */
class Products_Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Returns the modal that can be used to select a product
     */
    public function modal_product_lookups()
    {
        //$filter_family  = $this->input->get('filter_family');
        $filter_product = $this->input->get('filter_product');

        $this->load->model('mdl_products');
        $this->load->model('product_families/mdl_product_families');

        // Apply filters
        /*
        if((int)$filter_family) {
            $products = $this->mdl_products->by_family($filter_family);
        }
        */

        if (!empty($filter_product)) {
            $this->mdl_products->by_product($filter_product);
        }
        $this->mdl_products->get();
        $products = $this->mdl_products->result();

        $families = $this->mdl_product_families->get()->result();

        $data = array(
            'products' => $products,
            'families' => $families,
            'filter_product' => $filter_product,
            //'filter_family'  => $filter_family,
        );

        $this->layout->load_view('products/modal_product_lookups', $data);
    }

    /**
     * Returns all products based on the given IDs
     * @uses array $_POST['product_ids']
     */
    public function process_product_selections()
    {
        $this->load->model('mdl_products');

        $products = $this->mdl_products->where_in('id', $this->input->post('product_ids'))->get()->result();

        foreach ($products as $product) {
            $product->product_price = format_amount($product->product_price, $this->mdl_settings->setting('item_price_decimal_places'));
        }

        echo json_encode($products);
    }
}
