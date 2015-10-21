<?php
session_start(); 

	// FOR: Remote DIALER phonecalls
	//
	// Stores unique_id, phone #, and timestamp to temp_contacts table 
	// when user click on icon of phone
	
	// ONLY WORKS IF: rep has their unique_id field set up.
	
	include ('./includes/config.inc.php');
	require_once (MYSQL); 
	
	if (isset($_SESSION['unique_id'])) {
	
		$unique_id = $_SESSION['unique_id']; //echo $unique_id; exit;
		$rightnow = date("Y-m-d H:i:s"); 
		$status = 0;

		if(isset($_POST['phone'])) {
			
			$phone = $_POST['phone'];
			//Create key field for temp_contacts table field "id_temp_contact" 
			$idexists = true;
			do {
				$randnum = mt_rand(1,99999);
				$strnum = strval($randnum);

				switch (strlen($strnum)) {
					case 1:
						$finalnum = '0000'.$strnum;
						break;
					case 2:
						$finalnum = '000'.$strnum;
						break;
					case 3:
						$finalnum = '00'.$strnum;
						break;
					case 4:
						$finalnum = '0'.$strnum;
						break;
						case 5:
						$finalnum = $strnum;
						break;
				}

				$tempcontactid= $finalnum;
								
				// Is value already in id_temp_contact in temp_contacts table?
				$query = "SELECT id_temp_contact 
						  FROM temp_contacts 
						  WHERE id_temp_contact = '$tempcontactid'";
				//run it
				$rs = @mysqli_query ($dbc, $query);
				if (mysqli_num_rows($rs) != 1) {
					//id is unique
					$idexists = false;
				}
				mysqli_free_result($rs);
			} while ($idexists);
		
			// Query temp_contacts and see if there is already a record in the table
			// for this unique_id - phone number combination.
			// If there is = do NOT add another!
			$rec_doesnot_exist = TRUE;
			$q = "SELECT unique_id, phone
				  FROM temp_contacts 
				  WHERE unique_id = '$unique_id' 
				  AND phone = '$phone' 
				  AND comm_note = '' ";
			//run it
			$rs = mysqli_query ($dbc, $q);
			if (mysqli_num_rows($rs) >= 1) {
				//id is unique
				$rec_doesnot_exist = false;
			}
			mysqli_free_result($rs);
			
			// $rec_doesnot_exist tells us this:
			// 1.) FALSE - a record for this rep to this contact ALREADY exists
			// 2.) TRUE - there is no entry for this contact
			
			if ($rec_doesnot_exist){
				
				$query = "INSERT INTO temp_contacts (id_temp_contact, unique_id, phone, timestamp, status) 
							VALUES ('$tempcontactid', '$unique_id','$phone','$rightnow','$status')";
				//echo $query;
								
				 $res = @mysqli_query ($dbc, $query) or die("Data is not inserted.");
			
			}

		}	//end isset(phone)
		
	} //end isset $_SESSION['unique_id']
?>