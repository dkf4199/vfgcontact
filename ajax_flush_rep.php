<?php
session_start();
# ajax_flush_rep.php

	//called from function dumpContact() on dumped_reps.php
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
	# First, check to see if record exists in dumped_reps
	$query = "SELECT rep_id FROM dumped_reps WHERE rep_id = '$rid' LIMIT 1";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) != 1) {
		//dumped contact doesn't exist in dumped_contacts
		$errors[] = 'Rep doesn\'t exist.  Contact system administrator.';
	}
	mysqli_free_result($rs);
	
	
	if (empty($errors)){
	
		$messages = array();
		
		// Move from dumped_reps to reps_archive
		$archive_query = "INSERT INTO reps_archive
							SELECT *
							FROM dumped_reps 
							WHERE dumped_reps.contact_id='$contactid'";
		$rs = mysqli_query ($dbc, $archive_query);
		if (mysqli_affected_rows($dbc) == 1){
			$messages[] = 'Rep archived.';
		} else {
			$messages[] = 'Rep not archived.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		}
		
		// DELETE FROM dumped reps
		$flush_query = "DELETE FROM dumped_reps WHERE rep_id='$rid' LIMIT 1";
							
		$rs = mysqli_query ($dbc, $flush_query);
		if (mysqli_affected_rows($dbc) == 1){
			$messages[] = 'Rep has been flushed.';
			//Set the $_SESSION['dump_total_recs'] var => subtract 1
			$_SESSION['total_dumped_reps'] = $_SESSION['total_dumped_reps'] - 1;
		
		} else {
			$messages[] = 'Rep has not been flushed.';
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
		echo '<p><u>ERRORS:</u><br />';
		foreach ($errors as $msg){
			echo " - $msg<br />\n";
		}
		echo '</p>';
	}
	
	unset($_POST);
	mysqli_close($dbc); // Close the database connection.
?>