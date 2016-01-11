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
    public $table = 'ip_products';
    public $primary_key = 'ip_products.product_id';

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
        $this->db->order_by('ip_families.family_name, ip_products.product_name');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('ip_families', 'ip_families.family_id = ip_products.family_id', 'left');
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_products.tax_rate_id', 'left');
    }

    /**
     * Query to get something by the product name
     * @param $match
     */
    public function by_product($match)
    {
        $this->db->like('product_sku', $match);
        $this->db->or_like('product_name', $match);
        $this->db->or_like('product_description', $match);
    }

    /**
     * Returns the validation rules for products
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'product_sku' => array(
                'field' => 'product_sku',
                'label' => lang('product_sku'),
                'rules' => 'required'
            ),
            'product_name' => array(
                'field' => 'product_name',
                'label' => lang('product_name'),
                'rules' => 'required'
            ),
            'product_description' => array(
                'field' => 'product_description',
                'label' => lang('product_description'),
                'rules' => ''
            ),
            'product_price' => array(
                'field' => 'product_price',
                'label' => lang('product_price'),
                'rules' => 'required'
            ),
            'purchase_price' => array(
                'field' => 'purchase_price',
                'label' => lang('purchase_price'),
                'rules' => ''
            ),
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

        );
    }
}
