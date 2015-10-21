<?php
	//newlead.php
	//$_POST parms:
	//CONTACT DATA: firstname, lastname, phone, email
	//AGENT DATA: user_email, user_sub_domain
	
	/*
		This program takes the supplied parms and adds the contact
		to the the contacts table.
	*/
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	include ('includes/config.inc.php');
	//DB Connection
	require_once (MYSQL);
	
	//STEP 1 - Get the $_GET or $_POST data
	$contact_firstname = $contact_lastname = $contact_phone = $contact_phone_in = $contact_email = $rep_vfgid = '';
	
	if (isset($_POST['firstname'])) {
		$contact_firstname = ucwords(strtolower($_POST['firstname']));
	} elseif (isset($_GET['firstname'])) {
		$contact_firstname = ucwords(strtolower($_GET['firstname']));
	} else {
		$contact_firstname = '';
	}
	if ($contact_firstname == '' || $contact_firstname == '*'){
		$contact_firstname = 'X';
	}
	if (isset($_POST['lastname'])) {
		$contact_lastname = ucwords(strtolower($_POST['lastname']));
	} elseif (isset($_GET['lastname'])) {
		$contact_lastname = ucwords(strtolower($_GET['lastname']));
	} else {
		$contact_lastname = '';
	}
	if (isset($_POST['phone'])) {
		$contact_phone_in = trim($_POST['phone']);
	} elseif (isset($_GET['phone'])) {
		$contact_phone_in = trim($_GET['phone']);
	} else {
		$contact_phone_in = '';
	}
	
	if (isset($_POST['email'])) {
		$contact_email = $_POST['email'];
	} elseif (isset($_GET['email'])) {
		$contact_email = $_GET['email'];
	} else {
		$contact_email = '';
	}
	
	if (isset($_POST['vfgid'])) {
		$rep_vfgid = $_POST['vfgid'];
	} elseif (isset($_GET['vfgid'])) {
		$rep_vfgid = $_GET['vfgid'];
	} else {
		$rep_vfgid = '';
	}
	
	// FORMAT contact phone
	
	// 1.) If it comes in as ##########, make it ###-###-####
	if (preg_match("/^\d{10}$/", $contact_phone_in)){
		$contact_phone = substr($contact_phone_in,0,3).'-'.substr($contact_phone_in,3,3).'-'.substr($contact_phone_in,6,4);
	}
	// 2.) Leave it alone if it comes in as ###-###-####
	if ( preg_match("/^\d{3}[-]\d{3}[-]\d{4}$/", $contact_phone_in)){
		$contact_phone = $contact_phone_in;
	}
	// 3.) If it is (###)-###-#### change it to ###-###-####
	if ( preg_match("/^[(]\d{3}[)-]\d{3}[-]\d{4}$/", $contact_phone_in)){
		$contact_phone = substr($contact_phone_in,1,3).'-'.substr($contact_phone_in,6,3).'-'.substr($contact_phone_in,10,4);
	}
		
	$messages = array();	//holds feedback from db io operations
	$contactadded = false;  //boolean switch for base contact data
	
	$rep_id = '';	//initialize rep_id var
	
	//Step 2 Get Agent's rep_id from db - given their vfgid 
	//from the marketing system call
	$query = "SELECT rep_id FROM reps WHERE vfgrepid = '$rep_vfgid' LIMIT 1";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) == 1) {
		while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
			$rep_id = $row['rep_id'];
		}
	}
	mysqli_free_result($rs);
	
	//Got the rep_id.....insert the contact record
	
	if ($rep_id != '') {
		$rightnow = date("Y-m-d H:i:s");
		$tierstatus = '1A';
		
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
			
			
			// make the contact's id
			// if $contact_lastname is blank, make it an X
			if ($contact_lastname == ''){
				$contactid = substr($contact_firstname,0,1).'X'.$finalnum;
			} else {
				$contactid = substr($contact_firstname,0,1).substr($contact_lastname,0,1).$finalnum;
			}
			
			//IS contact_id ALREADY IN contactid_lookup DB?
			$query = "SELECT contact_id FROM contactid_lookup WHERE contact_id = '$contactid'";
			//RUN QUERY
			$rs = mysqli_query ($dbc, $query);
			if (mysqli_num_rows($rs) != 1) {
				//id is unique
				$idexists = false;
			}
			mysqli_free_result($rs);
		} while ($idexists);
	
		$online = 'Y';
		//prepared statement - INSERT DATA into contacts
		$q = "INSERT INTO contacts (contact_id, firstname, lastname, email, 
					phone, tier_status, entry_date, rep_id, online_lead) 
			  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

		//prepare statement
		$stmt = mysqli_prepare($dbc, $q);

		//bind variables to statement
		mysqli_stmt_bind_param($stmt, 'sssssssss', $contactid, $contact_firstname, $contact_lastname, 
								$contact_email, $contact_phone, $tierstatus, $rightnow, $rep_id, $online);
	
	
		//execute query
		mysqli_stmt_execute($stmt);
		if (mysqli_stmt_affected_rows($stmt) == 1) {	//contact data insert successful

			$contactadded = true;
			
			// CLOSE rep data insert statement
			mysqli_stmt_close($stmt);
						
		} else {	//stmt_affected_row != 1 for base data

			//echo '<div id="messages">There was a system issue with your data.</div>';
			$messages[] = 'There was a system issue with base data.';

			//echo '<p>' . mysqli_stmt_error($stmt) . '</p>';
			//echo '</div>';
		
		}	//close base data insert
		
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
						
		//CLOSE connection
		mysqli_close($dbc);
	
	
	
	} else {	// vfgrepid NOT IN OUR SYSTEM
	
		$messages[] = 'No Rep ID found for this agent.';
	}
	
	//Don't return messages right now.  Maybe write it to a file in the future.....
	//foreach($messages as $msg){
	//	echo "$msg <br />";
	//}
?>