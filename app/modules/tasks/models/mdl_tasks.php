<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Tasks
 * @package Modules\Tasks\Models
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 */
class Mdl_Tasks extends Response_Model
{
    public $table = 'tasks';
    public $primary_key = 'tasks.id';
    public $date_created_field = 'date_created';
    public $date_modified_field = 'date_modified';

    /**
     * The default order directive used in every query
     */
    public function default_select()
    {
        $this->db->select('
            SQL_CALC_FOUND_ROWS *,
            (CASE WHEN DATEDIFF(NOW(), finish_date) > 0 THEN 1 ELSE 0 END)
            is_overdue
        ', false);
    }

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('projects.project_name, tasks.title');
    }

    /**
     * The default order directive used in every query
     */
    public function default_join()
    {
        $this->db->join('projects', 'projects.id = tasks.project_id', 'left');
    }

    /**
     * Query to get a task by its name
     * @param $match
     */
    public function by_task($match)
    {
        $this->db->like('title', $match);
        $this->db->or_like('description', $match);
    }

    /**
     * Returns the validation rules for tasks
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'project_id' => array(
                'field' => 'project_id',
                'label' => lang('task_project'),
                'rules' => 'required',
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate'),
            ),
            'name' => array(
                'field' => 'name',
                'label' => lang('task_name'),
                'rules' => 'required',
            ),
            'description' => array(
                'field' => 'description',
                'label' => lang('task_description'),
            ),
            'price' => array(
                'field' => 'price',
                'label' => lang('task_price'),
                'rules' => 'required',
            ),
            'finish_date' => array(
                'field' => 'finish_date',
                'label' => lang('task_finish_date'),
                'rules' => 'required',
            ),
            'status' => array(
                'field' => 'status',
                'label' => lang('task_status'),
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

        $db_array['finish_date'] = date_to_mysql($db_array['finish_date']);
        $db_array['price'] = standardize_amount($db_array['price']);

        return $db_array;
    }

    /**
     * Prepares the form with a date and price
     * @param null $id
     * @return bool
     */
    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('finish_date', date('Y-m-d'));
            parent::set_form_value('price', $this->mdl_settings->setting('default_hourly_rate'));
        }

        return true;
    }

    /**
     * Returns an array that holds all available status codes with
     * their label and class
     * @return array
     */
    public function statuses()
    {
        return array(
            '1' => array(
                'label' => lang('not_started'),
                'class' => 'draft'
            ),
            '2' => array(
                'label' => lang('in_progress'),
                'class' => 'viewed'
            ),
            '3' => array(
                'label' => lang('complete'),
                'class' => 'sent'
            ),
            '4' => array(
                'label' => lang('invoiced'),
                'class' => 'paid'
            )
        );
    }

    /**
     * Returns all tasks that are associated with the invoice Id
     * @param $invoice_id
     * @return array
     */
    public function get_tasks_to_invoice($invoice_id)
    {
        $result = array();
        if (!$invoice_id) {
            return $result;
        }

        $query = $this->db->select($this->table . '.*, projects.project_name')
            ->from($this->table)
            ->join('projects', 'projects.id = ' . $this->table . '.project_id')
            ->join('invoices', 'invoices.client_id = projects.client_id')
            ->where('invoices.id', $invoice_id)
            ->where($this->table . '.task_status', 3)
            ->order_by($this->table . '.task_finish_date', 'asc')
            ->order_by('projects.project_name', 'asc')
            ->order_by($this->table . '.task_name', 'asc')
            ->get();
        foreach ($query->result() as $row) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Updates the status for a task with the given ID
     * @param $new_status
     * @param $task_id
     */
    public function update_status($new_status, $task_id)
    {
        $statuses_ok = $this->statuses();
        if (isset($statuses_ok[$new_status])) {
            parent::save($task_id, array('task_status' => $new_status));
        }
    }

    /**
     * Updates the status of all tasks when the corresponding invoice gets deleted
     * @param $invoice_id
     */
    public function update_on_invoice_delete($invoice_id)
    {
        if (!$invoice_id) {
            return;
        }
        $query = $this->db->select($this->table . '.*')
            ->from($this->table)
            ->join('invoice_items', 'invoice_items.task_id = ' . $this->table . '.task_id')
            ->where('invoice_items.invoice_id', $invoice_id)
            ->get();

        foreach ($query->result() as $task) {
            $this->update_status(3, $task->task_id);
        }
    }

    /**
     * Query to get all overdue tasks
     * @return $this
     */
    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);
        return $this;
    }
}
