<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$insert_success = '';
	
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repid = $_SESSION['rep_id'];
	$repfirst = $_SESSION['rep_firstname'];
	$replast = $_SESSION['rep_lastname'];
	$contactid = $_POST['cid'];
	date_default_timezone_set($_SESSION['rep_tz']);
	$rightnow = date("Y-m-d H:i:s");
	
	$note = mysqli_real_escape_string($dbc, trim($_POST['note']));
	
	$insertsql = "INSERT INTO notes (rep_id, contact_id, rep_first, rep_last, note, note_date) 
						VALUES (?, ?, ?, ?, ?, ?)";

	//prepare statement
	$stmt = mysqli_prepare($dbc, $insertsql);
	//bind variables to statement
	mysqli_stmt_bind_param($stmt, 'ssssss', $repid, $contactid, $repfirst, $replast, $note, $rightnow);
	//execute query
	mysqli_stmt_execute($stmt);
	
	if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
		mysqli_stmt_close($stmt);
		echo "Note added.";
	} else {
		echo "Note failure.";
	}
	
	mysqli_close($dbc); // Close the database connection.
?>