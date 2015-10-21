<?php
session_start();
	
	$usegmail = false;	// use gmail smtp or php sendmail
	$mailsent = false;	// mail was sent or it failed
	
	//See if rep has their gmail credentials set
	$repgmail = $_SESSION['rep_gmail'];
	$repgpass = $_SESSION['rep_gpass'];
	if ($repgmail != '' && $repgpass != ''){ 
		$usegmail = true; 
	}
	echo $usegmail;
	//Ajax send mail
	//
	//Expected $_POST vars:
	// templateid, to_first, to_last, to_email, from_email
	//*****************************************************
	$repsid = $_SESSION['rep_id'];	//from login.php
	$repfn =  $_SESSION['rep_firstname'];	//from login.php
	$repln =  $_SESSION['rep_lastname'];	//from login.php
	
	//$_POST VARS
	$current_contact = $_POST['currentid'];
	$tcat = $_POST['templatecat'];
	$tid = $_POST['templateid'];	//for custom: template name   for global: 1,2,3,4,5
	$tofirst = $_POST['to_first'];
	$tolast = $_POST['to_last'];
	$toemail = $_POST['to_email'];
	$fromemail = $_POST['from_email'];
	
	$vfg_from_mail = 'vfg-mailer@vfgcontact.com';
	
	//includes for MYSQL, etc
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	
	unset($_SESSION['email_response']);
	
	//DB Connection
	require_once (MYSQL);

	$subject = $salutation = $body = $imglink = $closing = '';
	
	//CUSTOM TEMPLATES
	if ($tcat == 'custom'){
		// Make the query to get template data:
		$q = "SELECT t_id, template_name, subject, salutation, body,
					img_link, closing 
			  FROM vfg_customemail_settings
			  WHERE rep_id = '$repsid' 
			  AND t_id = '$tid'
			  LIMIT 1";	
			
		$r = mysqli_query ($dbc, $q); // Run the query.

		if ($r) { // If it ran OK, get the data if there is a record.
			if (mysqli_num_rows($r) == 1){
				// Fetch the needed fields:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$subject = $row['subject'];
					$salutation = $row['salutation']; 
					$body = stripslashes($row['body']);
					$imglink = $row['img_link'];
					$closing = $row['closing'];
					$templatename = $row['template_name'];
				}
				//echo 'Got the template record.';
				
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
						$sal = "Dear ".$tofirst.",";
						break;
					case '3':
						$sal = "Greetings Friend,";
						break;
					case '4':
						$sal = "Greetings ".$tofirst.",";
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
				$embody = '<html>
						<body>
						<p>'.$sal.'<br /><br />'.$body.'</p>
						<p>'.$close.'<br /><br />'.$repfn.' '.$repln.'<br />'.$fromemail.'<br />
						<a href="http://'.$imglink.'" target="_blank">'.$imglink.'</a></p>
						</body></html>';
				
				$finalbody = wordwrap($embody, 70);
				//************************************************************************************
				// IF : the rep filled in their gmail credentials (gmail and password) - use GMAIL
				//      service to send email.
				//      Otherwise, use PHP sendmail
				//************************************************************************************
				if ($usegmail){
					
					require_once ('Zend/Mail.php');
					require_once ('Zend/Mail/Transport/Sendmail.php');

					$tr = new Zend_Mail_Transport_Sendmail('smtp.gmail.com', array(
						'auth' => 'login',
						'username' => $repgmail,
						'password' => $repgpass,
						'ssl' => 'ssl',
						'port' => 465)
					);
					Zend_Mail::setDefaultTransport($tr);

					$mail = new Zend_Mail();
					$mail->setBodyHtml($finalbody);
					$mail->addTo($toemail);
					$mail->setSubject($subject);
					$mail->setFrom($repgmail);
					//
					try {
						$mail->send();
						$mailsent = true;
						//echo "Message sent!<br />\n";
					} catch (Exception $ex) {
						echo "Failed to send gmail mail! " . $ex->getMessage() . "<br />\n";
					}
					
					
				} else {	//Use PHP send mail
				
					$headers = "From: mailer@vfgcontact.com" . "\r\n";
					$headers = "Reply-To: " . $fromemail . "\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
					//Body
				
					try {
						mail($toemail, $subject, $finalbody, $headers);
						$mailsent = true;
					} catch (Exception $ex) {
						echo "Failed to send PHP mail! " . $ex->getMessage() . "<br />\n";
					}
				}
				
				//echo $finalbody;
				//Turn on when this hits PROD
				//
				if ($mailsent) {
					
					//Alter templatename field to say "Sent template name"
					$tphrase = "Sent email template ".$templatename;
					
					date_default_timezone_set($_SESSION['rep_tz']);
					$rightnow = date("Y-m-d H:i:s");
					//INSERT COMMUNICATION HISTORY DATA
					$insertsql = "INSERT INTO communication_matrix (rep_id, contact_id, comm_type, comm_note, comm_date) 
						VALUES (?, ?, 'EM', ?, ?)";
					//prepare statement
					$stmt = mysqli_prepare($dbc, $insertsql);
					//bind variables to statement
					mysqli_stmt_bind_param($stmt, 'ssss', $repsid, $current_contact, $tphrase, $rightnow);
					//execute query
					mysqli_stmt_execute($stmt);
					
					if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful	
						mysqli_stmt_close($stmt);	
					}
				} else {
					echo 'Not Sent.';
				}
			}	//close num_rows == 1
			
		} else {
			echo '$R problem.';
		}
	
	} //close $tcat == 'custom'
	
	//CUSTOM TEMPLATES
	if ($tcat == 'global'){
	
		$link = "#";
		$link1 = $link2= $link3 = $link4 = $link5 = '';
		// Make the query to get this template's link:
		$q = "SELECT link1, link2, link3, link4, link5 
			  FROM vfg_global_imagelinks
			  WHERE rep_id = '$repsid' 
			  LIMIT 1";	
			
		$r = mysqli_query ($dbc, $q); // Run the query.

		if ($r) { // If it ran OK, get the data if there is a record.
			if (mysqli_num_rows($r) == 1){
				// Fetch the needed fields:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$link1 = $row['link1'];
					$link2 = $row['link2'];
					$link3 = $row['link3'];
					$link4 = $row['link4'];
					$link5 = $row['link5'];
				}
			}
			mysqli_free_result($r);
		}
		
		$global_emailbody = '';
		//Determine which link to build html on
		switch ($tid){
			case '1':
				$global_emailbody = getGlobalTemplate($tid, $link1);
				break;
			case '2':
				$global_emailbody = getGlobalTemplate($tid, $link2);
				break;
			case '3':
				$global_emailbody = getGlobalTemplate($tid, $link3);
				break;
			case '4':
				$global_emailbody = getGlobalTemplate($tid, $link4);
				break;
			case '5':
				$global_emailbody = getGlobalTemplate($tid, $link5);
				break;
		}
		
		//Send the mail
		$headers = "From: mailer@vfgcontact.com" . "\r\n";
		$headers = "Reply-To: " . $fromemail . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		$subject = 'VFG Email';
		
		mail($toemail, $subject, $global_emailbody, $headers);		
				
		//echo $global_emailbody;
		
	}	//close global template if
	
	mysqli_close($dbc);
?>