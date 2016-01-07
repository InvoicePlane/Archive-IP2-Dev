<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Response_Model extends Form_Validation_Model
{

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

    public function delete($id)
    {
        parent::delete($id);

        $this->session->set_flashdata('alert_success', lang('record_successfully_deleted'));
    }

}

?>