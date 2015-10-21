<?php
session_start();
# ajax_restore_rep.php
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
	# First, check to see if record exists in reps
	$query = "SELECT rep_id FROM reps WHERE rep_id = '$rid'";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) == 1) {
		//dumped contact already exists in contacts
		$errors[] = 'Rep has already been restored.';
	}
	mysqli_free_result($rs);
	
	
	if (empty($errors)){
	
		$messages = array();
		$restoresuccessful = false;	//boolean switch
		
		# First run the select-into to get the data
		# from dumped_contacts into contacts
		$selectinto_query = "INSERT INTO reps
							SELECT *
							FROM dumped_reps 
							WHERE dumped_reps.rep_id='$rid'";
		$rs = mysqli_query ($dbc, $selectinto_query);
		if (mysqli_affected_rows($dbc) == 1){
			$restoresuccessful = true;
			$messages[] = 'Rep has been restored.';
			
			//Decrement $_SESSION['total_dumped_reps'] var by 1
			if (isset($_SESSION['total_dumped_reps'])){
				$_SESSION['total_dumped_reps'] = $_SESSION['total_dumped_reps'] - 1;
			}
		} else {
			$messages[] = 'Rep has not been restored.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		}
		
		if ($restoresuccessful){
			# NOW, delete contact from dumped_contacts table
			$delete_query = "DELETE FROM dumped_reps WHERE rep_id='$rid'";
			$rs = $rs = mysqli_query ($dbc, $delete_query);
			if (mysqli_affected_rows($dbc) == 1){
				$messages[] = 'Rep restore process complete.';
			} else {
				$messages[] = 'Rep restore process not complete.';
				//echo '<p>'.mysqli_error($dbc).'</p>';
			}
		} else {
			$messages[] = 'Rep restore process unsuccessful.';
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