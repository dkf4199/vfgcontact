<?php
session_start();

	// VARS coming into this program
	// tierstatus, inviter's rep_id, manager's vfgid
	// consultant's stuff is in $_SESSION
	$inviter_id = $_POST['inviter'];
	$manager_id = $_POST['manager'];
	$tierstatus = $_POST['tierstatus'];
	$contact_first = $_POST['cfirst'];
	$contact_last = $_POST['clast'];
	
	//set up the vars we need for consultant...
	$consult_gmail = $_SESSION['rep_gmail'];
	$consult_gpass = $_SESSION['rep_gpass'];
	$consult_fname = $_SESSION['rep_firstname'];
	$consult_lname = $_SESSION['rep_lastname'];
	
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	$mailsent = false;	// mail was sent or it failed
	
	require_once ('Zend/Mail.php');
	require_once ('Zend/Mail/Transport/Sendmail.php');

	$tr = new Zend_Mail_Transport_Sendmail('smtp.gmail.com', array(
		'auth' => 'login',
		'username' => $consult_gmail,
		'password' => $consult_gpass,
		'ssl' => 'ssl',
		'port' => 465)
	);
	Zend_Mail::setDefaultTransport($tr);
	
	$subject = '';
	// Set the subject line.
	if ($tierstatus == '3F'){
		$subject = 'Missed Tier 3 Meeting With Contact '.$contact_first.' '.$contact_last.'.';
	}
	if ($tierstatus == '4F'){
		$subject = 'Missed Tier 4 Meeting With Contact '.$contact_first.' '.$contact_last.'.';
	}
	
	
	//Inviter's Info
	$inviter_fname = $inviter_lname = $inviter_gmail = '';
	//Need the Inviter's name and email from reps table
	$q = "SELECT firstname, lastname, gmail_acct FROM reps WHERE rep_id = '$inviter_id' LIMIT 1";
	//RUN QUERY
	$r = mysqli_query ($dbc, $q);
	if (mysqli_num_rows($r) == 1) {
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$inviter_fname = $row['firstname'];
			$inviter_lname = $row['lastname'];
			$inviter_gmail = $row['gmail_acct'];
		}
	}
	mysqli_free_result($r);
	
	//Manager's Info
	$manager_fname = $manager_lname = $manager_gmail = '';
	//Need the Manager's name and email from reps table
	$q = "SELECT firstname, lastname, gmail_acct FROM reps WHERE vfgrepid = '$manager_id' LIMIT 1";
	//RUN QUERY
	$r = @mysqli_query ($dbc, $q);
	if (mysqli_num_rows($r) == 1) {
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$manager_fname = $row['firstname'];
			$manager_lname = $row['lastname'];
			$manager_gmail = $row['gmail_acct'];
		}
	}
	mysqli_free_result($r);
	
	//*******************************
	// Send Mail To Inviter
	//*******************************
	//
	$embody = '<p>Hi '.$inviter_fname.',<br /><br />Your contact, '.$contact_first.' '.$contact_last.
			 ' missed their scheduled meeting with me today.  Please check the notes section of their record for details.</p>'.
			 '<p>Thanks,<br /><br />'.$consult_fname.' '.$consult_lname.'</p>';
	
	$finalbody = wordwrap($embody, 70);
	$mail = new Zend_Mail();
	$mail->setBodyHtml($finalbody);
	$mail->addTo($inviter_gmail);
	if ($_SESSION['rep_bcc'] == 'Y'){
		$mail->addBcc($consult_gmail);
	}
	$mail->setSubject($subject);
	$mail->setFrom($consult_gmail);
	//
	try {
		$mail->send();
		$mailsent = true;
		echo "Email sent to Inviter.<br />";
	} catch (Exception $ex) {
		echo "Failed to send email to Inviter. " . $ex->getMessage() . "<br />";
	}
	//**********************************************************************************
	
	//********************************************
	// Send Mail To Manager, if one is on record
	//********************************************
	//
	if ($manager_gmail != ''){
		$embody = '<p>Hi '.$manager_fname.',<br /><br />Your contact, '.$contact_first.' '.$contact_last.
				 ' missed their scheduled meeting with me today.  Please check the notes section of their record for details.</p>'.
				 '<p>Thanks,<br /><br />'.$consult_fname.' '.$consult_lname.'</p>';
		
		$finalbody = wordwrap($embody, 70);
		$mail = new Zend_Mail();
		$mail->setBodyHtml($finalbody);
		$mail->addTo($manager_gmail);
		if ($_SESSION['rep_bcc'] == 'Y'){
			$mail->addBcc($consult_gmail);
		}
		$mail->setSubject($subject);
		$mail->setFrom($consult_gmail);
		//
		try {
			$mail->send();
			$mailsent = true;
			echo "Email sent to Manager.<br />";
		} catch (Exception $ex) {
			echo "Failed to send email to Manager. " . $ex->getMessage() . "<br />";
		}
	}
	
	mysqli_close($dbc);
	
?>
