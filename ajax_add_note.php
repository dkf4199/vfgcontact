<?php
session_start();

	// This rep's credentials
	$repid = $_SESSION['rep_id'];
	$repfirst = $_SESSION['rep_firstname'];
	$replast = $_SESSION['rep_lastname'];
	$repname = $repfirst.' '.$replast;
	
	// ZEND MAIL SETUP
	$repgmail = $_SESSION['rep_gmail'];
	$repgpass = $_SESSION['rep_gpass'];
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
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$insert_success = '';
	
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	
	$contactid = $_POST['cid'];
	date_default_timezone_set($_SESSION['rep_tz']);
	$rightnow = date("Y-m-d H:i:s");
	
	$contact_first = $contact_last = '';
	if (isset($_POST['cfirst'])){
		$contact_first = $_POST['cfirst'];
	}
	if (isset($_POST['clast'])){
		$contact_last = $_POST['clast'];
	}
	$contact_name = $contact_first.' '.$contact_last;
	$inv_name = $inv_phone = $inv_email = '';
	$man_name = $man_phone = $man_email = '';
	$con_name = $con_phone = $con_email = '';
	$addl_name = $addl_phone = $addl_email = '';

	if (isset($_POST['invname'])){
		$inv_name = $_POST['invname'];
	}
	if (isset($_POST['invphone'])){
		$inv_phone = $_POST['invphone'];
	}
	if (isset($_POST['invemail'])){
		$inv_email = $_POST['invemail'];
	}
	if (isset($_POST['manname'])){
		$man_name = $_POST['manname'];
	}
	if (isset($_POST['manphone'])){
		$man_phone = $_POST['manphone'];
	}
	if (isset($_POST['manemail'])){
		$man_email = $_POST['manemail'];
	}
	if (isset($_POST['conname'])){
		$con_name = $_POST['conname'];
	}
	if (isset($_POST['conphone'])){
		$con_phone = $_POST['conphone'];
	}
	if (isset($_POST['conemail'])){
		$con_email = $_POST['conemail'];
	}
	if (isset($_POST['addlname'])){
		$addl_name = $_POST['addlname'];
	}
	if (isset($_POST['addlphone'])){
		$addl_phone = $_POST['addlphone'];
	}
	if (isset($_POST['addlemail'])){
		$addl_email = $_POST['addlemail'];
	}
	
	$note = mysqli_real_escape_string($dbc, trim($_POST['note']));
	
	// Email Subject line
	$subject = 'New Note Added to '.$contact_first.' '.$contact_last.'\'s Record.';
	
	$insertsql = "INSERT INTO notes (rep_id, contact_id, rep_first, rep_last, note, note_date) 
						VALUES (?, ?, ?, ?, ?, ?)";

	//prepare statement
	$stmt = mysqli_prepare($dbc, $insertsql);
	//bind variables to statement
	mysqli_stmt_bind_param($stmt, 'ssssss', $repid, $contactid, $repfirst, $replast, $note, $rightnow);
	//execute query
	mysqli_stmt_execute($stmt);
	
	if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
		mysqli_stmt_close($stmt);
		echo "Note added.";
		
		// Send Email Notifications to other reps on record
		// Inviter first, Manager, Consultant, Additional Rep
		if ($inv_name != ''){
			if ($repname != $inv_name) {
				$mail = new Zend_Mail();
				$mail_body = '<p>A note was added to one of your contacts that you invited to the system.</p>
							<p>Contact Name:  '.$contact_name.'</p>
							<p>Note Added By:  '.$repname.'</p>';
				$mail->setBodyHtml($mail_body);
				$mail->addTo($inv_email);
				if ($_SESSION['rep_bcc'] == 'Y'){
					$mail->addBcc($repgmail);
				}
				$mail->setSubject($subject);
				$mail->setFrom($repgmail);
				//
				try {
					$mail->send();
				} catch (Exception $ex) {
					// Don't do anything right now......
					//echo "Failed to send gmail mail! " . $ex->getMessage() . "<br />";
				}
			}
		}
		
		if ($man_name != ''){
			if ($repname != $man_name){
				$mail = new Zend_Mail();
				$mail_body = '<p>A note was added to one of your contacts that you are the assigned manager.</p>
							<p>Contact Name:  '.$contact_name.'</p>
							<p>Note Added By:  '.$repname.'</p>';
				$mail->setBodyHtml($mail_body);
				$mail->addTo($man_email);
				if ($_SESSION['rep_bcc'] == 'Y'){
					$mail->addBcc($repgmail);
				}
				$mail->setSubject($subject);
				$mail->setFrom($repgmail);
				//
				try {
					$mail->send();
				} catch (Exception $ex) {
					// Don't do anything right now......
					//echo "Failed to send gmail mail! " . $ex->getMessage() . "<br />";
				}
			}
		}
		
		if ($con_name != ''){
			if ($repname != $con_name){
				$mail = new Zend_Mail();
				$mail_body = '<p>A note was added to one of your contacts that you are the assigned consultant.</p>
							<p>Contact Name:  '.$contact_name.'</p>
							<p>Note Added By:  '.$repname.'</p>';
				$mail->setBodyHtml($mail_body);
				$mail->addTo($con_email);
				if ($_SESSION['rep_bcc'] == 'Y'){
					$mail->addBcc($repgmail);
				}
				$mail->setSubject($subject);
				$mail->setFrom($repgmail);
				//
				try {
					$mail->send();
				} catch (Exception $ex) {
					// Don't do anything right now......
					//echo "Failed to send gmail mail! " . $ex->getMessage() . "<br />";
				}
			}
		}
		
		if ($addl_name != ''){
			if ($repname != $addl_name){
				$mail = new Zend_Mail();
				$mail_body = '<p>A note was added to one of your contacts that you are the assigned additional rep.</p>
							<p>Contact Name:  '.$contact_name.'</p>
							<p>Note Added By:  '.$repname.'</p>';
				$mail->setBodyHtml($mail_body);
				$mail->addTo($addl_email);
				if ($_SESSION['rep_bcc'] == 'Y'){
					$mail->addBcc($repgmail);
				}
				$mail->setSubject($subject);
				$mail->setFrom($repgmail);
				//
				try {
					$mail->send();
				} catch (Exception $ex) {
					// Don't do anything right now......
					//echo "Failed to send gmail mail! " . $ex->getMessage() . "<br />";
				}
			}
		}
	
	} else {
		echo "Note failure.";
	}
	
	mysqli_close($dbc); // Close the database connection.
?>