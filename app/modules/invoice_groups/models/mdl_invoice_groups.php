<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Invoice_Groups
 * @package Modules\InvoiceGroups\Models
 *
 * @property CI_DB_query_builder $db
 */
class Mdl_Invoice_Groups extends Response_Model
{
    public $table = 'invoice_groups';
    public $primary_key = 'invoice_groups.id';

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
        $this->db->order_by('invoice_groups.name');
    }

    /**
     * Returns the validation rules for invoice groups
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'name' => array(
                'field' => 'name',
                'label' => lang('name'),
                'rules' => 'required'
            ),
            'identifier_format' => array(
                'field' => 'identifier_format',
                'label' => lang('identifier_format'),
                'rules' => 'required'
            ),
            'next_id' => array(
                'field' => 'next_id',
                'label' => lang('next_id'),
                'rules' => 'required'
            ),
            'left_pad' => array(
                'field' => 'left_pad',
                'label' => lang('left_pad'),
                'rules' => 'required'
            )
        );
    }

    /**
     * Generates an invoice number based on the given invoice group ID
     * @param $invoice_group_id
     * @param bool $set_next
     * @return string
     */
    public function generate_invoice_number($invoice_group_id, $set_next = true)
    {
        $invoice_group = $this->get_by_id($invoice_group_id);

        $invoice_identifier = $this->parse_identifier_format(
            $invoice_group->identifier_format,
            $invoice_group->next_id,
            $invoice_group->left_pad
        );

        if ($set_next) {
            $this->set_next_invoice_number($invoice_group_id);
        }

        return $invoice_identifier;
    }

    /**
     * Returns the parsed format for the invoice number
     * @param $identifier_format
     * @param $next_id
     * @param $left_pad
     * @return string
     */
    private function parse_identifier_format($identifier_format, $next_id, $left_pad)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $identifier_format, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'year':
                        $replace = date('Y');
                        break;
                    case 'month':
                        $replace = date('m');
                        break;
                    case 'day':
                        $replace = date('d');
                        break;
                    case 'id':
                        $replace = str_pad($next_id, $left_pad, '0', STR_PAD_LEFT);
                        break;
                    default:
                        $replace = '';
                }

                $identifier_format = str_replace('{{{' . $var . '}}}', $replace, $identifier_format);
            }
        }

        return $identifier_format;
    }

    /**
     * Sets the next invoice number for the given invoice group ID
     * @param $invoice_group_id
     */
    public function set_next_invoice_number($invoice_group_id)
    {
        $this->db->where($this->primary_key, $invoice_group_id);
        $this->db->set('next_id', 'next_id+1', false);
        $this->db->update($this->table);
    }
}
