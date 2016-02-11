<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Response_Model
 * @package Core
 * @property CI_Session $session
 */
class Response_Model extends Form_Validation_Model
{
    /**
     * Save an entry to the database
     * @param int $id
     * @param array $db_array
     * @return int
     */
    public function save($id = null, $db_array = null)
    {

        if ($id) {
            $this->session->set_flashdata('alert_success', lang('record_successfully_updated'));
            parent::save($id, $db_array);
        } else {
            $this->session->set_flashdata('alert_success', lang('record_successfully_created'));
            $id = parent::save(null, $db_array);
        }

        return $id;
    }

    /**
     * Delete an entry from the database
     * @param $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->session->set_flashdata('alert_success', lang('record_successfully_deleted'));
    }

}
