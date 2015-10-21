<?php
//include ('../../includes/config.inc.php');
///require_once ('../'.MYSQL);

class API {
	public $dbc;
	public $data = "";		
	//DEFINE ('DB_USER', 'spiritdr_webusrW');
	//DEFINE ('DB_PASSWORD', 'webUser^99');
	//DEFINE ('DB_HOST', 'localhost');
	//DEFINE ('DB_NAME', 'spiritdr_vfgcontact');

	const DB_HOST = "localhost";
	const DB_USER = "spiritdr_webusrW";
	const DB_PASSWORD = "webUser^99";
	const DB_NAME = "spiritdr_vfgcontact";

	/*
	*  Database connection 
	*/
	public function __construct() {
		$this->dbc = @mysqli_connect (self::DB_HOST, self::DB_USER, self::DB_PASSWORD, self::DB_NAME) 
										OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
	}	
		
		
		
	/*
	* Public method for access api.
	* This method dynmically call the method based on the query string
	*/
	public function processApi(){ 
		if((!empty($_REQUEST['unique_id'])) && (!empty($_REQUEST['method']))) { 
			$id = $_REQUEST['unique_id'];
			// 12/10/2013 temp_id_contact field passed in now...
			$temp_id = $_REQUEST['temp_id'];
			
			$phone = (isset($_REQUEST['phone'])) ? $_REQUEST['phone'] : '';
			
			switch($_REQUEST['method']) {
				case "get":
					$data['call'] = $this->getContact($id); 
					break;
				case "delete":
					$data['call'] = $this->deleteContact($id,$phone,$temp_id); 	                                                        
					break;	

			} 
			/*if($_REQUEST['methodget'] == 'get') {
			$data['call'] = $this->getContact($id);     
			}
			if($_REQUEST['methoddelete'] == 'delete') {
			$data[] = $this->deleteContact($id,$phone); 
			}*/	
			//$data = $this->getContact($id); //print_r($data);exit;
		} else { 
			$data['call'] = "Please provide Unique Id";				
		}
		$detail = $this->json($data);
		return $detail;
	}
		
		
	public function getContact($id) { 
		if(!empty($id)) {
			// Include id_temp_contact field
			// in return array to just delete this record on subsequent delete call
			// from mobile app
			//
			$query = "SELECT id_temp_contact, unique_id, phone, comm_note 
					  FROM temp_contacts 
					  WHERE unique_id = '".$id."' 
					  AND status = 0 
					  ORDER BY timestamp DESC LIMIT 1";                 
			$resultID = @mysqli_query($this->dbc,$query);									
			//$resultID =  mysqli_query($query) or die("Contact not found.");

			while ($row = mysqli_fetch_array($resultID, MYSQLI_ASSOC)) { 
				$result[] = $row;
			}  
				 
		} else {
			$result[] = "Please Provide Unique Id";
		} 
		return $result;		
	}   

	public function deleteContact($id, $phone, $tempid) {
		if( !empty($id) && !empty($phone) && !empty($tempid) ) { 
			$query = "DELETE FROM temp_contacts 
					  WHERE unique_id = '$id' 
					  AND phone = '$phone' 
					  AND id_temp_contact = '$tempid' ";
			$resultID =  @mysqli_query($this->dbc,$query) or die("Contact not found.");
			$affected_row = mysqli_affected_rows($this->dbc);
			
			if($affected_row == 0) {
				 $data = "Contact is not deleted successfully";
			} else {
				$data = "Contact Deleted Successfully";
			}
			
		} else {
			$data = "Please Provide Unique Id AND Contact Number";
		}
		return $data;		
	}   
	   
	     	
				
	/* Encode array into JSON */
	private function json($data){  
		if(!empty($data)){  
			return json_encode($data);
		}
	}
}

// Initiiate Library
$api = new API;
//$a = $api->processApi();
echo $api->processApi();
exit;

?>
