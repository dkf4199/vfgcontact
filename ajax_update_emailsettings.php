<?php
session_start();
	# ajax_update_mailsettings.php

	//
	//$_POST[] DATA SENT IN FROM $ajax. function
	/* 	t_name, t_subject, t_salutation,
		t_body, t_imagelink, t_closing, 
		cid, data_changed
	*/
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	$errors = array();		//initialize errors array
	/*
	$dc = $_POST['data_changed'];
	$rc = $_POST['status_changed'];
	echo $dc;
	echo $rc;
	*/
	// Send $_POST data thru the scrubber
	// This gets rid of any directives used to spam
	//
	$scrubbed = array_map('spam_scrubber',$_POST);
				
	//VALIDATE FORM FIELDS
	//*******************************************************
	//Repid - Passed in from ajax
	$repsid = $scrubbed['rep'];
	$tid = $scrubbed['tid'];
	
	//Template Name
	if (empty($scrubbed['t_name']) || $scrubbed['t_name'] == ''){
		$errors[] = 'Enter template name.';
	} else {
		$tname = strip_tags(trim($scrubbed['t_name']));
		$templatename = mysqli_real_escape_string($dbc, ucwords(strtolower($tname)));	//adds slashes for mysql
		//compare name from form(without the slashes) to original name from get ajax
		if ( $tname != ucwords(strtolower($scrubbed['original_name']))){
			//IS template name already in db?
			$query = "SELECT template_name 
					  FROM vfg_customemail_settings
					  WHERE rep_id = '$repsid'
					  AND template_name = '$templatename'";
			//RUN QUERY
			$rs = @mysqli_query ($dbc, $query);
			if (mysqli_num_rows($rs) == 1) {
				//email already exists!
				$errors[] = 'You have already used this template name.';
				//debug
				$errors[] = $templatename;
				$errors[] = strip_tags(trim($scrubbed['t_name']));
			}
			mysqli_free_result($rs);
		}
	}
	
	//Subject
	if ( empty($scrubbed['t_subject']) ){
		$errors[] = 'Please enter subject line.';
	} else {
		$subject = mysqli_real_escape_string($dbc, trim($scrubbed['t_subject']));
	}
	
	//Salutation
	if ( empty($scrubbed['t_salutation']) ){
		$errors[] = 'Please select a salutation.';
	} else {
		$salutation = $scrubbed['t_salutation'];
	}
	
	//body
	if ( empty($scrubbed['t_body']) ){
		$errors[] = 'Please enter body content.';
	} else {
		$body = mysqli_real_escape_string($dbc, trim($scrubbed['t_body']));
	}
	
	//Image Link
	if ( empty($scrubbed['t_imagelink']) ){
		$errors[] = 'Please provide link to your VFG landing page.';
	} else {
		$imagelink = mysqli_real_escape_string($dbc, trim($scrubbed['t_imagelink']));
	}
	
	//Closing
	if ( $scrubbed['t_closing'] == '' ){
		$errors[] = 'Please select a closing.';
	} else {
		$closing = $scrubbed['t_closing'];
	}
	
	//*************** END FIELD VALIDATION ***********************
	
	//Data Changed flag
	$hasDataChanged = $scrubbed['data_changed'];
	
	//*************** DB UPDATE **********************************
	if (empty($errors)){
		if ($hasDataChanged == 'true'){
			//update
			$updatesql = "UPDATE vfg_customemail_settings 
						SET template_name ='$templatename',
							subject ='$subject', 
							salutation ='$salutation', 
							body ='$body',
							img_link ='$imagelink',
							closing ='$closing'
						WHERE rep_id = '$repsid' 
						AND t_id = '$tid'"; 

			//RUN UPDATE QUERY
			$rs= mysqli_query($dbc, $updatesql);
			
			if (mysqli_affected_rows($dbc) == 1){
				echo '<p>Update successful.</p>';
			} else {
				echo '<p>Update failed.</p>';
				//echo '<p>'.mysqli_error($dbc).'</p>';
			}
				
		} else {
			//no changes were made
			echo '<p>You made no changes to the data.</p>';
		}
		
		
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p align="center"><u><font color="red"><b>ERRORS:</b></font></u><br />';
		foreach ($errors as $msg){
			echo " ** $msg **<br /> \n";
		}
		echo '</p>';
	}
	mysqli_close($dbc); // Close the database connection.
?>