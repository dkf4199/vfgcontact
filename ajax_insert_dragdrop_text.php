<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repid = $_SESSION['rep_id'];
	$contactid = $_POST['cid'];
	$txttemplate = $_POST['texttemplate'];
	
	$textnote = 'Sent text template '.$txttemplate;
	
	date_default_timezone_set($_SESSION['rep_tz']);
	$rightnow = date("Y-m-d H:i:s");
	
	$insertsql = "INSERT INTO communication_matrix (rep_id, contact_id, comm_type, comm_note, comm_date) 
						VALUES (?, ?, 'TM', ?, ?)";

	//prepare statement
	$stmt = mysqli_prepare($dbc, $insertsql);
	//bind variables to statement
	mysqli_stmt_bind_param($stmt, 'ssss', $repid, $contactid, $textnote, $rightnow);
	//execute query
	mysqli_stmt_execute($stmt);
	
	if (mysqli_stmt_affected_rows($stmt) == 1) {	//note data insert successful
		
		mysqli_stmt_close($stmt);
		
	}	
	
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>