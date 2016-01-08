<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Ajax
 * @package Modules\Clients\Controllers
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    /**
     * Returns all clients that match the given client name
     * @uses $_POST['query']
     */
    public function name_query()
    {
        // Load the model
        $this->load->model('clients/mdl_clients');

        // Get the post input
        $query = $this->input->post('query');

        $clients = $this->mdl_clients->select('client_name')
            ->like('client_name', $query)
            ->order_by('client_name')
            ->get(array(), false)->result();

        $response = array();

        foreach ($clients as $client) {
            $response[] = $client->client_name;
        }

        echo json_encode($response);
    }

    /**
     * Saves a note to a given client ID and returns the result
     * @uses $_POST['client_id']
     * @uses $_POST['client_note']
     */
    public function save_client_note()
    {
        $this->load->model('clients/mdl_client_notes');

        if ($this->mdl_client_notes->run_validation()) {
            $this->mdl_client_notes->save();

            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }

        echo json_encode($response);
    }

    /**
     * Returns all notes that are saved for a given client ID
     * @uses $_POST['client_id']
     */
    public function load_client_notes()
    {
        $this->load->model('clients/mdl_client_notes');

        $data = array(
            'client_notes' => $this->mdl_client_notes
                ->where('client_id', $this->input->post('client_id'))
                ->get()->result(),
        );

        $this->layout->load_view('clients/partial_notes', $data);
    }

}
