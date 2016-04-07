<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Statuses
 * @package Modules\Statuses\Models
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property CI_Session $session
 */
class Mdl_Statuses extends Response_Model
{
    public $table = 'invoices';
    public $primary_key = 'invoices.id';

    public $status_types = [
        'quotes' => [
            'quote_draft',
            'quote_open',
            'quote_closed',
        ],
        'invoices' => [
            'invoice_draft',
            'invoice_open',
            'invoice_closed',
        ],
    ];

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('statuses.id ASC');
    }

    /**
     * Returns the validation rules for statuses
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'status_name' => array(
                'field' => 'status_name',
                'label' => lang('status_name'),
                'rules' => 'required|is_unique[statuses.status_name]'
            ),
            'color' => array(
                'field' => 'status_color',
                'label' => lang('status_color'),
            ),
            'type' => array(
                'field' => 'status_type',
                'label' => lang('status_type'),
                'rules' => 'required'
            ),
        );
    }
}
