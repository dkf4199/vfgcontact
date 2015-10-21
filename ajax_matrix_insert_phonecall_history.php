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
	
	$phonescript = $_POST['script'];	// which script was used
	$calltype = $_POST['calltype'];		// voice mail or conversation
	
	$sendmail = $_POST['sendmail'];		// trifecta send mail flag
	$sendtext = $_POST['sendtext'];		// trifecta send text flag
	
	$thisscript = '';
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
		
		// 11/01/2013 Change to a canned email.  Not a matching template that
		//            the rep has to configure.
		//
		// $phonescript holds which script rep used
		// $calltype indicates if its a vm or cv
		//
		switch ($phonescript){
			case 'intro':
				$subject = $contact_fname.', I am following up on your request for more info for Virtual Financial Group.';
				$body = '<p>This is '.$repfn.' '.$repln.' with Virtual Financial Group.  You had reqested info 
						from me online and I am following up to see if you had a chance to see our videos 
						on our unique business model & powerful income opportunity?</p>
						<p>When can we set a time to follow up online after you watch the video?</p>';
				break;
			case 'aftervideo':
				$subject = $contact_fname.', what did you think of the videos?';
				$body = '<p>This is '.$repfn.' '.$repln.' with Virtual Financial Group trying again to touch base 
				with you on the info you watched in the videos.</p><p>I wanted to see if you had any questions
				regarding our unique business model & powerful income opportunity.</p>
				<p>When can we set a time to discuss the videos?</p>';
				break;
			case 'phoneattempt2':
				$subject = $contact_fname.', I am following up again on your request for more info from VFG.';
				$body = '<p>This is '.$repfn.' '.$repln.' with Virtual Financial Group trying again to touch base 
				with you on the info you had reqested from me online.</p><p>I wanted to see if you had a chance to 
				see our Videos on our unique business model & powerful income opportunity?</p>
				<p>When can we set a time to follow up online after you watch the video?</p>';
				break;
		}
		
		$finalbody = wordwrap($body, 70);
						
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
			echo "Phone Script email sent.<br />";
			$mail_sent = true;
		} catch (Exception $ex) {
			echo "Failed to send matching email." . $ex->getMessage() . "<br />";
			$mail_sent = false;
		}
		
		//Write email sent to comm history
		if ($mail_sent) {
			//Write email sent to the comm history for rep
			date_default_timezone_set($_SESSION['rep_tz']);
			$rightnow = date("Y-m-d H:i:s");
			
			//Comm History note - conversation or vm
			$vmorcv = '';
			switch ($calltype){
				case 'vm':
					$vmorcv = "Left Voice Mail - sent corresponding email.";
					break;
				case 'cv':
					$vmorcv = "Had Conversation - sent corresponding email.";
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
		
	} //end $sendmail = 'Y'
	//***********************************************************************
	
	//**************************************
	//SEND TEXT if sendtext == Y
	//**************************************
	if ($sendtext == 'Y') {
	
		// 11/01/2013 Change to a canned email.  Not a matching template that
		//            the rep has to configure.
		//
		// $phonescript holds which script rep used
		// $calltype indicates if its a vm or cv
		//
		switch ($phonescript){
			case 'intro':
				$text_msg = 'This is '.$repfn.' '.$repln.' with Virtual Financial Group you had reqested info from me online '. 
				'and I am following up to see if you had a chance to see our videos on our unique business model and '.
				'powerful income opportunity? When can we set a time to follow up online after you watch the video?';
				break;
			case 'aftervideo':
				$text_msg = 'This is '.$repfn.' '.$repln.' with Virtual Financial Group trying to get in touch with you again. '. 
				'Do you have any questions on the videos regarding our unique business model & powerful income opportunity? '. 
				'When can we set a time to discuss and answer any questions you may have?';
				break;
			case 'phoneattempt2':
				$text_msg = 'This is '.$repfn.' '.$repln.' with Virtual Financial Group trying to get in touch with you again. '. 
				'Have you had a chance to see our Videos on our unique business model & powerful income opportunity? '. 
				'When can we set a time to follow up online after you watch the video?';
				break;
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
				$query = "SELECT id_temp_contact 
						  FROM temp_contacts 
						  WHERE id_temp_contact = '$tempcontactid' ";
				$rs = @mysqli_query ($dbc, $query);
				if (mysqli_num_rows($rs) != 1) {
					  //id is unique
					  $idexists = false;
				}
				mysqli_free_result($rs);
				
			} while ($idexists);
			
			//CHECK - see if record with this text already exists in the table.
			// Query temp_contacts and see if there is already a record in the table
			// for this unique_id - phone number - text combination.
			// If there is = do NOT add another!
			$textrec_does_not_exist = TRUE;
			$q = "SELECT unique_id, phone, comm_note
				  FROM temp_contacts 
				  WHERE unique_id = '$dialer_unique_id' 
				  AND phone = '$contact_phone' 
				  AND comm_note = '$text_msg' ";
			//run it
			$rs = @mysqli_query ($dbc, $q);
			if (mysqli_num_rows($rs) >= 1) {
				//id is unique
				$textrec_does_not_exist = false;
			}
			mysqli_free_result($rs);
		
		
			//NOTE: Change this next routine....send the phone # into this program via ajax call.
			//      No need to go to db to retrieve this.
			if ($contact_phone != ''){
				$text_insert_success = false;
				
				if ($textrec_does_not_exist){
					$insquery = "INSERT INTO temp_contacts (id_temp_contact, unique_id, phone, comm_note, timestamp, status) 
								VALUES ('$tempcontactid', '$dialer_unique_id', '$contact_phone', '$text_msg', '$rightnow', '$status')";
					$result = mysqli_query ($dbc, $insquery);
					if (mysqli_affected_rows($dbc) == 1){
						echo 'Corresponding text sent via Dialer.<br />';
						$text_insert_success = true;
					} else {
						echo 'Dialer problem with text message.';
						//echo '<p>'.mysqli_error($dbc).'</p>';
					}
				} else {
					echo '<p>The Dialer has already received this text message record.</p>';
				}
			
			
			} //end if($contact_phone)
			
			//Write text sent to the comm history for rep - IF text successfully entered for Dialer
			
			if ($text_insert_success){
				date_default_timezone_set($_SESSION['rep_tz']);
				$rightnow = date("Y-m-d H:i:s");
				
				//Comm History note, sent text - conversation or vm
				$vmorcv = '';
				switch ($calltype){
					case 'vm':
						$vmorcv = "Left Voice Mail - sent corresponding text.";
						break;
					case 'cv':
						$vmorcv = "Had Conversation - sent corresponding text.";
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
					echo '<p>Text record saved to communication history.</p>';
				} else {
					echo '<p>Communication history record problem.</p>';
				}
			
			}	//end if($text_insert_success)
		
		} //end if($dialer_unique_id)
				
	} //end if ($sendtext)
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>