<?php
session_start();
	include ('includes/config.inc.php');
	//$_POST VARS
	$evt_id = $_POST['event_id'];
	$evt_title = $_POST['event_title'];
	//$evt_desc = $_POST['event_desc'];
	$evt_start_hh = $_POST['event_start_hh'];
	$evt_start_mm = $_POST['event_start_mm'];
	$evt_end_hh = $_POST['event_end_hh'];
	$evt_end_mm = $_POST['event_end_mm'];
	$start_date = $_POST['event_start_dt'];
	$end_date = $_POST['event_end_dt'];
	//$m = $_POST['month'];
	//$d = $_POST['day'];
	//$y = $_POST['year'];
	$url = '';
	// Add our new events
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_start = $start_date." ".$evt_start_hh.":".$evt_start_mm.":00";
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_end = $end_date." ".$evt_end_hh.":".$evt_end_mm.":00";

	// connection to the database
	try {
		$bdd = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}

	// update the records
	$sql = "UPDATE events SET title=?, start=?, end=? WHERE id=?";
	$q = $bdd->prepare($sql);
	$status = $q->execute(array($evt_title,$event_start,$event_end,$evt_id));
	
	//execute returns true or false
	if($status){
		echo 'Event updated.';
	} else {
		echo 'Update failed, or you didn\'t change any data.';
	}
	
	
?>