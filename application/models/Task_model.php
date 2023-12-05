<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Task_model (Task Model)
 * Task model class to get to handle task related data 
 * @author : Kishor Mali
 * @version : 1.5
 * @since : 18 Jun 2022
 */
class Task_model extends CI_Model
{
    /**
     * This function is used to get the task listing count
     * @param string $searchText : This is optional search text
     * @return number $count : This is row count
     */
    function taskListingCount($searchText)
    {
        $this->db->select('BaseTbl.taskId, BaseTbl.taskTitle, BaseTbl.description,BaseTbl.type,BaseTbl.status, BaseTbl.createdDtm');
        $this->db->from('tbl_task as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.taskTitle LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    /**
     * This function is used to get the task listing count
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function taskListing($searchText, $page, $segment)
    {
        $this->db->select('BaseTbl.taskId, BaseTbl.taskTitle, BaseTbl.device_id, BaseTbl.emergency_type,BaseTbl.rescue_status, BaseTbl.location, BaseTbl.link, BaseTbl.town, BaseTbl.message, BaseTbl.description, BaseTbl.type, BaseTbl.status,BaseTbl.rescue_status, BaseTbl.createdDtm');
        $this->db->from('tbl_task as BaseTbl');
    
        if (!empty($searchText)) {
            $likeCriteria = "(BaseTbl.taskTitle LIKE '%" . $searchText . "%')";
            $this->db->where($likeCriteria);
        }
    
        $this->db->where('BaseTbl.isDeleted', 0);
       
        $this->db->order_by('BaseTbl.taskId', 'DESC');
        $this->db->limit($page, $segment);
    
        $query = $this->db->get();
        $result = $query->result();
    
        return $result;
    }
    
    
    function taskRescue($emergencyType = null, $city_muni = null) {
        $this->db->trans_start();
        $this->db->select('BaseTbl.taskId, BaseTbl.taskTitle, BaseTbl.device_id, BaseTbl.emergency_type,BaseTbl.rescue_status, BaseTbl.location,BaseTbl.town,BaseTbl.link, BaseTbl.message, BaseTbl.description, BaseTbl.type, BaseTbl.status, tbl_information.city_muni, BaseTbl.createdDtm');
        $this->db->from('tbl_task as BaseTbl');
        $this->db->where('BaseTbl.isDeleted', 0);
    
        // Add the WHERE condition for emergency_type if provided
        if ($emergencyType !== null) {
            $this->db->where('BaseTbl.emergency_type', $emergencyType);
        }
    
        // Join with tbl_nali_device on device_id
        $this->db->join('tbl_nali_device', 'BaseTbl.device_id = tbl_nali_device.device_id', 'left');
    
        // Join with tbl_information on user_id in tbl_nali_device
        $this->db->join('tbl_information', 'tbl_nali_device.user_id = tbl_information.user_id', 'left');
    
        // Add the WHERE condition for city_muni if provided
        if ($city_muni !== null) {
            $this->db->where('BaseTbl.town', $city_muni);
        }
    
        $this->db->order_by('BaseTbl.taskId', 'DESC');
        $query = $this->db->get();
       
        $result = $query->result();
        $this->db->trans_complete();
        return $result;
    }
    function taskforRelatives($relative_id) {
        $this->db->trans_start();
        $this->db->select('BaseTbl.taskId, BaseTbl.taskTitle, BaseTbl.device_id, BaseTbl.emergency_type,BaseTbl.rescue_status, BaseTbl.location,BaseTbl.town,BaseTbl.link, BaseTbl.message, BaseTbl.description, BaseTbl.type, BaseTbl.status, tbl_information.city_muni, BaseTbl.createdDtm');
        $this->db->from('tbl_task as BaseTbl');
        $this->db->where('BaseTbl.isDeleted', 0);
    
        // Join with tbl_nali_device on device_id
        $this->db->join('tbl_nali_device', 'BaseTbl.device_id = tbl_nali_device.device_id', 'left');
    
        // Join with tbl_information on user_id in tbl_nali_device
        $this->db->join('tbl_information', 'tbl_nali_device.user_id = tbl_information.user_id', 'left');
    
        // Add a condition to filter by relative_id
        $this->db->where('tbl_nali_device.relative_id', $relative_id);
    
        $this->db->order_by('BaseTbl.taskId', 'DESC');
        $query = $this->db->get();
    
        $result = $query->result();
        $this->db->trans_complete();
        return $result;
    }
    
    

    /**
     * This function is used to add new task to system
     * @return number $insert_id : This is last inserted id
     */
    public function addNewTask($taskInfo) {
        $device_id = $taskInfo['device_id'];
    
        // Check if the device_id and emergency_type already exist in the database
        $existingRecord = $this->db->get_where('tbl_task', array('device_id' => $device_id))->row_array();
    
        if ($existingRecord) {
            // If the device_id exists, get the status and emergency_type from the existing record
            $status = $existingRecord['status'] ?? null;
            $existingEmergencyType = $existingRecord['emergency_type'] ?? null;
    
            if (($status === 'DONE') || ($existingEmergencyType && $existingEmergencyType !== $taskInfo['emergency_type'])) {
                // If status is 'DONE' or emergency_type is different, delete the record
                $this->db->where('device_id', $device_id);
                $this->db->delete('tbl_task');
                return null; // Return null to indicate the record was deleted
            } else {
                // If status is not 'DONE' and emergency_type is the same, update the record excluding 'status'
                $updateData = $taskInfo;
                unset($updateData['status']); // Exclude 'status' from update
                $this->db->where('device_id', $device_id);
                $this->db->update('tbl_task', $updateData);
    
                // Retrieve 'id' from the existing record (if 'id' is present)
                $inserted_id = $existingRecord['id'] ?? null;
            }
        } else {
            // If the device_id doesn't exist, insert a new record
            $this->db->insert('tbl_task', $taskInfo);
            $inserted_id = $this->db->insert_id();
            $status = $taskInfo['status'] ?? null; // Initialize status for the new record
        }
    
        // Get additional information
        $location = $taskInfo['location'] ?? null;
        $link = $taskInfo['link'] ?? null;
        $town = $taskInfo['town'] ?? null;
    
        // Return an array with device_id, status, and location
        return array(
            'device_id' => $device_id,
            'status' => $status,
            'location' => $location,
            'link' => $link,
            'town' => $town
        );
    }
    
    

    public function updateTaskInformation ($taskId){

    }

    public function registerDeviceID($deviceInfo) {
        $this->db->trans_start();
        $this->db->insert('tbl_nali_device', $deviceInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }
    
    public function registerRelativeID($deviceID,$RelativeInfo) {
  
        $this->db->where('device_id', $deviceID);
        $this->db->update('tbl_nali_device', $RelativeInfo);
      
        
        return TRUE;
    }
    
    function checkDeviceExists($device_id)
    {
        $this->db->select("user_id,device_id");
        $this->db->from("tbl_nali_device");
        $this->db->where("user_id", $device_id);   
      
        $query = $this->db->get();

        return $query->result();
    }   

    function checkDevice($device_id)
    {
        $this->db->select("user_id,device_id");
        $this->db->from("tbl_nali_device");
        $this->db->where("device_id", $device_id);   
      
        $query = $this->db->get();

        return $query->result();
    }   

    function checkRelativeExists($relative_id)
    {
        $this->db->select("user_id,device_id");
        $this->db->from("tbl_nali_device");
        $this->db->where("relative_id", $relative_id);   
      
        $query = $this->db->get();

        return $query->result();
    }   

    

    function checkUserRole($device_id)
    {
        $this->db->select("user_id,device_id");
        $this->db->from("tbl_nali_device");
        $this->db->where("user_id", $device_id);   
      
        $query = $this->db->get();

        return $query->result();
    }   
    
    /**
     * This function used to get task information by id
     * @param number $taskId : This is task id
     * @return array $result : This is task information
     */
    function getTaskInfo($taskId)
    {
        $this->db->select('taskId, taskTitle, description');
        $this->db->from('tbl_task');
        $this->db->where('taskId', $taskId);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get();
        
        return $query->row();
    }
    
    
   
    function getTaskUserInformation($taskId)
    {
        $this->db->select('tbl_nali_device.device_id, tbl_users.userId, tbl_users.name');
        $this->db->from('tbl_nali_device');
        $this->db->join('tbl_users', 'tbl_nali_device.user_id = tbl_users.userId', 'left');
        $this->db->where('tbl_nali_device.device_id', $taskId);
    
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
         // Device not registered to any user
   
        }
    }
    
    /**
     * This function is used to update the task information
     * @param array $taskInfo : This is task updated information
     * @param number $taskId : This is task id
     */
    function editTask($taskInfo, $taskId)
    {
        $this->db->where('taskId', $taskId);
        $this->db->update('tbl_task', $taskInfo);
        
        return TRUE;
    }
    function editTaskInformation($taskInfo, $taskId)
    {
        $this->db->set($taskInfo); // Set the values from the $userInfo array
        $this->db->where('taskId', $taskId);
        $this->db->update('tbl_task');
    
        return TRUE;
    }
}