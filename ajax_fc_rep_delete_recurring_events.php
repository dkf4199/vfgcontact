<?php
session_start();
	$repsid = $_SESSION['rep_id'];
	
	//DELETE SINGLE EVENT
	
	//Set the timezone to the rep's timezone
	date_default_timezone_set($_SESSION['rep_tz']);
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//$_POST VARS
	$fc_id = $_POST['fcid'];
	$fc_parent_id = (int) $_POST['fcparent'];		//this will be 0 for those records with no parent rec
	$m = $_POST['month'];
	$d = $_POST['day'];
	$y = $_POST['year'];
	
	// Delete recurring events from fc_events
	$delEvent_sql = "DELETE FROM fc_events 
					 WHERE rep_id = '$repsid' 
					 AND parent_id = $fc_parent_id ";
			
	$r = mysqli_query($dbc, $delEvent_sql);    // or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) >= 1){
		echo 'Recurring Events deleted.';	
	} else {
		echo 'Recurring Events Delete problem.';
		//echo mysqli_error($dbc);
	}
	
	if ($fc_parent_id != 0){
		$delParentEvent_sql = "DELETE FROM fc_events_parent 
					 WHERE rep_id = '$repsid' 
					 AND parent_id = $fc_parent_id LIMIT 1";
			
		$r = mysqli_query($dbc, $delParentEvent_sql);    // or die(mysqli_error($dbc));
		
		if (mysqli_affected_rows($dbc) == 1){
			echo '<br />Parent Event deleted.';	
		} else {
			echo '<br />Parent Event Delete problem.';
		}
	}
	
	mysqli_close($dbc);
	
?>