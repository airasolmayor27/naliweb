<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class ESPController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load any necessary models or libraries here
    }

    public function getData() {
        // Handle HTTP GET requests from ESP32
        $data = $this->input->get(); // Access GET parameters
        // Process the data as needed
        print_r($data);
    }

    public function postData() {
        // Handle HTTP POST requests from ESP32
        $data = $this->input->post(); // Access POST parameters
        // Process the data as needed
        print_r($data);
    }
}
