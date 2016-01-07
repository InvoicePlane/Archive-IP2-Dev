<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class Response_Model
 * @package Core
 */
class Response_Model extends Form_Validation_Model
{
    /**
     * @param int $id
     * @param array $db_array
     * @return int
     */
    public function save($id = NULL, $db_array = NULL)
    {

        if ($id) {
            $this->session->set_flashdata('alert_success', lang('record_successfully_updated'));
            parent::save($id, $db_array);
        } else {
            $this->session->set_flashdata('alert_success', lang('record_successfully_created'));
            $id = parent::save(NULL, $db_array);
        }

        return $id;
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->session->set_flashdata('alert_success', lang('record_successfully_deleted'));
    }

}
