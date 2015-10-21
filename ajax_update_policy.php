<?php
session_start();
# ajax_update_policy.php

	$repsid = $_SESSION['rep_id'];
	$repsvfgid = $_SESSION['vfgrep_id'];
	
	$policy_change = false;
	//Name on contact record
	$policy_account_fn = $_SESSION['policy_account_firstname'];
	$policy_account_ln = $_SESSION['policy_account_lastname'];
	
	//$_POST vars
	$cid = $_POST['cid'];
	$p_num = $_POST['policy_num'];
	$p_fname = $_POST['firstname'];
	$p_lname = $_POST['lastname'];
	$p_hs = $_POST['household_status'];
	$p_type = $_POST['policy_type'];
	$p_carrier = $_POST['carrier'];
	$p_premium = $_POST['target_premium'];
	$p_inviter = $_POST['inviter'];
	$p_manager = $_POST['manager'];
	$p_consultant = $_POST['consultant'];
	
	//Email Stuff
	$consult_gmail = $_SESSION['rep_gmail'];
	$consult_gpass = $_SESSION['rep_gpass'];
	$consult_fname = $_SESSION['rep_firstname'];
	$consult_lname = $_SESSION['rep_lastname'];
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
	
	
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	// STEP 1 - Insert or Update Policy Info
	//********************************************************
	$policy_exists = false;
	//Check for existing policy
	$q = "SELECT contact_id, policy_num
		   FROM policy
		   WHERE contact_id = '$cid'
		   AND policy_num = '$p_num' LIMIT 1";
	$r = mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) == 1){
		$policy_exists = true;
	}
	mysqli_free_result($r);
	
	if ($policy_exists){	//do update
		
		$sql = "UPDATE policy
				SET rep_id = '$p_inviter',
					manager_id = '$p_manager',
					consultant_id = '$p_consultant',
					firstname = '$p_fname', 
					lastname = '$p_lname', 
					policy_type = '$p_type',
					household_status = '$p_hs',
					carrier = '$p_carrier',
					target_premium = '$p_premium'
				WHERE contact_id = '$cid' 
				AND policy_num = '$p_num' LIMIT 1";
		$rs= mysqli_query($dbc, $sql);	
		if (mysqli_affected_rows($dbc) == 1){
			echo 'Update successful.<br />';
			$policy_change = true;
		} else {
			echo 'No Data Change.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		
		}	// close mysqli_affected_rows
	
	} else {		//do insert
		
		$query = "INSERT INTO policy (contact_id, policy_num, rep_id, manager_id, consultant_id,
									  firstname, lastname, policy_type, household_status, carrier, target_premium) 
						VALUES ('$cid', '$p_num', '$p_inviter', '$p_manager', '$p_consultant', '$p_fname', '$p_lname', 
								'$p_type', '$p_hs', '$p_carrier', '$p_premium')";
		//echo $query;
						
		$rs = mysqli_query ($dbc, $query);
		if (mysqli_affected_rows($dbc) == 1){
			echo 'Insert successful.<br />';
			$policy_change = true;
		} else {
			echo 'Insert problem.';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		
		}	// close mysqli_affected_rows
	}
	
	// STEP 2 - Send Emails to Inviter and Manager - IF change/new policy
	//**********************************************************************
	
	if ($policy_change){
		$sp_first = $sp_last = $sp_type = $sp_status = $sp_carrier = $sp_tp = '';
		//Get the policy info that was just changed
		$q = "SELECT a.firstname, a.lastname, b.policy_desc, c.household_desc, d.carrier, a.target_premium
		  FROM policy a 
		  INNER JOIN ref_policy_type b ON a.policy_type = b.policy_code
		  INNER JOIN ref_household_status c ON a.household_status = c.household_code
		  INNER JOIN ref_carriers d ON a.carrier = d.carrier_code
		  WHERE a.contact_id = '$cid'
		  AND policy_num = '$p_num' LIMIT 1";
		$r = mysqli_query ($dbc, $q); // Run the query.
		if (mysqli_num_rows($r) == 1){
			//Build the options string with the template ids
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$sp_first = $row['firstname'];
				$sp_last = $row['lastname'];
				$sp_type = $row['policy_desc'];
				$sp_status = $row['household_desc'];
				$sp_carrier = $row['carrier'];
				$sp_tp = $row['target_premium'];
			}
			mysqli_free_result($r);
		} 
		//Set subject line
		$subject = '';
		// Set the subject line.
		if ($policy_exists){	//existing policy
			$subject = 'Policy Change For Contact:  '.$policy_account_fn.' '.$policy_account_ln.'.';
		} else {	//new policy
			$subject = 'New Policy For:  '.$policy_account_fn.' '.$policy_account_ln.'.';
		}
		
		//Inviter's Info
		$inviter_fname = $inviter_lname = $inviter_gmail = '';
		//Need the Inviter's name and email from reps table
		$q = "SELECT firstname, lastname, gmail_acct FROM reps WHERE rep_id = '$p_inviter' LIMIT 1";
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
		$q = "SELECT firstname, lastname, gmail_acct FROM reps WHERE vfgrepid = '$p_manager' LIMIT 1";
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
		if ($policy_exists){	//a change
			$embody = '<p>Hi '.$inviter_fname.',<br /><br />There has been an update/change to an existing policy on '.$policy_account_fn.' '.$policy_account_ln.
				 '\'s record.  Here are the details.<br /><br />'.
				 '<p>Policy Number (in our system):  '.$p_num.'<br />'.
				 'First Name:  '.$sp_first.'<br />'.
				 'Last Name:  '.$sp_last.'<br />'.
				 'Policy Type:  '.$sp_type.'<br />'.
				 'Household Status:  '.$sp_status.'<br />'.
				 'Carrier:  '.$sp_carrier.'<br />'.
				 'Target Premium:  '.$sp_tp.'<br />';
				 
		} else {	//a new policy
		
			$embody = '<p>Hi '.$inviter_fname.',<br /><br />A new policy has been submitted for '.$policy_account_fn.' '.$policy_account_ln.
				 '\'s record.  Here are the details.<br /><br />'.
				 '<p>Policy Number (in our system):  '.$p_num.'<br />'.
				 'First Name:  '.$sp_first.'<br />'.
				 'Last Name:  '.$sp_last.'<br />'.
				 'Policy Type:  '.$sp_type.'<br />'.
				 'Household Status:  '.$sp_status.'<br />'.
				 'Carrier:  '.$sp_carrier.'<br />'.
				 'Target Premium:  '.$sp_tp.'<br />';
		}
		
		//SEND MAIL TO INVITER
		//$finalbody = wordwrap($embody, 70);
		$mail = new Zend_Mail();
		$mail->setBodyHtml($embody);
		$mail->addTo($inviter_gmail);
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
				
			//$finalbody = wordwrap($embody, 70);
			$mail = new Zend_Mail();
			$mail->setBodyHtml($embody);
			$mail->addTo($manager_gmail);
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
		
	} //end if($policy_change)
	
	
	mysqli_close($dbc); // Close the database connection.
?>