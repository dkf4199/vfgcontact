<?php
session_start();
# ajax_flushcontact.php

	//called from function dumpContact() on rep_manage_contacts.php
	//
	//$_POST[] DATA SENT IN FROM $ajax. function
	/* 	currentcontactid
	*/
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	$repsid = $_SESSION['rep_id'];
	
	//DB Connection
	require_once (MYSQL);
	
	$errors = array();		//initialize errors array
	
	//Get the flushed_contacts # from reps table
	//FLUSHED CONTACTS for rep
	$flushed_contacts = 0;
	$t = "SELECT flushed_contacts FROM reps
	   WHERE rep_id='$repsid'";
	$r = mysqli_query($dbc, $t);
	$row = mysqli_fetch_array($r, MYSQLI_NUM);
	$flushed_contacts = $row[0];
	mysqli_free_result($r);
	
	//*******************************************************
	# NO FORM FIELDS - ONLY $_POST['currentcontactid']
	$contactid = $_POST['cid'];
	//*******************************************************
	
	//*************** DB STUFF ******************************
	# First, check to see if record exists in dumped_contacts
	$query = "SELECT contact_id FROM dumped_contacts WHERE contact_id = '$contactid' LIMIT 1";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) != 1) {
		//dumped contact doesn't exist in dumped_contacts
		$errors[] = 'Contact doesn\'t exist.  Contact system administrator.';
	}
	mysqli_free_result($rs);
	
	
	if (empty($errors)){
	
		$messages = array();
		
		// Move from dumped_contacts to contacts_archive
		$archive_query = "INSERT INTO contacts_archive
							SELECT *
							FROM dumped_contacts 
							WHERE dumped_contacts.contact_id='$contactid'";
		$rs = mysqli_query ($dbc, $archive_query);
		if (mysqli_affected_rows($dbc) == 1){
			$messages[] = 'Contact archived.';
		} else {
			$messages[] = 'Contact not archived.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		}
		
		// DELETE NOTES
		$flush_notes = "DELETE FROM notes WHERE contact_id='$contactid'";
		$ds = mysqli_query($dbc, $flush_notes);
		
		// DELETE FROM dumped_contacts
		$flush_query = "DELETE FROM dumped_contacts WHERE contact_id='$contactid' LIMIT 1";
							
		$rs = mysqli_query ($dbc, $flush_query);
		if (mysqli_affected_rows($dbc) == 1){
			$messages[] = 'Contact has been flushed.';
			//Set the $_SESSION['dump_total_recs'] var => subtract 1
			//$_SESSION['dump_total_recs'] = $_SESSION['dump_total_recs'] - 1;
			
			//Increment and update flushed_contacts column in reps
			$flushed_contacts = $flushed_contacts+1;
			
			$updatesql = "UPDATE reps
						SET flushed_contacts = $flushed_contacts
						WHERE rep_id = '$repsid' LIMIT 1"; 
			//RUN UPDATE QUERY
			$r= mysqli_query($dbc, $updatesql);
			if (mysqli_affected_rows($dbc) == 1){
				$messages[] = 'Flushed contacts count has been updated.';
			} else {
				$messages[] = 'Flushed contacts count update problem.</p>';
				//echo '<p>'.mysqli_error($dbc).'</p>';
			}
		
		} else {
			$messages[] = 'Contact has not been flushed.';
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