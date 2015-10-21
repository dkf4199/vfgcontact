<?php
session_start();
# ajax_restoredumpcontact.php

	//called from function dumpContact() on rep_manage_contacts.php
	//
	//$_POST[] DATA SENT IN FROM $ajax. function
	/* 	currentcontactid
	*/
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	$errors = array();		//initialize errors array
	
	//*******************************************************
	# NO FORM FIELDS - ONLY $_POST['currentcontactid']
	$contactid = $_POST['cid'];
	//*******************************************************
	
	//*************** DB STUFF ******************************
	# First, check to see if record exists in contacts
	$query = "SELECT contact_id FROM contacts WHERE contact_id = '$contactid'";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) == 1) {
		//dumped contact already exists in contacts
		$errors[] = 'Contact has already been restored.';
	}
	mysqli_free_result($rs);
	
	
	if (empty($errors)){
	
		$messages = array();
		$restoresuccessful = false;	//boolean switch
		
		# First run the select-into to get the data
		# from dumped_contacts into contacts
		$selectinto_query = "INSERT INTO contacts
							SELECT *
							FROM dumped_contacts 
							WHERE dumped_contacts.contact_id='$contactid'";
		$rs = mysqli_query ($dbc, $selectinto_query);
		if (mysqli_affected_rows($dbc) == 1){
			$restoresuccessful = true;
			$messages[] = 'Contact has been restored.';
			
			//Decrement $_SESSION['dump_total_recs'] var by 1
			if (isset($_SESSION['dump_total_recs'])){
				$_SESSION['dump_total_recs'] = $_SESSION['dump_total_recs'] - 1;
			}
		} else {
			$messages[] = 'Contact has not been restored.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		}
		
		if ($restoresuccessful){
			# NOW, delete contact from dumped_contacts table
			$delete_query = "DELETE FROM dumped_contacts WHERE contact_id='$contactid'";
			$rs = $rs = mysqli_query ($dbc, $delete_query);
			if (mysqli_affected_rows($dbc) == 1){
				$messages[] = 'Contact restore process complete.';
			} else {
				$messages[] = 'Contact restore process not complete.';
				//echo '<p>'.mysqli_error($dbc).'</p>';
			}
		} else {
			$messages[] = 'Contact restore process unsuccessful.';
		}
		
		//Display db i/o messages
		echo '<p>';
		foreach ($messages as $msg){
			echo "$msg<br />\n";
		}
		echo '</p>';
		/************ END DB I/O ********************************************/
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p>';
		foreach ($errors as $msg){
			echo " - $msg<br />\n";
		}
		echo '</p>';
	}
	
	unset($_POST);
	mysqli_close($dbc); // Close the database connection.
?>