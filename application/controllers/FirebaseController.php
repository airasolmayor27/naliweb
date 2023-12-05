<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Kreait\Firebase\Database;
use Kreait\Firebase\Factory;
class FirebaseController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Task_model', 'tm');
       
        $this->module = 'Task';
        // Load Firebase libraries and set up the configuration
        require_once APPPATH . '../vendor/autoload.php'; // Update the path accordingly
    
        // Use the Factory class to create a Firebase instance and load the service account
        $factory = (new Kreait\Firebase\Factory);
       // $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/application/config/firebase_credentials.json';
     

        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . '/nali/application/config/firebase_credentials.json';
     


        $factory = (new Factory)->withServiceAccount($absolutePath)
        ->withDatabaseUri('https://nali-ab2d7-default-rtdb.firebaseio.com/');

        $this->database = $factory->createDatabase();
    }
    

    public function retrieveData() {
        $path = 'emer_data';
        $insertedData = array();
        
        try {
            $reference = $this->database->getReference($path);
            $snapshot = $reference->getSnapshot();
            $data = $snapshot->getValue();
    
            // Check if data is not empty and is an array
            if (!empty($data) && is_array($data)) {
                // Check if lat and lon keys exist
                if (isset($data['lat']) && isset($data['lon'])) {
                    $lat = $data['lat'];
                    $lon = $data['lon'];
    
                    // Make a request to the geocode endpoint
                    $geocodeEndpoint = "https://geocode.maps.co/reverse?lat={$lat}&lon={$lon}";
                    $geocodeResponse = file_get_contents($geocodeEndpoint);
    
                    if ($geocodeResponse !== false) {
                        $geocodeData = json_decode($geocodeResponse, true);
                            
                   
                        // Check if display_name and address keys exist
                        if (isset($geocodeData['display_name']) && isset($geocodeData['address'])) {
                          
                            $userInformation = $this->tm->getTaskUserInformation($data['device_id']);
                            $data['taskTitle'] = !empty($userInformation->name) ? $userInformation->name : 'No Data Found';
                            
                            // Add or update the "updatedDtm" key with lat,lon value
                            $data['link'] = 'https://www.google.com/maps?q=' . $lat . ',' . $lon;
                            $data['location'] = $geocodeData['display_name'];

                            $locationParts = explode(', ', $data['location']);

                            // Extract the city (assuming it's the first part after splitting)
                            $city = $locationParts[1];

                            // Add a conditional check for specific cities
                            if ($city === 'Malolos' || $city === 'Guiguinto') {
                                // Your code for Malolos or Guiguinto
                                // For example:
                                if ($city === 'Malolos') {
                                    $data['town']=$geocodeData['address']['city'];
                                } elseif ($city === 'Guiguinto') {
                                    $data['town']=$geocodeData['address']['town'];
                                }
                            } else {
                                // Code for other cities
                            }
                            
                            $data['updatedDtm'] = date('Y-m-d H:i:s');
                            // Call the addNewTask method with the modified data
                            $insertedData = $this->tm->addNewTask($data);
                            if ($insertedData['device_id']) {
                                $response = array(
                                    'message' => 'Data saved successfully',
                                    'data' => array(
                                        'ID' => $insertedData['device_id'],
                                        'link' => $insertedData['link'],
                                        'location' => $insertedData['location'],
                                        'town' =>$insertedData['town'], 
                                       
                                    )
                                );
                            } else {
                                log_message('error', 'Failed to save data to the local database.');
                                $response = array(
                                    'message' => 'Failed to save data'
                                );
                            }
    
                            // Output the response as JSON
                            echo json_encode($response);
                        } else {
                            echo 'No valid display_name and address data found in geocode response.';
                        }
                    } else {
                        echo 'Failed to retrieve geocode data.';
                    }
                } else {
                    echo 'No valid lat and lon data found.';
                }
            } else {
                echo 'No valid data found.';
            }
        } catch (Kreait\Firebase\Exception\DatabaseException $e) {
            // Log the error instead of echoing directly
            log_message('error', 'Firebase database error: ' . $e->getMessage());
            echo 'Error: Failed to retrieve data from Firebase.';
        }
    }
    
    
    
    
    
}
