<?php
session_start();
	$repsid = $_SESSION['rep_id'];
	
	//DELETE THIS EVENT
	
	//Set the timezone to the rep's timezone
	date_default_timezone_set($_SESSION['rep_tz']);
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//$_POST VARS
	$cal_id = $_POST['calid'];
	$m = $_POST['month'];
	$d = $_POST['day'];
	$y = $_POST['year'];
	// Add our new events
	
	$delEvent_sql = "DELETE FROM calendar_events 
					 WHERE rep_id = '$repsid' 
					 AND cal_id = $cal_id LIMIT 1";
			
	$r = mysqli_query($dbc, $delEvent_sql);    // or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) == 1){
		echo 'Event deleted.';	
	} else {
		echo 'Delete problem.';
	}
	
	mysqli_close($dbc);
	
?>