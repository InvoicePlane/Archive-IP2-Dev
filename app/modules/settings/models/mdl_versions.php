<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Versions
 * @package Modules\Settings\Models
 */
class Mdl_Versions extends Response_Model
{
    public $table = 'ip_versions';
    public $primary_key = 'ip_versions.version_id';

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
        $this->db->order_by('ip_versions.version_date_applied DESC, ip_versions.version_file DESC');
    }
}
