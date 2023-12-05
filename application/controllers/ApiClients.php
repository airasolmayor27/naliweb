<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiClients extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Clients_model'); // Load the Login_model
    }

 
public function check()
{
    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the device data from the POST request
        $deviceData = $this->input->post() + $this->input->get();
        // You may want to perform validation on the device data here

        // Check if the device already exists
        $existingDevice = $this->Clients_model->checkUserExists($deviceData['user_id']);

        if (!empty($existingDevice)) {
            // Device already exists, return a success response
            $response = array(
                'status' => 'true',
                'message' => 'User exists',
                'result' => $existingDevice
            );
            $this->output
                ->set_status_header(200) // OK
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Device does not exist
            $response = array(
                'status' => 'false',
                'message' => 'User does not exist'
            );
            $this->output
                ->set_status_header(404) // Not Found
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    } else {
        // Handle invalid request method (e.g., GET)
        $response = array(
            'status' => 'false',
            'message' => 'Invalid request method'
        );
        $this->output
            ->set_status_header(405)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
    
}
