<?php
session_start();
# ajax_dumpcontact.php

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
	$rid = $_POST['rid'];
	//*******************************************************
	
	//*************** DB STUFF ******************************
	# First, check to see if record exists in dumped_contacts
	$query = "SELECT rep_id FROM dumped_reps WHERE rep_id = '$rid'";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) == 1) {
		//dumped rep already exists in dumped_reps
		$errors[] = 'Rep has already been dumped.';
	}
	mysqli_free_result($rs);
	
	
	if (empty($errors)){
	
		$messages = array();
		$dumpsuccessful = false;	//boolean switch
		
		# First run the select - into to get the data
		# from contacts into dumped_contacts
		$selectinto_query = "INSERT INTO dumped_reps
							SELECT *
							FROM reps
							WHERE reps.rep_id='$rid'";
		$rs = mysqli_query ($dbc, $selectinto_query);
		if (mysqli_affected_rows($dbc) == 1){
			$dumpsuccessful = true;
			$messages[] = 'Rep has been dumped.';
		} else {
			$messages[] = 'Rep has not been dumped.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		}
	
		# NOW, delete contact from contacts table
		$delete_query = "DELETE FROM reps WHERE rep_id='$rid'";
		$rs = $rs = mysqli_query ($dbc, $delete_query);
		if (mysqli_affected_rows($dbc) == 1){
			$messages[] = 'Rep dump complete.';
		} else {
			$messages[] = 'Rep dump not successful.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
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