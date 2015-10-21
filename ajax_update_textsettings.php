<?php
session_start();
# ajax_update_textsettings.php

	//$_POST[] DATA SENT IN FROM $ajax. function
	/* 	tt_id, tt_body, data_changed
	*/
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	$errors = array();		//initialize errors array
	
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
	if (empty($scrubbed['tt_name']) || $scrubbed['tt_name'] == ''){
		$errors[] = 'Enter template name.';
	} else {
		$ttname = strip_tags(trim($scrubbed['tt_name']));
		$templatename = mysqli_real_escape_string($dbc, ucwords(strtolower($ttname)));	//adds slashes for mysql
		
		//compare name from form(without slashes) to original name from get ajax
		if ( $ttname != ucwords(strtolower($scrubbed['original_name']))){
			//IS template name already in db?
			$query = "SELECT text_template_name 
					  FROM vfg_rep_text_templates
					  WHERE rep_id = '$repsid'
					  AND text_template_name = '$templatename'";
			//RUN QUERY
			$rs = @mysqli_query ($dbc, $query);
			if (mysqli_num_rows($rs) == 1) {
				//email already exists!
				$errors[] = 'You have already used this template name.';
			}
			mysqli_free_result($rs);
		}
	}
	
	//body
	if ( empty($scrubbed['tt_body']) ){
		$errors[] = 'Please text message content.';
	} else {
		$ttbody = mysqli_real_escape_string($dbc, trim($scrubbed['tt_body']));
	}

	//*************** END FIELD VALIDATION ***********************
	
	//Data Changed flag
	$hasDataChanged = $scrubbed['data_changed'];
	
	//*************** DB UPDATE **********************************
	if (empty($errors)){
		if ($hasDataChanged == 'true'){
			//update
			$updatesql = "UPDATE vfg_rep_text_templates
						SET text_template_name ='$templatename',
							text_body ='$ttbody'
						WHERE rep_id = '$repsid' 
						AND tt_id = '$tid'"; 

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