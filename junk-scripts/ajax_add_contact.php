<?php
include ('includes/selectlists.php');
include ('includes/phpfunctions.php');
include ('includes/config.inc.php');

//check form submission
if (isset($_POST['submitted']) && $_POST['submitted'] == "addcontact"){
	
	//date_default_timezone_set($_SESSION['rep_tz']);
	//DB Connection
	require_once (MYSQL);
	$errors = array();		//initialize errors array
	$messages = array();	//initialize messages array
	$contactadded = false;  //boolean switch for base contact data
	
	//VALIDATE FIELDS
	// 08/02/2013 dkf - Change to allow for blank fields
	//                  fill with 'none'
	//*******************************************************
	//FIRST NAME - Have to have first name
	if (empty($_POST['first_name'])){
		$errors[] = 'Please enter contact\'s first name.';
	} elseif (!preg_match("/^[A-Za-z]+$/", trim($_POST['first_name']))) {
		$errors[] = 'Your first name contains at least 1 invalid character.';
	} else {
		$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
		$fn = ucwords($fname);
	}
	
	//LAST NAME
	if (empty($_POST['last_name'])){
		//$errors[] = 'Please enter contact\'s last name.';
		$ln = '';
	} elseif (!preg_match("/^[A-Za-z -]+$/", trim($_POST['last_name']))) {
		$errors[] = 'Your last name contains at least 1 invalid character.';
	} else {
		$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
		$ln = ucwords($lname);
	}
	
	//EMAIL
	//check email
	if (empty($_POST['email'])){
		//$errors[] = 'Enter contact\'s email.';
		$email = '';
	} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['email']))) {
		$errors[] = 'Enter a valid email address.';
	} else {
		$email = strip_tags(trim($_POST['email']));

		//IS EMAIL ALREADY IN DB?
		$query = "SELECT email FROM contacts WHERE email = '$email'";
		//RUN QUERY
		$rs = @mysqli_query ($dbc, $query);
		if (mysqli_num_rows($rs) == 1) {
			//email already exists!
			$errors[] = 'Email address already exists in database.';
		}
		mysqli_free_result($rs);
	}
	
	//phone 
	if (empty($_POST['phone'])){
		//$errors[] = 'Please enter contact\'s area code.';
		$phone = '';
	} elseif (!preg_match("/^(1[-])?\d{3}[-]?\d{3}[-]?\d{4}$/", trim($_POST['phone']))) {
		$errors[] = 'Invalid phone number format. ########## or ###-###-####.';
	} else{
		$phone = trim($_POST['phone']);
	}
	
	if (empty($_POST['city'])){
		//$errors[] = 'Please enter contact\'s city.';
		$city = '';
	} elseif (!preg_match("/^[A-Za-z ]+$/", trim($_POST['city']))) {
		$errors[] = 'City contains at least 1 invalid character.';
	} else {
		$city = strip_tags(ucwords(trim(strtolower($_POST['city']))));
	}

	if ($_POST['state'] == ""){
		//$errors[] = 'Select contact\'s state.';
		$state = '';
	} else {
		$state = strip_tags(trim($_POST['state']));
	}
	
	//Timezone
	if (empty($_POST['timezone'])){
		//$errors[] = 'Please select contact\'s time zone.';
		# default tz to the rep's timezone
		$tz = "";
	} else {
		$tz = mysqli_real_escape_string($dbc, trim($_POST['timezone']));
	}
	
	//tier
	$tier = strip_tags(trim($_POST['tier']));
	
	//tierstep
	if ($_POST['tierstep'] == ""){
		$errors[] = 'Select contact\'s Tier status.';
	} else {
		$tierstep = strip_tags(trim($_POST['tierstep']));
	}
	
	//Team Member
	$teammember = $_POST['team_member'];
	
	//notes
	if (empty($_POST['notes'])){
		$notes = 'none';
	} else {
		$notes = mysqli_real_escape_string($dbc, trim($_POST['notes']));
	}
	//*************** END FIELD VALIDATION ***********************
	
	if (empty($errors)){
	
		$rightnow = date("Y-m-d H:i:s");
		$repid = $_SESSION['rep_id'];
		$tierstatus = $tier.$tierstep;
		
		//Create Unique ID for this contact - FNinit.LNinit.5 digit number
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
			if ($ln = ''){
				$contactid = substr($fn,0,1).'X'.$finalnum;
			} else {
				$contactid = substr($fn,0,1).substr($ln,0,1).$finalnum;
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
					phone, city, state, timezone, tier_status, notes, entry_date, rep_id, update_date, team_member) 
			  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		//prepare statement
		$stmt = mysqli_prepare($dbc, $q);

		//bind variables to statement
		mysqli_stmt_bind_param($stmt, 'ssssssssssssss', $contactid, $fn, $ln, $email, 
				$phone, $city, $state, $tz, $tierstatus, $notes, $rightnow, $repid, $rightnow, $teammember);

		//execute query
		mysqli_stmt_execute($stmt);

		if (mysqli_stmt_affected_rows($stmt) == 1) {	//rep data insert successful

			$contactadded = true;
			
			// CLOSE rep data insert statement
			mysqli_stmt_close($stmt);
			
			//echo '<div id="messages">Contact Added.  Add another if you wish.</div>';					
			unset($_POST);
			
		} else {	//stmt_affected_row != 1 for base data

			//echo '<div id="messages">There was a system issue with your data.</div>';
			$messages[] = 'There was a system issue with base data.';

			//echo '<p>' . mysqli_stmt_error($stmt) . '</p>';
			//echo '</div>';
		
		}	//close base data insert
		
		/*
		//Add FIRST TIER value for this contact to contact_progress table
		if ($contactadded) {
			$q = sprintf("INSERT INTO contact_progress (contact_id, contact_status)
							VALUES ('%s', '%s')", $contactid, "1A");
			$r = mysqli_query($dbc,$q);
			if (mysqli_affected_rows($dbc) == 1){
				$messages[] = 'Contact Added.  Add another if you wish.';
			} else {
				$messages[] = 'There was a system issue with status data.';
			}
		}
		*/
		//Add contact_id value for this contact to contactid_lookup table
		if ($contactadded) {
			$q = sprintf("INSERT INTO contactid_lookup (contact_id)
							VALUES ('%s')", $contactid);
			$r = mysqli_query($dbc,$q);
			if (mysqli_affected_rows($dbc) == 1){
				$messages[] = 'Contact Added.  Add another if you wish.';
			} else {
				$messages[] = 'There was a system issue with the data.';
			}
		}
		
		//Display messages
		echo '<p>';
		foreach ($messages as $msg){

			echo "$msg<br />\n";
		}

		echo '</p>';
		
		//CLOSE connection
		mysqli_close($dbc);			
	
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		//echo '<div id="messages">';
		echo '<p><u>ERRORS:</u><br /><br />';

		foreach ($errors as $msg){

			echo " - $msg<br />\n";
		}

		echo '</p>';
	}
	
}
?>