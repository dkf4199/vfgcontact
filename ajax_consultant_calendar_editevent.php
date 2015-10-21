<?php
session_start();
	$repsid = $_SESSION['rep_id'];
	
	//Set the timezone to the rep's timezone
	date_default_timezone_set($_SESSION['rep_tz']);
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//$_POST VARS
	$cal_id = $_POST['calid'];
	$evt_title = $_POST['event_title'];
	$evt_desc = $_POST['event_desc'];
	$evt_start_hh = $_POST['event_start_hh'];
	$evt_start_mm = $_POST['event_start_mm'];
	$evt_end_hh = $_POST['event_end_hh'];
	$evt_end_mm = $_POST['event_end_mm'];
	$m = $_POST['month'];
	$d = $_POST['day'];
	$y = $_POST['year'];
	// Add our new events
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_start = $y."-".$m."-".$d." ".$evt_start_hh.":".$evt_start_mm.":00";
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_end = $y."-".$m."-".$d." ".$evt_end_hh.":".$evt_end_mm.":00";

	$updEvent_sql = "UPDATE calendar_events 
					 SET event_title = '$evt_title',
					    event_shortdesc = '$evt_desc',
						event_start = '$event_start',
						event_end = '$event_end'
					 WHERE rep_id = '$repsid' 
					 AND cal_id = $cal_id LIMIT 1";
			
	$r = mysqli_query($dbc, $updEvent_sql);    // or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) == 1){
		echo 'Event updated.';	
	} else {
		echo 'No data change.';
	}
	
	mysqli_close($dbc);
	
?>