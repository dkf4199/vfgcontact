<?php
session_start();
# ajax_convert_lead_to_prospect.php

	$repsid = $_SESSION['rep_id'];
	$repsvfgid = $_SESSION['vfgrep_id'];
	//called from function updateContact() on rep_manage_contacts.php
	//
	
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	//boolean switches
	$contact_exists = false;
	$contactadded = false;
	
	$errors = array();	//to check phone and email, need at least one populated
	
	// $_POST FIELDS
	$thislid = $_POST['lid'];
	$first = ucwords(strtolower($_POST['first_name']));
	$last = ucwords(strtolower($_POST['last_name']));
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$notes = $_POST['lead_notes'];
	
	//PHONE AND EMAIL ERROR CHECK
	if ($phone == '' && $email == ''){
		$errors[] = 'Must have either phone or email present to convert lead.';
	}
	//*******************************************************
	
	//Check to see if this lead is already in contacts db
	$query = "SELECT contact_id 
			  FROM contacts 
			  WHERE firstname = '$first'
			  AND lastname = '$last'
			  AND email = '$email' LIMIT 1";
	//RUN QUERY
	$rs = @mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) == 1) {
		//contact already exists!
		$contact_exists = true;
	}
	mysqli_free_result($rs);
	
	//VALIDATE FORM FIELDS
	//*******************************************************
	
	//*************** END FIELD VALIDATION ***********************
	
	
	//*************** DB INSERT **********************************
	if (empty($errors)){
		if (!$contact_exists){
			if (isset($_SESSION['rep_tz'])){
				date_default_timezone_set($_SESSION['rep_tz']);
			}
			$rightnow = date("Y-m-d H:i:s");
			$tierstatus = '1A';
			
			// Generate uniqueid for contact
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
				
				
				// make the rep's id
				// if $ln is blank, make it an X
				if ($last == ''){
					$contactid = substr($first,0,1).'X'.$finalnum;
				} else {
					$contactid = substr($first,0,1).substr($last,0,1).$finalnum;
				}
				
				//IS UNIQUEID ALREADY IN contactid_lookup DB?
				$query = "SELECT contact_id FROM contactid_lookup WHERE contact_id = '$contactid'";
				//RUN QUERY
				$rs = @mysqli_query ($dbc, $query);
				if (mysqli_num_rows($rs) != 1) {
					//id is unique
					$idexists = false;
				}
				mysqli_free_result($rs);
			} while ($idexists);
			
			//prepared statement - INSERT DATA into reps
			$q = "INSERT INTO contacts (contact_id, firstname, lastname, email, 
						phone, tier_status,	notes, entry_date, rep_id) 
				  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			//prepare statement
			$stmt = mysqli_prepare($dbc, $q);

			//bind variables to statement
			mysqli_stmt_bind_param($stmt, 'sssssssss', 
								$contactid, $first, $last, $email, 
								$phone, $tierstatus, $notes, $rightnow,	$repsid);
			
			//execute query
			mysqli_stmt_execute($stmt);
			if (mysqli_stmt_affected_rows($stmt) == 1) {	//rep data insert successful
				
				$contactadded = true;
				mysqli_stmt_close($stmt);
				
			} else {	//stmt_affected_row != 1 for base data

				echo 'Lead conversion problem.';
				//echo '<p>' . mysqli_stmt_error($stmt) . '</p>';
				//echo '</div>';
			
			}			
			
			if ($contactadded) {
				$q = sprintf("INSERT INTO contactid_lookup (contact_id)
								VALUES ('%s')", $contactid);
				$r = mysqli_query($dbc,$q);
				if (mysqli_affected_rows($dbc) == 1){
					echo 'Lead successfully added to system as a Prospect.';
				} else {
					echo 'Contact id problem.';
				}
				
				//update is_recruit field in lead_list
				$q = sprintf("UPDATE lead_list SET is_prospect = 'Y' WHERE l_id = %d LIMIT 1", $thislid);
				$r = mysqli_query($dbc,$q);
				if (mysqli_affected_rows($dbc) == 1){
					
				} else {
					
				}
			}
			
		} else {
			
			echo 'Lead has already been converted to Recruit.';
		
		}
	
	} else {	//$errors not empty
		
		foreach ($errors as $msg){
			echo "$msg<br />";
		}	
	
	}	//end empty($errors)
	mysqli_close($dbc); // Close the database connection.
?>