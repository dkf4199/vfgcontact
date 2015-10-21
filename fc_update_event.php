<?php
session_start();
	include ('includes/config.inc.php');
	$repsid = $_SESSION['rep_id'];
	
	/* Values received via ajax */
	$id = $_POST['id'];
	$title = $_POST['title'];
	$start = $_POST['start'];
	$end = $_POST['end'];

	// connection to the database
	try {
		$bdd = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}
	// update the records
	$sql = "UPDATE fc_events SET title=?, start=?, end=? WHERE id=?";
	$q = $bdd->prepare($sql);
	$q->execute(array($title,$start,$end,$id));
?>