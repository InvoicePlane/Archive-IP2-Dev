<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Items
 * @package Modules\Invoices\Models
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Amounts $mdl_item_amounts
 * @property Mdl_Invoice_Amounts $mdl_invoice_amounts
 */
class Mdl_Items extends Response_Model
{
    public $table = 'invoice_items';
    public $primary_key = 'invoice_items.id';
    public $date_created_field = 'date_created';

    /**
     * Returns all items for the given invoice ID
     * 
     * @TODO check occurrence and implement / mark as deprecated
     * 
     * @param $invoice_id
     * @param string $invoice_date_created
     * @return array
     */
    public function get_items_and_replace_vars($invoice_id, $invoice_date_created = 'now')
    {
        $items = array();
        $query = $this->where('invoice_id', $invoice_id)->get();

        foreach ($query->result() as $item) {
            $item->item_name = $this->parse_item($item->item_name, $invoice_date_created);
            $item->item_description = $this->parse_item($item->item_description, $invoice_date_created);
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Parses the item for specific formatting
     * 
     * @TODO check occurrence and implement / mark as deprecated
     * 
     * @param $string
     * @param $invoice_date_created
     * @return mixed
     */
    private function parse_item($string, $invoice_date_created)
    {
        if (preg_match_all(
            '/{{{(?<format>[yYmMdD])(?:(?<=[Yy])ear|(?<=[Mm])onth|(?<=[Dd])ay)(?:(?<operation>[-+])(?<amount>[1-9]+))?}}}/m',
            $string,
            $template_vars,
            PREG_SET_ORDER)) {
            try {
                $formattedDate = new DateTime($invoice_date_created);
            } catch (Exception $e) {
                // If creating a date based on the invoice_date_created isn't possible, use current date
                $formattedDate = new DateTime();
            }

            // Calculate the date first, before starting replacing the variables
            foreach ($template_vars as $var) {
                if (!isset($var['operation'], $var['amount'])) {
                    continue;
                }

                if ($var['operation'] == '-') {
                    $formattedDate->sub(new DateInterval('P' . $var['amount'] . strtoupper($var['format'])));
                } else {
                    if ($var['operation'] == '+') {
                        $formattedDate->add(new DateInterval('P' . $var['amount'] . strtoupper($var['format'])));
                    }
                }
            }

            // Let's replace all variables
            foreach ($template_vars as $var) {
                $string = str_replace($var[0], $formattedDate->format($var['format']), $string);
            }
        }

        return $string;
    }

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select('invoice_item_amounts.*, invoice_items.*, item_tax_rates.item_tax_rates AS tax_rate');
    }

    /**
     * The default oder by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('invoice_items.item_order');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('invoice_item_amounts', 'invoice_item_amounts.item_id = invoice_items.id',
            'left');
        $this->db->join('tax_rates AS item_tax_rates',
            'tax_rates.tax_rate_id = invoice_items.tax_rate_id', 'left');
    }

    /**
     * Returns the validation rules for invoice items
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate')
            ),
            'task_id' => array(
                'field' => 'task_id',
                'label' => lang('task')
            ),
            'product_id' => array(
                'field' => 'product_id',
                'label' => lang('product')
            ),
            'name' => array(
                'field' => 'name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'description' => array(
                'field' => 'description',
                'label' => lang('description')
            ),
            'quantity' => array(
                'field' => 'quantity',
                'label' => lang('quantity'),
                'rules' => 'required'
            ),
            'price' => array(
                'field' => 'price',
                'label' => lang('price'),
                'rules' => 'required'
            ),
            'discount_amount' => array(
                'field' => 'discount_amount',
                'label' => lang('discount_amount'),
            ),
        );
    }

    /**
     * Saves an invoice item to the database
     * @param int|null $invoice_id
     * @param null $id
     * @param null $db_array
     * @return int|null
     */
    public function save($invoice_id, $id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('invoices/mdl_item_amounts');
        $this->mdl_item_amounts->calculate($id);

        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        return $id;
    }

    /**
     * Deletes an invoice from the database
     * @param $id
     * @return null
     */
    public function delete($id)
    {
        // Get item:
        // the invoice id is needed to recalculate invoice amounts
        // and the task id to update status if the item refers a task
        $query = $this->db->get_where($this->table, array('id' => $id));
        
        if ($query->num_rows() == 0) {
            return null;
        }

        $row = $query->row();
        $invoice_id = $row->invoice_id;

        // Delete the item
        parent::delete($id);

        // Delete the item amounts
        $this->db->where('id', $id);
        $this->db->delete('invoice_item_amounts');

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);
        return $row;
    }
}
