<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_User_Custom
 * @package Modules\Custom_Fields\Models
 */
class Mdl_User_Custom extends MY_Model
{
    public $table = 'ip_user_custom';
    public $primary_key = 'ip_user_custom.user_custom_id';

    /**
     * Saves a custom field for users to the database
     * @param $user_id
     * @param $db_array
     */
    public function save_custom($user_id, $db_array)
    {
        $user_custom_id = null;

        $db_array['user_id'] = $user_id;

        $user_custom = $this->where('user_id', $user_id)->get();

        if ($user_custom->num_rows()) {
            $user_custom_id = $user_custom->row()->user_custom_id;
        }

        parent::save($user_custom_id, $db_array);
    }

}
