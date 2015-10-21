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
			$this->dbc = @mysqli_connect (self::DB_HOST, self::DB_USER, self::DB_PASSWORD, self::DB_NAME) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
		 }	
		
		
		
		  /*
		  * Public method for access api.
  		  * This method dynmically call the method based on the query string
		  */
		  public function processApi(){
			// print_r($_REQUEST);exit;
			if(!empty($_REQUEST['unique_id'])) { 
 				$id = $_REQUEST['unique_id']; //print_r($id);exit;
				 $data = $this->getContact($id); //print_r($data);exit;
			} else { 
			     $data = "Please provie Unique Id";				
			}
 			$detail = $this->json($data);
			   return $detail;
		   }
		
		
		public function getContact($id) {
	          if(!empty($id)) { 
		       $query = "SELECT unique_id,phone FROM temp_contacts where unique_id = '".$id."' AND status = 0 ORDER BY timestamp DESC LIMIT 1";                 
			$resultID = @mysqli_query($this->dbc,$query);									
			//$resultID =  mysqli_query($query) or die("Contact not found.");
			
 	    		while ($row = mysqli_fetch_array($resultID, MYSQLI_ASSOC)) { 
		 		$res[] = $row;
 			 }  
                         
		  } else {
		       $res[] = "Please Provide Unique Id";
		  } 
                 $result['call'] = $res;
		  return $result;		
		}   
	   
	     	
				
		/*
		 *	Encode array into JSON
		*/
		private function json($data){  
			if(!empty($data)){  
				return json_encode($data);
			}
		}
}

// Initiiate Library
$api = new API;
//$a = $api->processApi();
echo $api->processApi();exit;

?>
