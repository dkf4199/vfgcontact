<?php
session_start();
	
	$mailsent = false;	// mail was sent or it failed
	
	//See if rep has their gmail credentials set
	$repgmail = $_SESSION['rep_gmail'];
	$repgpass = $_SESSION['rep_gpass'];
	$repphone = '';
	if (isset($_SESSION['rep_phone'])){
		$repphone = $_SESSION['rep_phone'];
	}
	
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
	
	
	//Ajax send mail
	//
	//Expected $_POST vars:
	// templateid, to_first, to_last, to_email, from_email
	//*****************************************************
	$repsid = $_SESSION['rep_id'];			//from login.php
	$repfn =  $_SESSION['rep_firstname'];	//from login.php
	$repln =  $_SESSION['rep_lastname'];	//from login.php
	
	//$_POST VARS
	$current_contact = $_POST['currentid'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];
	$tofirst = $_POST['to_first'];
	$tolast = $_POST['to_last'];
	$toemail = $_POST['to_email'];
	$fromemail = $_POST['from_email'];
	
	$vfg_from_mail = 'vfgmailer@vfgcontact.com';
	
	//includes for MYSQL, etc
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	
	unset($_SESSION['email_response']);
	
	//DB Connection
	require_once (MYSQL);

	
				
	// Put the email together
	$embody = '<p>'.$body.'</p>';
	
	//$finalbody = wordwrap($embody, 70);
	
	

	$mail = new Zend_Mail();
	$mail->setBodyHtml($embody);
	$mail->addTo($toemail);
	if ($_SESSION['rep_bcc'] == 'Y'){
		$mail->addBcc($repgmail);
	}
	$mail->setSubject($subject);
	$mail->setFrom($repgmail);
	//
	try {
		$mail->send();
		$mailsent = true;
		echo "Blank email sent to ".$tofirst." ".$tolast.".<br />";
	} catch (Exception $ex) {
		echo "Failed to send blank gmail. " . $ex->getMessage() . "<br />";
	}
					
	if ($mailsent) {
		
		//Alter templatename field to say "Sent template name"
		$tphrase = "Sent blank email with subject line: ".$subject;
		
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
		} else {
			echo 'data insert problem.';
		}
		
	} else {
		echo 'Mailsent is false.<br />';
	}
				
	mysqli_close($dbc);
?>