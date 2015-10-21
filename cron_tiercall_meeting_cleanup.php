<?php
	//cron job - deletes tiercall meetings that have
	//           past their scheduled date.
	//
	//******************************************************************************
	include ('./includes/config.inc.php');

	//DB Connection
	require_once (MYSQL);

	$thisday = date("m-d-Y");	//Todays date
	
	// Delete meetings from yesterday
	$delquery = "DELETE FROM tiercall_meetings
				 WHERE scheduled_meeting < $thisday";
	$d_result = mysqli_query ($dbc, $delquery);
	if (mysqli_affected_rows($dbc) == 1){
		$msgs[] = 'Record processed and deleted.<br />';
	} else {
		$msgs[] = 'Problem processing record.';
		//echo '<p>'.mysqli_error($dbc).'</p>';
	}
	
	mysqli_close($dbc);
	
?>