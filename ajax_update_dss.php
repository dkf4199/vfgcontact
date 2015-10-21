<?php
session_start();
	$repid = $_SESSION['rep_id'];
	
	//$_POSTS
	$stepnum = $_POST['stepnum'];
	$yesno = $_POST['yesno'];
	
	$field = 'step'.$stepnum;
	include ('includes/config.inc.php');
	//DB Connection
	require_once (MYSQL);
	
	$sql = "UPDATE daily_success_steps
				SET $field = '$yesno'
				WHERE rep_id = '$repid' LIMIT 1";
	$rs= mysqli_query($dbc, $sql);	
	if (mysqli_affected_rows($dbc) == 1){
		echo 'Update successful.<br />';
	} else {
		echo 'No Data Change.';
		//echo '<p>'.mysqli_error($dbc).'</p>';
	}	// close mysqli_affected_rows
	mysqli_close($dbc);
?>