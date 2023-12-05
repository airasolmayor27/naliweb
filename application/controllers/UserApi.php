<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserApi extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Load the User_model
        $this->load->model('User_model');
        $this->load->model('Task_model', 'tm');
       
        $this->module = 'Task';
    }

    public function addUser()
    {
        // Check if the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the registration data from the POST request
            $userData = $this->input->post();

            // You may want to perform validation on the user data here
            // For example, check if the email already exists
            $existingEmail = $this->User_model->checkEmailExists($userData['email']);

            if (!empty($existingEmail)) {
                // Email already exists, return an error response
                $response = array(
                    'status' => 'false',
                    'message' => 'Email already exists'
                );
                $this->output
                    ->set_status_header(201) // Bad Request
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return; // Exit the function
            }

            // Add the new user to the database using the User_model
            $userId = $this->User_model->addNewUser($userData);

            if ($userId) {
                // Registration successful
                $response = array(
                    'status' => 'true',
                    'message' => 'User registered successfully',
                    'user_id' => $userId
                );
                $this->output
                    ->set_status_header(201)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
            } else {
                // Registration failed
                $response = array(
                    'status' => 'false',
                    'message' => 'User registration failed'
                );
                $this->output
                    ->set_status_header(201)
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
    public function addUserInformation()
{
    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the user information data from the POST request
        $userInfoData = $this->input->post();
        $user_id = $userInfoData['user_id'];
        // You may want to perform validation on the user information data here
        // For example, check if the user exists or if the data is complete

        // Add the user information to the database using the Information_model
        $infoId = $this->User_model->editUserInformation($userInfoData,$user_id);
      
        if ($infoId) {
            // User information added successfully
            $response = array(
                'status' => 'true',
                'message' => 'User information added successfully',
                'info_id' => $infoId
            );
            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Adding user information failed
            $response = array(
                'status' => 'false',
                'message' => 'User information addition failed'
            );
            $this->output
                ->set_status_header(201)
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

public function addDevice()
{
    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the registration data from the POST request
        $userData = $this->input->post() + $this->input->get();
        // You may want to perform validation on the user data here
        // For example, check if the email already exists
        $existingDevice = $this->tm->checkDeviceExists($userData['user_id']);

        if (!empty($existingDevice)) {
            // Email already exists, return an error response
            $response = array(
                'status' => 'false',
                'message' => 'Device already exists'
            );
            $this->output
                ->set_status_header(201) // Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return; // Exit the function
        }

        // Add the new user to the database using the User_model
        $userId =  $this->tm->registerDeviceID($userData);

        if ($userId) {
            // Registration successful
            $response = array(
                'status' => 'true',
                'message' => 'Device registered successfully',
                'user_id' => $userId
            );
            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Registration failed
            $response = array(
                'status' => 'false',
                'message' => 'Device registration failed'
            );
            $this->output
                ->set_status_header(201)
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


public function checkRelative()
{
    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the registration data from the POST request
        $userData = $this->input->post() + $this->input->get();
        // You may want to perform validation on the user data here
        // For example, check if the email already exists
        $existingDevice = $this->tm->checkRelativeExists($userData['relative_id']);

        if (!empty($existingDevice)) {
            // Email already exists, return an error response
            $response = array(
                'status' => 'true',
                'message' => 'Relative already exists'
            );
            $this->output
                ->set_status_header(201) // Bad Request
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
            return; // Exit the function
        }else {
            // Registration failed
            $response = array(
                'status' => 'false',
                'message' => 'No Relative ID found'
            );
            $this->output
                ->set_status_header(201)
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

public function addRelative()
{
    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the registration data from the POST request
        $userData = $this->input->post() + $this->input->get();
        // You may want to perform validation on the user data here
        // For example, check if the email already exists
        $existingDevice = $this->tm->checkDevice($userData['device_id']);

        if (!empty($existingDevice)) {
           // Add the new user to the database using the User_model
        $userId =  $this->tm->registerRelativeID($userData['device_id'],$userData);

        if ($userId) {
            // Registration successful
            $response = array(
                'status' => 'true',
                'message' => 'Relative added successfully',
            );
            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Registration failed
            $response = array(
                'status' => 'false',
                'message' => 'Relative registration failed'
            );
            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
        } else {
          
            $response = array(
                'status' => 'true',
                'message' => 'Device not exists'
            );
            $this->output
                ->set_status_header(200)
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



public function checkDevice()
{
    // Check if the request is a POST request
     // Check if the request is a POST request
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the user information data from the POST request
        $userInfoData = $this->input->post();
        $user_id = $userInfoData['user_id'];
        // You may want to perform validation on the user information data here
        // For example, check if the user exists or if the data is complete

        // Add the user information to the database using the Information_model
        $infoId = $this->tm->editTask($userInfoData,$user_id);
      
        if ($infoId) {
            // User information added successfully
            $response = array(
                'status' => 'true',
                'message' => 'Task Updated',
                'info_id' => $infoId
            );
            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Adding user information failed
            $response = array(
                'status' => 'false',
                'message' => 'Failed'
            );
            $this->output
                ->set_status_header(201)
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


public function updateTask()
{
    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the device data from the POST request
        $userInfoData = $this->input->post();
        $user_id = $userInfoData['taskId'];
        // You may want to perform validation on the user information data here
        // For example, check if the user exists or if the data is complete

        // Add the user information to the database using the Information_model
        $infoId = $this->tm->editTaskInformation($userInfoData,$user_id);

        if (!empty($infoId)) {
            // Device already exists, return a success response
            $response = array(
                'status' => 'true',
                'message' => 'Device exists',
            
            );
            $this->output
                ->set_status_header(200) // OK
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Device does not exist
            $response = array(
                'status' => 'false',
                'message' => 'Device does not exist'
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


public function listoftask()
{
    // Check if the request is a GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get the task list data from the GET request
        $rescueParam = $this->input->get('rescue');
        $cityMuniParam = $this->input->get('city_muni'); // Add this line

        // Check if the "rescue" key exists
        if (isset($rescueParam)) {
            // Call the listoftask function from Task_model with both parameters
            $tasks = $this->tm->taskRescue($rescueParam, $cityMuniParam); // Pass $cityMuniParam as the second parameter

            $response = array(
                'status' => 'true',
                'message' => 'Task list retrieved successfully',
                'tasks' => $tasks
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Handle the case where "rescue" key is not present in the GET request
            $response = array(
                'status' => 'false',
                'message' => 'Parameter "rescue" is missing'
            );
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    } else {
        // Handle invalid request method (e.g., POST)
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



public function listofrelative()
{
    // Check if the request is a GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get the task list data from the GET request
        $rescueParam = $this->input->get('relative_id');
    
        // Check if the "rescue" key exists
        if (isset($rescueParam)) {
            // Call the listoftask function from Task_model with both parameters
            $tasks = $this->tm->taskforRelatives($rescueParam); // Pass $cityMuniParam as the second parameter

            $response = array(
                'status' => 'true',
                'message' => 'Task list retrieved successfully',
                'tasks' => $tasks
            );

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Handle the case where "rescue" key is not present in the GET request
            $response = array(
                'status' => 'false',
                'message' => 'Parameter "rescue" is missing'
            );
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    } else {
        // Handle invalid request method (e.g., POST)
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