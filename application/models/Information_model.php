<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Information_model (Information Model)
 * Information model class to get and handle information-related data 
 * @author : Your Name
 * @version : 1.0
 * @since : Date
 */
class Information_model extends CI_Model
{


    // Add user information to the database
    public function addUserInformation($userInfoData)
    {
        // Assuming you have a table named 'user_information' in your database
        $this->db->insert('user_information', $userInfoData);

        // Check if the insertion was successful and return the inserted information ID
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }
    /**
     * This function is used to add new information to the system
     * @param array $infoData: An associative array of information data to add
     * @return number $insert_id: The last inserted ID
     */
  // Inside your Information_model.php file
   // Function to add new information to the database
   public function addNewInformation($informationData) {
    // Insert data into the 'tbl_information' table
    if ($this->db->insert('tbl_information', $informationData)) {
        return $this->db->insert_id();
    } else {
        return false;
    }
}
  /**
     * This function is used to add new user to system
     * @return number $insert_id : This is last inserted id
     */
    function addNewUserInfo($userInfo)
    {
        $this->db->trans_start();
        $success = $this->db->insert('tbl_info', $userInfo);
        $this->db->trans_complete();
    
        return $this->db->trans_status() ? 1 : 0;
    }
  function addInformation($infoData)
{
    $this->db->trans_start();
    $this->db->insert('tbl_information', $infoData);
    $insert_id = $this->db->insert_id();

    // Get the last executed SQL query
    $sql = $this->db->last_query();

    $this->db->trans_complete();

    return $insert_id;
}
  function addOrUpdateInformation($infoId, $infoData)
  {
      $this->db->trans_start();
  
      // Check if the ID exists in the table
      $existingRecord = $this->db->get_where('tbl_information', array('id' => $infoId))->row();
  
      if ($existingRecord) {
          // If the record exists, update it
          $this->db->where('id', $infoId);
          $this->db->set($infoData);
          $this->db->update('tbl_information');
          $insert_id = $infoId; // ID remains the same for an update
      } else {
          // If the record doesn't exist, insert it
          $this->db->insert('tbl_information', $infoData);
          $insert_id = $this->db->insert_id();
      }
  
      // Get the last executed SQL query
      $sql = $this->db->last_query();
  
      // Log the SQL query to the server's error log
      echo "<script>console.log('SQL Query: " . $sql . "');</script>";
  
      $this->db->trans_complete();
  
      return $insert_id;
  }
    // You can add more functions for retrieving and managing information data as needed.
}
