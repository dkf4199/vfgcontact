<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	require_once ('Zend/Mail.php');
	require_once ('Zend/Mail/Transport/Sendmail.php');
	
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repsid = $_SESSION['rep_id'];
	$repfn = $_SESSION['rep_firstname'];
	$repln = $_SESSION['rep_lastname'];
	$repgmail = $_SESSION['rep_gmail'];
	$repgpass = $_SESSION['rep_gpass'];
	$repphone = '';
	if (isset($_SESSION['rep_phone'])){ 
		$repphone = $_SESSION['rep_phone'];
	}
	$dialer_unique_id = '';
	if (isset($_SESSION['unique_id'])){ 
		$dialer_unique_id = $_SESSION['unique_id'];
	}
	
	$contactid = $_POST['cid'];
	$contact_email = $_POST['contact_em'];		// contact's email
	$contact_fname = $_POST['contact_fname'];	// contact's firstname
	$contact_lname = $_POST['contact_lname'];	// contact's lastname
	$contact_phone = '';
	$contact_phone = $_POST['contact_phone'];	// contact's phone
	
	$phonescript = $_POST['script'];	// which phone script was used
	$calltype = $_POST['calltype'];		// voice mail or conversation
	
	$sendmail = $_POST['sendmail'];		// trifecta send mail flag
	$sendtext = $_POST['sendtext'];		// trifecta send text flag
	
	//$thisscript = $phonescript;
	switch ($phonescript){
		case 'intro':
			$thisscript = "Intro Script";
			break;
		case 'aftervideo':
			$thisscript = "Call After Video";
			break;
		case 'phoneattempt2':
			$thisscript = "2nd Call Attempt";
			break;
	}
	
	//voicemail or conversation
	$vmorcv = '';
	switch ($calltype){
		case 'vm':
			$vmorcv = "Left Voice Mail";
			break;
		case 'cv':
			$vmorcv = "Had Conversation";
			break;
	}
	
	$phonenote = $vmorcv.' using script: '.$thisscript.'.';
	
	//$phonenote = mysqli_real_escape_string($dbc, trim($_POST['phonenote']));
	
	date_default_timezone_set($_SESSION['rep_tz']);
	$rightnow = date("Y-m-d H:i:s");
	
	$insertsql = "INSERT INTO communication_matrix (rep_id, contact_id, comm_type, comm_note, comm_date) 
						VALUES (?, ?, 'PC', ?, ?)";

	//prepare statement
	$stmt = mysqli_prepare($dbc, $insertsql);
	//bind variables to statement
	mysqli_stmt_bind_param($stmt, 'ssss', $repsid, $contactid, $phonenote, $rightnow);
	//execute query
	mysqli_stmt_execute($stmt);
	
	if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
		mysqli_stmt_close($stmt);
		echo '<p>Call record inserted into history.</p>';
	} else {
		//echo '<p>'.mysqli_error($dbc).'</p>';
		echo '<p>Call record insert problem.</p>';
	}
	
	// SEND EMAIL if sendmail == Y
	$mail_sent = false;		//flag to indicate if mail was successfully sent
	
	if ($sendmail == 'Y') {
		$tr = new Zend_Mail_Transport_Sendmail('smtp.gmail.com', array(
			'auth' => 'login',
			'username' => $repgmail,
			'password' => $repgpass,
			'ssl' => 'ssl',
			'port' => 465)
		);
		Zend_Mail::setDefaultTransport($tr);
		
		// Make the query to get matching email template for corresponding phone script:
		$q = "SELECT template_name, subject, salutation, body,
					img_link, closing 
			  FROM vfg_customemail_settings
			  WHERE rep_id = '$repsid' 
			  AND template_name = '$thisscript'
			  LIMIT 1";	
			
		$r = mysqli_query ($dbc, $q); // Run the query.

		if ($r) { // If it ran OK, get the data if there is a record.
			if (mysqli_num_rows($r) == 1){
				// Fetch the needed fields:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$emt_name = $row['template_name'];
					$subject = $row['subject'];
					$salutation = $row['salutation']; 
					$body = stripslashes($row['body']);
					$imglink = $row['img_link'];
					$closing = $row['closing'];
					//$templatename = $row['template_name'];
				}
							
				//Put the email together and send it.
				//***** EMAIL VARS *******
				//To:  $toemail
				//From: $fromemail
				//Subject: $subj
				//Body: $_POST['emailmsg']
				//*************************
				
				//First, figure out the salutation
				/*
					1 - Dear Friend
					2 - Dear {firstname}
					3 - Greetings Friend
					4 - Greetings {firstname}
				*/
				$sal = '';
				switch ($salutation){
					case '1':
						$sal = "Dear Friend,";
						break;
					case '2':
						$sal = "Dear ".$contact_fname.",";
						break;
					case '3':
						$sal = "Greetings Friend,";
						break;
					case '4':
						$sal = "Greetings ".$contact_fname.",";
						break;
					default:
						$sal = "Dear Friend,";
						break;
				}
				
				//Figure out the Closing
				//First, figure out the salutation
				/*
					1 - Sincerely
					2 - Regards
					3 - Best Regards
					4 - Respectfully
					5 - Thank You
				*/
				$close = '';
				switch ($closing){
					case '1':
						$close = "Sincerely,";
						break;
					case '2':
						$close = "Regards,";
						break;
					case '3':
						$close = "Best Regards,";
						break;
					case '4':
						$close = "Respectfully,";
						break;
					case '5':
						$close = "Thank You,";
						break;
					default:
						$sal = "Dear Friend,";
						break;
				}
				
				//Now, put together the body with the salutation, body, closing, reps name and link
				$embody = '<p>'.$sal.'<br /><br />'.$body.'</p>
						<p>'.$close.'<br /><br />'.$repfn.' '.$repln.'<br />'.$repgmail.'<br />'.$repphone.'<br />
						<a href="http://'.$imglink.'" target="_blank">'.$imglink.'</a></p>';
				
				$finalbody = wordwrap($embody, 70);
				
				$mail = new Zend_Mail();
				$mail->setBodyHtml($finalbody);
				$mail->addTo($contact_email);
				if ($_SESSION['rep_bcc'] == 'Y'){
					$mail->addBcc($repgmail);
				}
				$mail->setSubject($subject);
				$mail->setFrom($repgmail);
				//
				try {
					$mail->send();
					echo "Matching Phone Script Email Template sent.<br />";
					$mail_sent = true;
				} catch (Exception $ex) {
					echo "Failed to send the email template. " . $ex->getMessage() . "<br />";
					$mail_sent = false;
				}
				
			} else {
				//no matching email template found
				echo '<p>No matching email template on record.</p>';
				
			}	//end num_rows == 1
			
			//Write mail sent to comm history
			if ($mail_sent) {
				//Write email sent to the comm history for rep
				date_default_timezone_set($_SESSION['rep_tz']);
				$rightnow = date("Y-m-d H:i:s");
				
				//Comm History note - conversation or vm
				$vmorcv = '';
				switch ($calltype){
					case 'vm':
						$vmorcv = "Left Voice Mail - sent email template ".$emt_name;
						break;
					case 'cv':
						$vmorcv = "Had Conversation - sent email template ".$emt_name;
						break;
				}
				$emsql = "INSERT INTO communication_matrix (rep_id, contact_id, comm_type, comm_note, comm_date) 
									VALUES (?, ?, 'EM', ?, ?)";

				//prepare statement
				$stmt = mysqli_prepare($dbc, $emsql);
				//bind variables to statement
				mysqli_stmt_bind_param($stmt, 'ssss', $repsid, $contactid, $vmorcv, $rightnow);
				//execute query
				mysqli_stmt_execute($stmt);
				
				if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
					mysqli_stmt_close($stmt);
					echo '<p>Email record saved to history.</p>';
				} else {
					echo '<p>Email history insert problem.</p>';
				}
			} //end if($mailsent)
			mysqli_free_result($r);
		} //end if($r)
		
		
	} //end $sendmail = 'Y'
	//***********************************************************************
	
	//**************************************
	//SEND TEXT if sendtext == Y
	//**************************************
	if ($sendtext == 'Y') {
	
		$tt_body = $tt_name = '';
		// Make the query to get matching text template for phone script:
		$q = "SELECT text_template_name, text_body 
			  FROM vfg_rep_text_templates
			  WHERE rep_id = '$repsid' 
			  AND text_template_name = '$thisscript'
			  LIMIT 1";	
			
		$r = mysqli_query ($dbc, $q); // Run the query.

		if ($r) { // If it ran OK, get the data if there is a record.
			if (mysqli_num_rows($r) == 1){
				// Fetch the needed fields:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$tt_name = $row['text_template_name'];
					$tt_body = addslashes($row['text_body']);
				}
				
				// SET UP DIALER to send text - ONLY if rep's unique_id field is set
				//
				if ($dialer_unique_id != ''){
				
					//initialize status
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
						  WHERE unique_id = '$dialer_unique_id' 
						  AND phone = '$contact_phone' 
						  AND comm_note = '$tt_body' ";
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
										
						//NOTE: Change this next routine....send the phone # into this program via ajax call.
						//      No need to go to db to retrieve this.
						if ($contact_phone != ''){
							$insquery = "INSERT INTO temp_contacts (id_temp_contact, unique_id, phone, comm_note, timestamp, status) 
										VALUES ('$tempcontactid', '$dialer_unique_id', '$contact_phone', '$tt_body', '$rightnow', '$status')";
							$result = mysqli_query ($dbc, $insquery);
							if (mysqli_affected_rows($dbc) == 1){
								echo 'Text sent via Dialer.<br />';
							} else {
								echo 'Dialer problem with text message.';
								//echo '<p>'.mysqli_error($dbc).'</p>';
							}
							
							//Write text sent COMM MATRIX
							date_default_timezone_set($_SESSION['rep_tz']);
							$rightnow = date("Y-m-d H:i:s");
							
							//Comm History note - conversation or vm
							$vmorcv = '';
							switch ($calltype){
								case 'vm':
									$vmorcv = "Left Voice Mail - sent text template ".$tt_name;
									break;
								case 'cv':
									$vmorcv = "Had Conversation - sent text template ".$tt_name;
									break;
							}
							$txtsql = "INSERT INTO communication_matrix (rep_id, contact_id, comm_type, comm_note, comm_date) 
												VALUES (?, ?, 'TM', ?, ?)";

							//prepare statement
							$stmt = mysqli_prepare($dbc, $txtsql);
							//bind variables to statement
							mysqli_stmt_bind_param($stmt, 'ssss', $repsid, $contactid, $vmorcv, $rightnow);
							//execute query
							mysqli_stmt_execute($stmt);
							
							if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
								mysqli_stmt_close($stmt);
								echo '<p>Text record saved to history.</p>';
							} else {
								echo '<p>Text history insert problem.</p>';
							}
						
						} else {
							
							echo '<p>This contact\'s phone number is blank.  Can\'t send text via Dialer.</p>';
							
						}	//end if contact_phone != ''
						
					
					} else {
						echo '<p>The Dialer has already received this text message for this contact.</p>';
					
					}	//end if ($textrec_does_not_exist)
					
					
				
				} //end if($dialer_unique_id)
				
			} else {
			
				//no matching text template found
				echo '<p>No matching text template on record.</p>';
			
			}	//end num_rows == 1
			
			mysqli_free_result($r);
			
		} //end if($r)
	
	} //end if ($sendtext)
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>