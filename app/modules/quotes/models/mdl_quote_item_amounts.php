<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Quote_Item_Amounts
 * @package Modules\Quotes\Models
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Quote_Items $mdl_quote_items
 */
class Mdl_Quote_Item_Amounts extends CI_Model
{
    /**
     * Calculates the amounts for an item
     * 
     * * item_amount_id
     * * item_id
     * * item_subtotal = item_quantity * item_price
     * * item_tax_total
     * * item_total = (item_quantity * item_price) + item_tax_total
     * 
     * @param $item_id
     */
    public function calculate($item_id)
    {
        $this->load->model('quotes/mdl_quote_items');
        $item = $this->mdl_quote_items->get_by_id($item_id);

        $item_price = $item->price;
        $item_subtotal = $item->quantity * $item_price;
        $item_tax_total = $item_subtotal * ($item->tax_rate_percent / 100);
        $item_discount_total = $item->discount_amount * $item->quantity;
        $item_total = $item_subtotal + $item_tax_total - $item_discount_total;

        $db_array = array(
            'id' => $item_id,
            'subtotal' => $item_subtotal,
            'tax_total' => $item_tax_total,
            'discount' => $item_discount_total,
            'total' => $item_total,
        );

        $this->db->where('item_id', $item_id);
        if ($this->db->get('quote_item_amounts')->num_rows()) {
            $this->db->where('item_id', $item_id);
            $this->db->update('quote_item_amounts', $db_array);
        } else {
            $this->db->insert('quote_item_amounts', $db_array);
        }
    }
}
