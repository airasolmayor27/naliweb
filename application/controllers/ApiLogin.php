<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ApiLogin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model'); // Load the Login_model
    }

    public function login()
    {
        $email = $this->input->post('email'); // Get email from POST data
        $password = $this->input->post('password'); // Get password from POST data
    
        // Check if email and password are provided
        if (!empty($email) && !empty($password)) {
            // Call the loginMe method from Login_model
            $user = $this->Login_model->loginMe($email, $password);
    
            if (!empty($user)) {
                // Login successful
                $response['status'] = true;
                $response['message'] = 'Login successful';
                $response['login'] = true;
                $response['user'] = $user; // Assuming $user is an associative array
            } else {
                // Login failed
                $response['status'] = false;
                $response['login'] = false;
                $response['message'] = 'Login failed. Invalid email or password.';
            }
        } else {
            // Invalid input data
            $response['status'] = false;
            $response['message'] = 'Email and password are required.';
        }
    
        // Send the response as JSON
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
    
}
