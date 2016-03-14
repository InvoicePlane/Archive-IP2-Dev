<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Notes_Ajax
 * @package Modules\Clients\Controllers
 *
 * @property CI_Loader $load
 * @property CI_Session $session
 * @property Layout $layout
 * @property Mdl_Notes $mdl_notes
 */
class Notes_Ajax extends User_Controller {

    public $ajax_controller = true;

    /**
     * Saves a note to a given client ID and returns the result
     * @uses $_POST['type']
     * @uses $_POST['type_id']
     */
    public function save_note()
    {
        $this->load->model('notes/mdl_notes');

        $type = $this->input->post('type');

        if (in_array($type, $this->mdl_notes->available_types)) {

            // Prepare post for form validation
            $_POST[$type . '_id'] = $this->input->post('type_id');
            $_POST['user_id'] = $this->session->user['id'];

            if ($this->mdl_notes->run_validation()) {
                $this->mdl_notes->save();

                $response = array(
                    'success' => true,
                );
            } else {
                $this->load->helper('json_error');
                $response = array(
                    'success' => false,
                    'validation_errors' => json_errors()
                );
            }

        } else {
            $response = array(
                'success' => true,
            );
        }

        echo json_encode($response);
    }

    /**
     * Returns all notes that are saved for a given type and id
     * @uses $_POST['type']
     * @uses $_POST['type_id']
     */
    public function get_notes()
    {
        $this->load->model('notes/mdl_notes');

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');

        $data = array(
            'notes' => $this->mdl_notes
                ->where($type . '_id', $type_id)
                ->get()->result(),
        );

        $this->layout->load_view('notes/partial_notes', $data);
    }

    /**
     * Saves a note to a given client ID and returns the result
     * @uses $_POST['user_id']
     * @uses $_POST['note_id']
     */
    public function delete_note()
    {
        $this->load->model('notes/mdl_notes');

        $user_id = $this->session->user['id'];
        $note_id = $this->input->post('note_id');
        $note_user_id = $this->input->post('user_id');

        if (user_is_admin() || check_permission('delete_notes')) {

            if (($user_id != $note_user_id && check_permission('notes_delete_all'))
                || ($user_id === $note_user_id && check_permission('notes_delete_own'))) {

                $this->mdl_notes->delete($note_id);

                $response = array(
                    'success' => true,
                );

            } else {

                $response = array(
                    'success' => false,
                );

            }

        } else {

            $response = array(
                'success' => false,
            );

        }

        echo json_encode($response);
    }
}