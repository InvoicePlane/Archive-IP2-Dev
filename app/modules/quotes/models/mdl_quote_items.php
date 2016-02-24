<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Quote_Items
 * @package Modules\Quotes\Models
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Quote_Amounts $mdl_quote_amounts
 * @property Mdl_Quote_Item_Amounts $mdl_quote_item_amounts
 */
class Mdl_Quote_Items extends Response_Model
{
    public $table = 'quote_items';
    public $primary_key = 'quote_items.id';
    public $date_created_field = 'date_created';

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('quote_item_amounts.*, quote_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    /**
     * The default order by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('quote_items.item_order');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('quote_item_amounts', 'quote_item_amounts.id = quote_items.item_id', 'left');
        $this->db->join('products', 'products.id = quote_items.product_id', 'left');
        $this->db->join('tax_rates', 'tax_rates.id = quote_items.tax_rate_id', 'left');
    }

    /**
     * Returns the validation rules for quotes items
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'quote_id' => array(
                'field' => 'quote_id',
                'label' => lang('quote'),
                'rules' => 'required',
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
            ),
            'product_id' => array(
                'field' => 'product_id',
                'label' => lang('product'),
            ),
            'name' => array(
                'field' => 'name',
                'label' => lang('name'),
                'rules' => 'required',
            ),
            'description' => array(
                'field' => 'description',
                'label' => lang('description'),
            ),
            'quantity' => array(
                'field' => 'quantity',
                'label' => lang('quantity'),
                'rules' => 'required',
            ),
            'price' => array(
                'field' => 'price',
                'label' => lang('price'),
                'rules' => 'required',
            ),
            'discount_amount' => array(
                'field' => 'discount_amount',
                'label' => lang('discount_amount'),
            ),
        );
    }

    /**
     * Saves a quote item to the database
     * @param int|null $quote_id
     * @param null $id
     * @param null $db_array
     * @return int|null
     */
    public function save($quote_id, $id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('quotes/mdl_quote_item_amounts');
        $this->mdl_quote_item_amounts->calculate($id);

        $this->load->model('quotes/mdl_quote_amounts');
        $this->mdl_quote_amounts->calculate($quote_id);

        return $id;
    }

    /**
     * Deletes a quote item from the database
     * @param $item_id
     */
    public function delete($item_id)
    {
        // Get the quote id so we can recalculate quote amounts
        $this->db->select('quote_id');
        $this->db->where('item_id', $item_id);
        $quote_id = $this->db->get('quote_items')->row()->quote_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('quote_item_amounts');

        // Recalculate quote amounts
        $this->load->model('quotes/mdl_quote_amounts');
        $this->mdl_quote_amounts->calculate($quote_id);
    }
}
