<?php
session_start();
	$repsid = $_SESSION['rep_id'];							//agent adding event
	$managers_id = $_SESSION['assigned_manager_repid'];		//managers repid
	
	//Set the timezone to the rep's timezone
	date_default_timezone_set($_SESSION['rep_tz']);
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//$_POST VARS
	$evt_title = $_POST['event_title'];
	$evt_desc = $_POST['event_desc'];
	$evt_start_hh = $_POST['event_start_hh'];
	$evt_start_mm = $_POST['event_start_mm'];
	$evt_end_hh = $_POST['event_end_hh'];
	$evt_end_mm = $_POST['event_end_mm'];
	$m = $_POST['month'];
	$d = $_POST['day'];
	$y = $_POST['year'];
	$cid = $_POST['cid'];	//needed for url link to contact's record from managers calendar
	
	
	// For fc_events_parent table:
	$todays_date = $y.'-'.$m.'-'.$d;
	$start_time = $evt_start_hh.":".$evt_start_mm.":00";
	$end_time = $evt_end_hh.":".$evt_end_mm.":00";
	$weekday = date('N', strtotime($todays_date));
	
	// For notes table
	$repfirst = $_SESSION['rep_firstname'];
	$replast = $_SESSION['rep_lastname'];
	
	// URL link to rep_edit_contact for this contact
	// role is either (manager, consult, addlrep)
	$url = 'rep_edit_contact.php?s=0&cid='.$cid.'&role=manager&fromsrc=fccal';
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_start_fulldatetime = $y."-".$m."-".$d." ".$evt_start_hh.":".$evt_start_mm.":00";
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_end_fulldatetime = $y."-".$m."-".$d." ".$evt_end_hh.":".$evt_end_mm.":00";
	$rightnow = date("Y-m-d H:i:s");
	
	// connection to the database
	try {
		$dbh = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}
	$repeats = 0;
	$repeat_freq = 0;
	try{
		$dbh->beginTransaction();
		$stmt = $dbh->prepare("INSERT INTO fc_events_parent 
			(rep_id,title,start_date, start_time, end_time, weekday, repeats, repeat_freq)
			VALUES (:rep_id, :title, :start_date, :start_time, :end_time, :weekday, :repeats, :repeat_freq)");
		
		$stmt->bindParam(':rep_id', $managers_id );
		$stmt->bindParam(':title', $evt_title );
		$stmt->bindParam(':start_date', $todays_date);
		$stmt->bindParam(':start_time', $start_time);
		$stmt->bindParam(':end_time', $end_time);
		$stmt->bindParam(':weekday', $weekday);
		$stmt->bindParam(':repeats', $repeats);
		$stmt->bindParam(':repeat_freq', $repeat_freq);
		$stmt->execute();
		$last_id = $dbh->lastInsertId();

		// CHILD EVENT
		$stmt = $dbh->prepare("INSERT INTO fc_events 
			(parent_id, rep_id, title, description, start, end, url, appt_set_by, appt_set_date)
			VALUES (:parent_id, :rep_id, :title, :description, :start, :end, :url, :set_by, :set_date)");

		$stmt->bindParam(':parent_id', $last_id);
		$stmt->bindParam(':rep_id', $managers_id);
		$stmt->bindParam(':title', $evt_title );
		$stmt->bindParam(':description', $evt_desc);
		$stmt->bindParam(':start', $event_start_fulldatetime);
		$stmt->bindParam(':end', $event_end_fulldatetime);
		$stmt->bindParam(':url', $url);
		$stmt->bindParam(':set_by', $repsid);
		$stmt->bindParam(':set_date', $rightnow);
		
		$stmt->execute();

		$dbh->commit();
		echo 'Event Added.';
	}
	catch(Exception $e){
		$dbh->rollback();
		echo 'Add Failed.';
	}
	
	// Lastly, ADD NOTE to contact's record
	// ADD NOTE TO CONTACTS RECORD
	$note = 'Assigned manager meeting scheduled.  Set by '.$repfirst.' '.$replast;
	$stmt = $dbh->prepare("INSERT INTO notes
		(rep_id, contact_id, rep_first, rep_last, note, note_date)
		VALUES (:rep_id, :contact_id, :rep_first, :rep_last, :note, :note_date)");
	$stmt->bindParam(':rep_id', $repsid);
	$stmt->bindParam(':contact_id', $cid);
	$stmt->bindParam(':rep_first', $repfirst );
	$stmt->bindParam(':rep_last', $replast);
	$stmt->bindParam(':note', $note);
	$stmt->bindParam(':note_date', $rightnow);
	$stmt->execute();
	
?>