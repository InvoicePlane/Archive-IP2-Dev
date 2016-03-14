<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Clients_Ajax
 * @package Modules\Clients\Controllers
 *
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Clients $mdl_clients
 * @property Mdl_Client_Notes $mdl_client_notes
 */
class Clients_Ajax extends User_Controller
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
}
