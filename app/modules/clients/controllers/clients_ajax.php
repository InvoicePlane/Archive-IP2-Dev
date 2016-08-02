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
 */
class Clients_Ajax extends User_Controller
{
    public $ajax_controller = true;

    /**
     * Returns all clients that match the given client name
     * @uses $_POST['client_name']
     */
    public function name_query()
    {
        // Load the model
        $this->load->model('clients/mdl_clients');

        $client_name = $this->input->post('client_name');

        if (!$client_name) {
            echo json_encode([]);
            exit;
        }

        $clients = $this->mdl_clients
            ->select('clients.id, clients.name')
            ->like('clients.name', $client_name)
            ->get()->result_array();

        if (empty($clients)) {
            echo json_encode([]);
            exit;
        }

        echo json_encode($clients);
    }
}
