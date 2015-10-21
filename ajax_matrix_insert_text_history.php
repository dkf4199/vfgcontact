<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$repid = $_SESSION['rep_id'];
	
	// $_POST VARS
	$templatename = $_POST['template_name'];
	$contactid = $_POST['cid'];
	$contactphone = $_POST['phone'];
	$textbody = mysqli_real_escape_string($dbc, trim($_POST['text_body']));
	
	//Set up the textnote for insert to history table
	if ($templatename == 'blank'){
		$textnote = 'Sent blank custom text: '.$textbody;
	} else {
		$textnote = 'Sent custom text template '.$templatename;
	}
	
	
	date_default_timezone_set($_SESSION['rep_tz']);
	$rightnow = date("Y-m-d H:i:s");
	
	
	
	// SET UP DIALER to send text - ONLY if rep's unique_id field is set
	//
	if (isset($_SESSION['unique_id'])){
	
		$uniqueid = $_SESSION['unique_id'];
		$status = "0";
		
		//Create key field for temp_contacts table field "id_temp_contact" 
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
			$tempcontactid= $finalnum;
			
			// Is value already in id_temp_contact in temp_contacts table?
			$query = "SELECT id_temp_contact FROM temp_contacts WHERE id_temp_contact = '".$tempcontactid."'";
			$rs = @mysqli_query ($dbc, $query);
			if (mysqli_num_rows($rs) != 1) {
				  //id is unique
				  $idexists = false;
			}
			mysqli_free_result($rs);
			
		} while ($idexists);
	
				
		//CHECK - see if record with this text already exists in the table.
		// Query temp_contacts and see if there is already a record in the table
		// for this unique_id - phone number combination.
		// If there is = do NOT add another!
		$textrec_does_not_exist = TRUE;
		$q = "SELECT unique_id, phone, comm_note
			  FROM temp_contacts 
			  WHERE unique_id = '$uniqueid' 
			  AND phone = '$contactphone' 
			  AND comm_note = '$textbody' ";
		//run it
		$rs = @mysqli_query ($dbc, $q);
		if (mysqli_num_rows($rs) >= 1) {
			//id is unique
			$textrec_does_not_exist = false;
		}
		mysqli_free_result($rs);
		
		// $rec_doesnot_exist tells us this:
		// 1.) FALSE - a record for this rep to this contact ALREADY exists
		// 2.) TRUE - there is no entry for this contact/text msg
		if ($textrec_does_not_exist){
			// Send $textbody....not $textnote.  Textnote has a notification header on it. 
			$insquery = "INSERT INTO temp_contacts (id_temp_contact, unique_id, phone, comm_note, timestamp, status) 
						VALUES ('$tempcontactid', '$uniqueid', '$contactphone', '$textbody', '$rightnow', '$status')";
			$result = mysqli_query ($dbc, $insquery);
			echo '<p>Text sent via Dialer.</p>';
			
			
			//Create History entry to comm_history
			$insertsql = "INSERT INTO communication_matrix (rep_id, contact_id, comm_type, comm_note, comm_date) 
						VALUES (?, ?, 'TM', ?, ?)";
			//prepare statement
			$stmt = mysqli_prepare($dbc, $insertsql);
			//bind variables to statement
			mysqli_stmt_bind_param($stmt, 'ssss', $repid, $contactid, $textnote, $rightnow);
			//execute query
			mysqli_stmt_execute($stmt);
			
			if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
				mysqli_stmt_close($stmt);
				echo '<p>Text record saved to history.</p>';
			} else {
				echo '<p>Text record insert problem.</p>';
			}
			
		
		} else {
			echo '<p>The Dialer has already received this text message for this contact.</p>';
		}
		
	
	} //end isset($_SESSION['unique_id'];	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>