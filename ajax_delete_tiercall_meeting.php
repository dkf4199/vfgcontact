<?php
session_start();
	//DELETE THIS MEETING
	
	//Set the timezone to the rep's timezone
	date_default_timezone_set($_SESSION['rep_tz']);
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//$_POST VARS
	$cid = $_POST['cid'];
		
	$del_sql = "DELETE FROM tiercall_meetings 
					 WHERE contact_id = '$cid' LIMIT 1";
			
	$r = mysqli_query($dbc, $del_sql);    // or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) == 1){
		echo 'Meeting deleted.';	
	} else {
		echo 'Delete problem.';
	}
	
	mysqli_close($dbc);
?>