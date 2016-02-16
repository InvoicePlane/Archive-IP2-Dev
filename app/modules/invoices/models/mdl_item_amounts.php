<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Amounts
 * @package Modules\Invoices\Models
 * @property CI_DB_query_builder $db
 * @property Mdl_Items $mdl_items
 */
class Mdl_Amounts extends CI_Model
{
    /**
     * Calculates the amounts for an item
     * 
     * * amount_id
     * * id
     * * subtotal = quantity * price
     * * tax_total
     * * total = (quantity * price) + tax_total
     * 
     * @param $id
     */
    public function calculate($id)
    {
        $this->load->model('invoices/mdl_items');
        $item = $this->mdl_items->get_by_id($id);

        $subtotal = $item->quantity * $item->price;
        $tax_total = $subtotal * ($item->tax_rate_percent / 100);
        $discount_total = $item->discount_amount * $item->quantity;
        $total = $subtotal + $tax_total - $discount_total;

        $db_array = array(
            'id' => $id,
            'subtotal' => $subtotal,
            'tax_total' => $tax_total,
            'discount' => $discount_total,
            'total' => $total
        );

        $this->db->where('id', $id);
        if ($this->db->get('invoice_amounts')->num_rows()) {
            $this->db->where('id', $id);
            $this->db->update('invoice_amounts', $db_array);
        } else {
            $this->db->insert('invoice_amounts', $db_array);
        }
    }
}
