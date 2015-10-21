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
	
	$event_count = 0;
	// First, find out how many events exist for this parent event
	if ($fc_parent_id != 0){
		$count_sql = "SELECT COUNT(*) as total_events FROM fc_events WHERE parent_id=$fc_parent_id";
		$r = mysqli_query($dbc, $count_sql);
		if (mysqli_num_rows($r) == 1){
			while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$event_count = $ev['total_events'];
			}
		}
	}
	
	$delEvent_sql = "DELETE FROM fc_events 
					 WHERE rep_id = '$repsid' 
					 AND id = $fc_id LIMIT 1";
			
	$r = mysqli_query($dbc, $delEvent_sql);    // or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) == 1){
		echo 'Event deleted.';	
	} else {
		echo 'Delete problem.';
	}
	
	if ($fc_parent_id != 0 && $event_count == 1){	//only, or last event left for the parent event
		$delParentEvent_sql = "DELETE FROM fc_events_parent 
					 WHERE rep_id = '$repsid' 
					 AND parent_id = $fc_parent_id LIMIT 1";
			
		$r = mysqli_query($dbc, $delParentEvent_sql);    // or die(mysqli_error($dbc));
		
		if (mysqli_affected_rows($dbc) == 1){
			echo 'Parent Event deleted.';	
		} else {
			echo 'Parent Event Delete problem.';
		}
	}
	
	mysqli_close($dbc);
	
?>