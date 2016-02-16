<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Invoices_Recurring
 * @package Modules\Invoices\Models
 */
class Mdl_Invoices_Recurring extends Response_Model
{
    public $table = 'invoices_recurring';
    public $primary_key = 'invoices_recurring.id';
    public $frequencies = array(
        '7D' => 'calendar_week',
        '1M' => 'calendar_month',
        '1Y' => 'year',
        '3M' => 'quarter',
        '6M' => 'six_months'
    );

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select("SQL_CALC_FOUND_ROWS invoices.*,
            clients.name,
            invoices_recurring.*,
            IF(end_date > date(NOW()) OR end_date = '0000-00-00', 'active', 'inactive') AS status",
            false);
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('invoices', 'invoices.id = invoices_recurring.invoice_id');
        $this->db->join('clients', 'clients.id = invoices.client_id');
    }

    /**
     * Returns the validation rules for recurring invoices
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'rules' => 'required'
            ),
            'email_template_id' => array(
                'field' => 'email_template_id',
                'label' => lang('email_template'),
            ),
            'start_date' => array(
                'field' => 'start_date',
                'label' => lang('start_date'),
                'rules' => 'required'
            ),
            'end_date' => array(
                'field' => 'end_date',
                'label' => lang('end_date')
            ),
            'frequency' => array(
                'field' => 'frequency',
                'label' => lang('every'),
                'rules' => 'required'
            ),
            'next_date' => array(
                'field' => 'next_date',
                'label' => lang('next_date'),
            ),
            'invoices_due_after' => array(
                    'field' => 'invoices_due_after',
                    'label' => lang('invoices_due_after'),
                    'rules' => 'required'
            ),
        );
    }

    /**
     * Returns the prepared database array
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['start_date'] = date('Y-m-d');
        $db_array['next_date'] = $db_array['start_date'];

        if ($db_array['end_date']) {
            $db_array['end_date'] = date_to_mysql($db_array['end_date']);
        } else {
            $db_array['end_date'] = '0000-00-00';
        }

        return $db_array;
    }

    /**
     * Stop a recurring invoice
     * @param $id
     */
    public function stop($id)
    {
        $db_array = array(
            'end_date' => date('Y-m-d'),
            'next_date' => '0000-00-00'
        );

        $this->db->where('id', $id);
        $this->db->update('invoices_recurring', $db_array);
    }

    /**
     * Query to get the current active invoices
     * @return \Mdl_Invoices_Recurring
     */
    public function active()
    {
        $this->filter_where("next_date <= date(NOW()) AND (end_date > date(NOW()) OR end_date = '0000-00-00')");
        return $this;
    }

    /**
     * Sets the next recurring date for a recurring invoice
     * @param $id
     */
    public function set_next_date($id)
    {
        $invoice_recurring = $this->where('id', $id)->get()->row();

        $next_date = increment_date($invoice_recurring->next_date, $invoice_recurring->frequency);

        $db_array = array(
            'next_date' => $next_date
        );

        $this->db->where('id', $id);
        $this->db->update('invoices_recurring', $db_array);
    }
}
