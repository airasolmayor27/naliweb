<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : Clients (ClientsController)
 * Clients Class to control all client related operations.
 * @author : Your Name
 * @version : 1.0
 * @since : Your Start Date
 */
class ClientsController extends BaseController
{
    /**
     * This is the default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
        $this->isLoggedIn();
    }

    /**
     * This function is used to load the list of clients
     */
    public function index()
    {
        $this->global['pageTitle'] = 'NIPA : Dashboard';
        
        $this->loadViews("general/dashboard", $this->global, NULL , NULL);
    }
    /**
     * This function is used to load the user list
     */
    function clientsListing()
    {
        if(!$this->isAdmin())
        {
            $this->loadThis();
        }
        else
        {        
            $searchText = '';
            if(!empty($this->input->post('searchText'))) {
                $searchText = $this->security->xss_clean($this->input->post('searchText'));
            }
            $data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->clients_model->clientsListingCount($searchText);

			$returns = $this->paginationCompress ( "clients/list/", $count, 10 );
            
            $data['userRecords'] = $this->clients_model->clientsListing($searchText, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'NALI : Client Listing';
            
            $this->loadViews("clients/clients", $this->global, $data, NULL);
        }
    }
    /**
     * This function is used to add a new client
     */
    public function addNew()
    {
        // Your code to add a new client goes here.
    }

    /**
     * This function is used to edit an existing client
     * @param int $clientId : Client ID to edit
     */
    public function editClient($clientId)
    {
        // Your code to edit an existing client goes here.
    }

    /**
     * This function is used to delete a client by client ID
     * @param int $clientId : Client ID to delete
     */
    public function deleteClient($clientId)
    {
        // Your code to delete a client goes here.
    }

    /**
     * Page not found: error 404
     */
    public function pageNotFound()
    {
        $this->global['pageTitle'] = 'NALI : 404 - Page Not Found';

        $this->loadViews("general/404", $this->global, NULL, NULL);
    }

    // Add more functions as needed for client-related operations.
}

/* End of file Clients.php */
/* Location: application/controllers/Clients.php */
