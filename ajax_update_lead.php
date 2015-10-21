<?php
session_start();
# ajax_update_lead.php

	$repsid = $_SESSION['rep_id'];
	$repsvfgid = $_SESSION['vfgrep_id'];
	//
	
	$cur_lid = (int) $_POST['lid'];
	
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	$errors = array();		//initialize errors array
	
	//VALIDATE FORM FIELDS
	//*******************************************************
	
	//Lead Category
	if ($_POST['category'] == ""){
		$errors[] = 'Select category for lead.';
	} else {
		$lead_cat = strip_tags(trim($_POST['category']));
	}
	
	//FIRST NAME
	if ( empty($_POST['first_name']) ){
		$errors[] = 'Please enter lead\'s first name.';
	} elseif (!preg_match("/^[A-Za-z]+$/", trim($_POST['first_name']))) {
		$errors[] = 'First name contains at least 1 invalid character.';
	} else {
		$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
		$fn = ucwords(strtolower($fname));
	}
	
	//LAST NAME
	if (empty($_POST['last_name']) ){
		//$errors[] = 'Please enter lead\'s last name.';
		$ln = '';
	} elseif (!preg_match("/^[A-Za-z -]+$/", trim($_POST['last_name']))) {
		$errors[] = 'Last name contains at least 1 invalid character.';
	} else {
		$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
		$ln = ucwords(strtolower($lname));
	}
	
	//EMAIL
	//check email
	if (empty($_POST['email']) ){
		//$errors[] = 'Enter lead\'s email.';
		$email = '';
	} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['email']))) {
		$errors[] = 'Enter a valid email address.';
	} else {
		$email = mysqli_real_escape_string($dbc, trim(strtolower($_POST['email'])));
		
	}
	
	//phone
	if ( empty($_POST['phone']) || $_POST['phone'] == ''){
		//$errors[] = 'Please enter contact\'s phone.';
		$phone = '';
	} elseif (!preg_match("/^\d{3}[-]\d{3}[-]\d{4}$/", trim($_POST['phone']))) {
		$errors[] = 'Invalid phone number format. ###-###-####.';
	} else {
		if (strlen(trim($_POST['phone'])) == 10) {	//no dashes ##########
			$formattedphone = trim($_POST['phone']);
			$phone = substr($formattedphone,0,3).'-'.substr($formattedphone,3,3).'-'.substr($formattedphone,6,4);
		}
		if (strlen(trim($_POST['phone'])) == 12) {	//dashes ###-###-####
			$phone = trim($_POST['phone']);
		}
	}
	
	//Lead Priority
	if ($_POST['lead_priority'] == ""){
		$errors[] = 'Select lead priority.';
	} else {
		$lead_priority = strip_tags(trim($_POST['lead_priority']));
	}
		
	//notes
	if (empty($_POST['lead_notes'])){
		$notes = 'none';
	} else {
		$notes = mysqli_real_escape_string($dbc, trim($_POST['lead_notes']));
	}
	//*************** END FIELD VALIDATION ***********************
	
	//Data Changed flag
	$hasDataChanged = $_POST['data_changed'];

	
	//*************** DB UPDATE **********************************
	if (empty($errors)){
		if ($hasDataChanged == 'true'){
			if (isset($_SESSION['rep_tz'])){
				date_default_timezone_set($_SESSION['rep_tz']);
			}
	
			//prepared statement - INSERT DATA into reps
			$q = "UPDATE lead_list 
					SET firstname = ?,
						lastname = ?,
						phone = ?,
						email = ?,
						notes = ?,
						priority_number = ?,
						category = ?
				  WHERE l_id = ? AND rep_id = ? LIMIT 1 ";
			//prepare statement
			$stmt = mysqli_prepare($dbc, $q);
			//bind variables to statement
			mysqli_stmt_bind_param($stmt, 'sssssssds', $fn, $ln, $phone, $email, $notes,
									$lead_priority, $lead_cat, $cur_lid, $repsid);
			//execute query
			mysqli_stmt_execute($stmt);
			if (mysqli_stmt_affected_rows($stmt) == 1) {	//rep data insert successful
				mysqli_stmt_close($stmt);
				echo 'Lead updated.';
				
			} else {	//stmt_affected_row != 1 for base data

				echo 'Update problem.';
				//echo '<p>' . mysqli_stmt_error($stmt) . '</p>';
				//echo '</div>';
			
			}
			
		} else {
			//no changes were made
			echo '<p>You made no changes to the data.</p>';
		}
		
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p><u><font color="red"><b>ERRORS:</b></font></u><br />';
		foreach ($errors as $msg){
			echo "<br />$msg\n";
		}
		echo '</p>';
	}
	
	mysqli_close($dbc); // Close the database connection.
?>