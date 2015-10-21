<?php
session_start();
	include ('includes/config.inc.php');
	$repsid = $_SESSION['rep_id'];
	//$_POST VARS
	$evt_id = $_POST['event_id'];
	
	// connection to the database
	try {
		$bdd = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}

	// update the records
	$sql = "DELETE FROM fc_events WHERE id=?";
	$q = $bdd->prepare($sql);
	
	$status = $q->execute(array($evt_id));
	
	//execute return true or false
	if($status){
		echo 'Event Deleted.';
	} else {
		echo 'Delete Failed.';
	}
	
	
?>