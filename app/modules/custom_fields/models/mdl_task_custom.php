<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Task_Custom
 * @package Modules\CustomFields\Models
 */
class Mdl_Task_Custom extends MY_Model
{
    public $table = 'custom_task';
    public $primary_key = 'custom_task.id';

    /**
     * Saves a custom field for users to the database
     * @param $task_id
     * @param $db_array
     */
    public function save_custom($task_id, $db_array)
    {
        $task_custom_id = null;

        $db_array['task_id'] = $task_id;

        $task_custom = $this->where('user_id', $task_id)->get();

        if ($task_custom->num_rows()) {
            $task_custom_id = $task_custom->row()->id;
        }

        parent::save($task_custom_id, $db_array);
    }

}
