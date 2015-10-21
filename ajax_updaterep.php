<?php
session_start();
# ajax_updatecontact.php

	//$repsid = $_SESSION['rep_id'];
	//$repsvfgid = $_SESSION['vfgrep_id'];
	//called from function updateRep() in tcs_admin_modal_handlers.php
	
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	$errors = array();		//initialize errors array
	
	//VALIDATE FORM FIELDS - rep_licensed, purchased_policy
	//*******************************************************
	$rid = $_POST['rid'];
	
	//rep_licensed
	if (empty($_POST['rep_licensed'])){
		$errors[] = 'Please select value for Rep Licensed.';
	} else {
		$rep_licensed = mysqli_real_escape_string($dbc, trim($_POST['rep_licensed']));
	}
	//purchased_policy
	if (empty($_POST['purchased_policy'])){
		$errors[] = 'Please select value for Purchased Policy.';
	} else {
		$purchased_policy = mysqli_real_escape_string($dbc, trim($_POST['purchased_policy']));
	}
	//rep_manager
	if (empty($_POST['rep_manager'])){
		$errors[] = 'Please select value for Rep Manager.';
	} else {
		$rep_manager = mysqli_real_escape_string($dbc, trim($_POST['rep_manager']));
	}
	//rep_consultant
	if (empty($_POST['rep_consultant'])){
		$errors[] = 'Please select value for Rep Consultant.';
	} else {
		$rep_consultant = mysqli_real_escape_string($dbc, trim($_POST['rep_consultant']));
	}
		
	//Data Changed flag
	$hasDataChanged = $_POST['data_changed'];
	
	//*************** DB UPDATE **********************************
	if (empty($errors)){
		if ($hasDataChanged == 'true'){
			//rep time:
					
			$updatesql = "UPDATE reps 
						  SET replevel_manager = '$rep_manager',
							  replevel_consultant = '$rep_consultant',
							  rep_licensed = '$rep_licensed', 
							  purchased_policy = '$purchased_policy'
						  WHERE rep_id = '$rid' LIMIT 1"; 

			//RUN UPDATE QUERY
			$rs= mysqli_query($dbc, $updatesql);
			
			if (mysqli_affected_rows($dbc) == 1){
				echo '<p>Update successful.</p>';
								
			} else {
				echo '<p>Update failed.</p>';
				//echo '<p>'.mysqli_error($dbc).'</p>';
			
			}	// close mysqli_affected_rows($dbc) == 1
		} else {
			//no changes were made
			echo '<p>You made no changes to the data.</p>';
		}
		
	
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p><u><font color="red"><b>ERRORS:</b></font></u>  :';
		foreach ($errors as $msg){
			echo " ** $msg ** \n";
		}
		echo '</p>';
	}
	
	mysqli_close($dbc); // Close the database connection.
?>