<?php
session_start();
	include ('includes/config.inc.php');
	$repsid = $_SESSION['rep_id'];
	
	//$_POST VARS
	$evt_title = $_POST['event_title'];
	//$evt_desc = $_POST['event_desc'];
	$evt_start_hh = $_POST['event_start_hh'];
	$evt_start_mm = $_POST['event_start_mm'];
	$evt_end_hh = $_POST['event_end_hh'];
	$evt_end_mm = $_POST['event_end_mm'];
	$todays_date = $_POST['todays_date'];
	//$m = $_POST['month'];
	//$d = $_POST['day'];
	//$y = $_POST['year'];
	$url = '';
	// Add our new events
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_start = $todays_date." ".$evt_start_hh.":".$evt_start_mm.":00";
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_end = $todays_date." ".$evt_end_hh.":".$evt_end_mm.":00";

	// connection to the database
	try {
		$bdd = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}

	// insert the records
	$sql = "INSERT INTO fc_events (rep_id, title, start, end, url) VALUES (:rep_id, :title, :start, :end, :url)";
	$q = $bdd->prepare($sql);
	$status = $q->execute(array(':rep_id'=>$repsid, ':title'=>$evt_title, ':start'=>$event_start, ':end'=>$event_end,  ':url'=>$url));
	
	//execute returns true or false
	if($status){
		echo 'Event Added.';
	} else {
		echo 'Add Failed.';
	}
	
	
?>