<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Product_Families
 * @package Modules\Product_Families\Models
 */
class Mdl_Product_Families extends Response_Model
{
    public $table = 'families';
    public $primary_key = 'families.id';

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
        $this->db->order_by('families.name');
    }

    /**
     * Returns the validation rules for product families
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'name' => array(
                'field' => 'name',
                'label' => lang('product_family_name'),
                'rules' => 'required'
            )
        );
    }
}
