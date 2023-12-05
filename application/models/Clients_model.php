<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients_model extends CI_Model {


    public function get_data() {
        // Your database queries here
        $query = $this->db->get('tbl_information');
        return $query->result();
    }

    // Add more functions for database operations as needed

 /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @return number $count : This is row count
     */
    function clientsListingCount($searchText)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.isAdmin, BaseTbl.createdDtm, Role.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId','left');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        // $this->db->where('BaseTbl.roleId !=', 1);
        $query = $this->db->get();
        
        return $query->num_rows();
    }
       
    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function clientsListing($searchText, $page, $segment)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile,Information.city_muni, BaseTbl.isAdmin, BaseTbl.createdDtm, Role.role, Role.status as roleStatus, Information.user_id, Information.emer_contact, Information.verified, Information.contact_person');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId', 'left');
        $this->db->join('tbl_information as Information', 'Information.user_id = BaseTbl.userId', 'left');
        
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email LIKE '%" . $searchText . "%' OR BaseTbl.name LIKE '%" . $searchText . "%' OR BaseTbl.mobile LIKE '%" . $searchText . "%')";
            $this->db->where($likeCriteria);
        }
        
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->where('BaseTbl.roleId', 15); // Add this line to filter by roleId = 15
        $this->db->order_by('BaseTbl.userId', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;
        
        
    }

      
    /**
     * This function is used to add new user to system
     * @return number $insert_id : This is last inserted id
     */
    function addClientInformation($clientInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_information', $clientInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }

        /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
   function getClientInfo($userId)
{
    $this->db->select("user_id, verified, emer_contact, contact_address, city_muni, blood_type, contact_person");
    $this->db->from("tbl_information");
    $this->db->where("user_id", $userId);   
  
    $query = $this->db->get();

    return $query->result();
}


function checkUserExists($userId)
{
    $this->db->select("user_id, verified, emer_contact, contact_address, city_muni, blood_type, contact_person");
    $this->db->from("tbl_information");
    $this->db->where("user_id", $userId);   
  
    $query = $this->db->get();

    return $query->result();
}   

}