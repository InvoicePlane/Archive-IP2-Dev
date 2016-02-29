<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Versions
 * @package Modules\Settings\Models
 *
 * @property CI_DB_query_builder $db
 */
class Mdl_Versions extends Response_Model
{
    public $table = 'versions';
    public $primary_key = 'versions.id';

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
        $this->db->order_by('versions.date_applied DESC, versions.file DESC');
    }
}
