<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Products
 * @package Modules\Products\Models
 */
class Mdl_Products extends Response_Model
{
    public $table = 'products';
    public $primary_key = 'products.id';

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
        $this->db->order_by('product_families.name, products.name');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('product_families', 'product_families.id = products.family_id', 'left');
        $this->db->join('tax_rates', 'ip_tax_rates.id = products.tax_rate_id', 'left');
    }

    /**
     * Query to get something by the product name
     * @param $match
     */
    public function by_product($match)
    {
        $this->db->like('sku', $match);
        $this->db->or_like('name', $match);
        $this->db->or_like('description', $match);
    }

    /**
     * Returns the validation rules for products
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'family_id' => array(
                'field' => 'family_id',
                'label' => lang('family'),
                'rules' => 'numeric'
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
                'rules' => 'numeric'
            ),
            'sku' => array(
                'field' => 'sku',
                'label' => lang('product_sku'),
                'rules' => 'required'
            ),
            'name' => array(
                'field' => 'name',
                'label' => lang('product_name'),
                'rules' => 'required'
            ),
            'description' => array(
                'field' => 'description',
                'label' => lang('product_description'),
                'rules' => ''
            ),
            'price' => array(
                'field' => 'price',
                'label' => lang('product_price'),
                'rules' => 'required'
            ),
            'purchase_price' => array(
                'field' => 'purchase_price',
                'label' => lang('purchase_price'),
                'rules' => ''
            ),
        );
    }
}
