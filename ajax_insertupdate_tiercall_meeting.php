<?php
session_start();
# ajax_insertupdate_tiercall_meeting.php
	include ('includes/config.inc.php');
	require_once (MYSQL);
	
	$repsid = $_SESSION['rep_id'];
	$repsvfgid = $_SESSION['vfgrep_id'];
	$repgmail = $_SESSION['rep_gmail'];
	$repgpass = $_SESSION['rep_gpass'];
	$repfirst = $_SESSION['rep_firstname'];
	$replast = $_SESSION['rep_lastname'];
	
	$repsuniqueid = '';
	if (isset($_SESSION['unique_id'])){
		$repsuniqueid = $_SESSION['unique_id'];
	}
	
	//POST VARS
	$cid = $_POST['cid'];				// contact id
	$cfirst = $_POST['cfirst'];			// contact first
	$clast = $_POST['clast'];			// contact last
	$cemail = $_POST['cemail'];			// contact email
	$cphone = $_POST['cphone'];			// contact phone
	$ctimezone = $_POST['ctimezone'];	// contact timezone
	$inviter = $_POST['inviter'];		// inviter rep_id
	$manager = $_POST['manager'];		// manager vfgid
	$consultant = $_POST['consultant'];	// consultant vfgid
	$tier = $_POST['tier'];
	$m_date = $_POST['mdate'];
	$m_hour = $_POST['mhour'];
	$m_minute = $_POST['mminute'];
	$n_interval = $_POST['interval'];
	$send_email = $_POST['sendemail'];
	$send_text = $_POST['sendtext'];
	$from_phone = $_POST['fromphone'];
	
	//IF from_phone is 'Y' (meeting set from phone script modal for TIER 2 CALL) - override the tier with '2' NO MATTER WHAT
	if ($from_phone == 'Y'){
		$tier = '2';
	}
	
	// Check to see if MEETING ALREADY SCHEDULED in tiercall_meetings
	$rec_exists = false;
	$query = "SELECT contact_id FROM tiercall_meetings WHERE contact_id = '$cid' LIMIT 1";
	//RUN QUERY
	$rs = mysqli_query ($dbc, $query);
	if (mysqli_num_rows($rs) == 1) {
		$rec_exists = true;
	}
	mysqli_free_result($rs);
	
	
	//VALIDATE INPUT
	$errors = array();
	
	if ($m_date == ''){
		$errors[] = 'Select a date for meeting.';
	}
	if ($m_hour == ''){
		$errors[] = 'Select a meeting hour.';
	}
	if ($m_minute == ''){
		$errors[] = 'Select a meeting minute.';
	}
	$need_interval = false;
	//Check for interval only if send_mail OR send_text is Y
	if ($send_email == 'Y' || $send_text == 'Y'){
		if ($n_interval == ''){
			$errors[] = 'Select an interval.';
		} else {
			$need_interval = true;
		}
	}
	//**********************************************
	if (empty($errors)){
		
		// Meeting Date - Split it
		list($dt_mm,$dt_dd,$dt_yy) = explode('-',$m_date);
		
		$nad = $dt_yy."-".$dt_mm."-".$dt_dd;
		
		// Formatting for SQL datetime (if this is edited, it will NOT work.)
		$scheduled_meeting = $dt_yy."-".$dt_mm."-".$dt_dd." ".$m_hour.":".$m_minute.":00";
		
		if ($need_interval){
			$timespan = '';
			switch ($n_interval){
				case '15':
					$timespan = 'PT15M';
					break;
				case '30':
					$timespan = 'PT30M';
					break;
				case '45':
					$timespan = 'PT45M';
					break;
				case '60':
					$timespan = 'PT60M';
					break;
				default:
					$timespan = 'PT30M';
					break;
			}
			//determines notification_time
			$m_date = new DateTime($scheduled_meeting); 
			$n_int = new DateInterval($timespan); 
			$n_int->invert = 1; //Make it negative. 
			$m_date->add($n_int);
			$notification_date = $m_date->format('Y-m-d H:i:s');
		}
		
		$data_changed = true;
		//echo $scheduled_meeting.'<br />'.$notification_date;
		//Do Insert or Update
		if ($rec_exists){
			//update
			if ($data_changed) {
				$upd_sql = "UPDATE tiercall_meetings
							SET tier = '$tier', 
								contact_firstname = '$cfirst', 
								contact_lastname = '$clast', 
								contact_email =	'$cemail', 
								contact_phone =	'$cphone',
								contact_timezone = '$ctimezone',
								rep_id = '$repsid', 
								rep_vfgid =	'$repsvfgid', 
								rep_unique_id =	'$repsuniqueid',
								rep_gmail = '$repgmail',
								rep_gpass = '$repgpass',
								scheduled_meeting =	'$scheduled_meeting', ";
				if ($need_interval){
					$upd_sql .= "notification_interval = '$n_interval', 
								 notification_time = '$notification_date', ";
				} else {
					$upd_sql .= "notification_interval = NULL, 
								 notification_time = NULL, ";
				}
				$upd_sql .= "send_email = '$send_email',
								send_text = '$send_text',
								inviter = '$inviter',
								manager = '$manager',
								consultant = '$consultant'
							WHERE contact_id = '$cid' LIMIT 1";
				$r = mysqli_query($dbc, $upd_sql);    // or die(mysqli_error($dbc));
		
				if (mysqli_affected_rows($dbc) == 1){
					echo 'Meeting updated.';	
				} else {
					echo 'No data change.';
					//echo mysqli_error($dbc).'<br />'.$ins_sql;
				}
				
			} else {
				echo 'No data change.';
			}	//end data_changed
			
		} else {
			
			if ($need_interval){
				//insert w/notification fields
				$ins_sql = "INSERT INTO tiercall_meetings (tier, contact_id, contact_firstname, contact_lastname,
									contact_email, contact_phone, contact_timezone, rep_id, rep_vfgid, rep_unique_id, rep_gmail, rep_gpass,
									scheduled_meeting, notification_interval, notification_time, send_email, send_text, inviter, manager, consultant) 
								VALUES('$tier', '$cid', '$cfirst', '$clast', '$cemail', '$cphone', '$ctimezone', '$repsid', 
										'$repsvfgid', '$repsuniqueid', '$repgmail', '$repgpass', '$scheduled_meeting', 
										'$n_interval', '$notification_date', '$send_email', '$send_text', '$inviter', '$manager', '$consultant') ";
			
			} else {	//don't need interval stuff
			
				//insert without notification fields
				$ins_sql = "INSERT INTO tiercall_meetings (tier, contact_id, contact_firstname, contact_lastname,
									contact_email, contact_phone, contact_timezone, rep_id, rep_vfgid, rep_unique_id, rep_gmail, rep_gpass,
									scheduled_meeting, send_email, send_text, inviter, manager, consultant) 
								VALUES('$tier', '$cid', '$cfirst', '$clast', '$cemail', '$cphone', '$ctimezone', '$repsid', 
										'$repsvfgid', '$repsuniqueid', '$repgmail', '$repgpass', '$scheduled_meeting', 
										'$send_email', '$send_text', '$inviter', '$manager', '$consultant') ";
			
			}
			$r = mysqli_query($dbc, $ins_sql);    // or die(mysqli_error($dbc));
	
			if (mysqli_affected_rows($dbc) == 1){
				echo 'Meeting successfully added.<br />';	
			} else {
				echo 'Insert Problem.<br />';
				//echo mysqli_error($dbc).'<br />'.$ins_sql;
			}
		}
		
		// SET the TIER_STATUS, next_action_date to meeting date, and latest scheduled tiercall fields
		// This is set to 2A for any calls from the phone script modals
		$t_status = $tier.'A';
		$mdt = new DateTime($scheduled_meeting);
		$mdt_formatted = $mdt->format('m-d-Y h:i a');
		$latest_scheduled = 'Tier '.$tier.' on '.$mdt_formatted;
		
		$updatesql = "UPDATE contacts
					SET tier_status ='$t_status',
					    next_action_date = '$nad',
						nad_set_by = '$repsid', 
						last_scheduled_tiercall = '$latest_scheduled'
					WHERE contact_id = '$cid' LIMIT 1"; 
			//RUN UPDATE QUERY
		$rs= mysqli_query($dbc, $updatesql);
		if (mysqli_affected_rows($dbc) == 1){
			echo 'Contact Tier-Status updated to Tier '.$tier.', Call Scheduled.';
		} else {
			echo '<p>Tier Status no change.</p>';
			//echo '<p>'.mysqli_error($dbc).'</p>';
		}
		
		
		// ENTER NOTE THAT TIER STATUS WAS UPDATED - CALL SCHEDULED
		//
		date_default_timezone_set($_SESSION['rep_tz']);
		$rightnow = date("Y-m-d H:i:s");
		$note = 'Tier '.$tier.' meeting scheduled. Notifications: Send Email - '.$send_email.'  Send Text - '.$send_text;
		
		$note_sql = "INSERT INTO notes (rep_id, contact_id, rep_first, rep_last, note, note_date) 
						VALUES (?, ?, ?, ?, ?, ?)";

		//prepare statement
		$stmt = mysqli_prepare($dbc, $note_sql);
		//bind variables to statement
		mysqli_stmt_bind_param($stmt, 'ssssss', $repsid, $cid, $repfirst, $replast, $note, $rightnow);
		//execute query
		mysqli_stmt_execute($stmt);
		
		if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
			mysqli_stmt_close($stmt);
		} 
		
		
		
	} else {
		
		foreach($errors as $msg){
			echo $msg.'<br />';
		}
	}
	
	mysqli_close($dbc);
?>