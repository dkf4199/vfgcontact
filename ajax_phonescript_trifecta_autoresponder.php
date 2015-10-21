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
	$h_link = '';
	if (isset($_SESSION['homepage_link'])){
		$h_link = $_SESSION['homepage_link'];
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
	
	$phonescript = $_POST['script'];	// which phone script was used (intro1, intro2, finalcall)
	$calltype = $_POST['calltype'];		// voice mail or conversation
	
	$sendmail = $_POST['sendmail'];		// trifecta send mail flag
	$sendtext = $_POST['sendtext'];		// trifecta send text flag
	
	$thisscript = '';
	$tierstep = '';		//used at the end of this script to update contact's record with 3x3 call status
	$add_date = false;	//used at the end of this script to set next_action_date on first or second call
	//$thisscript = $phonescript;
	switch ($phonescript){
		case 'intro1':
			$thisscript = "Intro Call #1";
			$tierstep = '1B';
			$add_date = true;
			break;
		case 'intro2':
			$thisscript = "Intro Call #2";
			$tierstep = '1C';
			$add_date = true;
			break;
		case 'finalcall':
			$thisscript = "Final Intro Call";
			$tierstep = '1D';
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
	
	//INSERT CALL MADE INTO HISTORY
	//**************************************************************************************************
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
	//*****************************************************************************************************
	
	
	// SEND EMAIL if sendmail == Y
	//*****************************************************************************************************
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
		
		//SET UP CANNED EMAIL TO SEND
		switch ($phonescript){
			case 'intro1':
				$thisscript = "Intro Call #1";
				break;
			case 'intro2':
				$thisscript = "Intro Call #2";
				break;
			case 'finalcall':
				$thisscript = "Final Intro Call";
				break;
		}
		
		if($phonescript == 'intro1'){
			// subject line
			$subject = $contact_fname.' '.$contact_lname.'  I am following up on your request for more info for Virtual Financial Group. Did you get a chance to see the powerful short Videos yet?';
			
			//Now, put together the body with the salutation, body, closing, reps name and link
			$embody = '<p>This is '.$repfn.' '.$repln.' with Virtual Financial Group you had requested info from me online and I am 
						following up to see if you had a chance to see our Videos on our unique business model & powerful income opportunity?</p>
					   <p>My website is '.$h_link.'.  Please reply with a good time for me to call you?</p>
					   <p>Thank You,</p>
					   <p>'.$repfn.' '.$repln.'<br /><br />'.$repphone.'</p>';
		}
		if($phonescript == 'intro2'){
			// subject line
			$subject = $contact_fname.' '.$contact_lname.'  I am following up again on your request for more info from Virtual Financial Group. Have you had a chance to see the powerful videos yet?';
			
			//Now, put together the body with the salutation, body, closing, reps name and link
			$embody = '<p>This is '.$repfn.' '.$repln.' trying again to touch base with you on the info you had requested from me online.</p>
					   <p>I wanted to see if you had a chance to see our videos on our unique virtual business model & powerful income opportunity you can do from home?</p>
					   <p>My website is '.$h_link.'.  Please reply with a good time for me to call you?</p>
					   <p>Thank You,</p>
					   <p>'.$repfn.' '.$repln.'<br /><br />'.$repphone.'</p>';
		}
		if($phonescript == 'finalcall'){
			// subject line
			$subject = $contact_fname.' '.$contact_lname.'  I am following up again on your request for more info from Virtual Financial Group. Have you had a chance to see the powerful videos yet?';
			
			//Now, put together the body with the salutation, body, closing, reps name and link
			$embody = '<p>This is '.$repfn.' '.$repln.' with Virtual Financial Group.  I\'m trying one last time to touch base with you on the info you had requested from me online.</p>
					   <p>I wanted to see if you had a chance to see our videos on our unique virtual business model & powerful income opportunity you can do from home?</p>
					   <p>My website is '.$h_link.'.  Please reply with a good time for me to call you?</p>
					   <p>Thank You,</p>
					   <p>'.$repfn.' '.$repln.'<br /><br />'.$repphone.'</p>';
		}
		
		
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
			echo "Email for ".$thisscript." sent.<br />";
			$mail_sent = true;
		} catch (Exception $ex) {
			echo "Failed to send email for ".$thisscript."<br />".$ex->getMessage()."<br />";
			$mail_sent = false;
		}
				
						
		//Write mail sent to comm history
		if ($mail_sent) {
			//Write email sent to the comm history for rep
			if (isset($_SESSION['rep_tz'])){
				date_default_timezone_set($_SESSION['rep_tz']);
			}
			$rightnow = date("Y-m-d H:i:s");
			
			//Comm History note - conversation or vm
			$vmorcv = '';
			switch ($calltype){
				case 'vm':
					$vmorcv = 'Left Voice Mail - sent email for '.$thisscript;
					break;
				case 'cv':
					$vmorcv = 'Had Conversation - sent email for '.$thisscript;
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
	//***********************************************************************
	if ($sendtext == 'Y') {
	
		//SET UP CANNED TEXT TO SEND
		switch ($phonescript){
			case 'intro1':
				$thisscript = "Intro Call #1";
				break;
			case 'intro2':
				$thisscript = "Intro Call #2";
				break;
			case 'finalcall':
				$thisscript = "Final Intro Call";
				break;
		}
		
		$text_body = '';
		if($phonescript == 'intro1'){
			$text_body = $repfn.' '.$repln.' Virtual Financial Group trying to get in touch with you You requested info Watch the video Joinvfg.com on your phone then reply '.$repphone;
		}
		if($phonescript == 'intro2'){
			$text_body = $repfn.' '.$repln.' Virtual Financial Group trying to get in touch with you again about VFG Watch our short video Joinvfg.com on your cell then reply '.$repphone;		
		}
		if($phonescript == 'finalcall'){
			$text_body = $repfn.' '.$repln.' Virtual Financial Group trying to get in touch with you again about VFG Watch our short video Joinvfg.com on your cell then reply '.$repphone;		
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
				  AND comm_note = '$text_body' ";
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
								VALUES ('$tempcontactid', '$dialer_unique_id', '$contact_phone', '$text_body', '$rightnow', '$status')";
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
							$vmorcv = "Left Voice Mail - sent text for ".$thisscript;
							break;
						case 'cv':
							$vmorcv = "Had Conversation - sent text for ".$thisscript;
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
				
				
	} //end if ($sendtext)
	
	// UPDATE THE CONTACT'S TIER STATUS - DEPENDING ON THE PHONE SCRIPT USED
	// FIRST CALL - 1B
	// SECOND CALL - 1C
	// FINAL CALL - 1D
	
	//Today - +2 days
	$date = date("Y-m-d");
	$mod_date = strtotime($date."+ 2 days");
	$nad = date("Y-m-d",$mod_date);
	
	$updatesql = "UPDATE contacts
				SET tier_status ='$tierstep'";
	if ($add_date){
		$updatesql .= ", next_action_date = '$nad', 
						nad_set_by = '$repsid'";
	}
	$updatesql .= " WHERE contact_id = '$contactid' LIMIT 1"; 
		//RUN UPDATE QUERY
	$rs= mysqli_query($dbc, $updatesql);
	if (mysqli_affected_rows($dbc) == 1){
		echo '<p>Contact Tier-Status updated.</p>';
	} else {
		echo '<p>Tier Status no change.</p>';
		//echo '<p>'.mysqli_error($dbc).'</p>';
	}
		
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>