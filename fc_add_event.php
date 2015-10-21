<?php
session_start();
	include ('includes/config.inc.php');
	$repsid = $_SESSION['rep_id'];
	
	// Values received via ajax
	$title = $_POST['title'];
	$start = $_POST['start'];
	$end = $_POST['end'];
	$url = $_POST['url'];
	// connection to the database
	try {
		$bdd = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}

	// insert the records
	$sql = "INSERT INTO events (rep_id, title, start, end, url) VALUES (:rep_id, :title, :start, :end, :url)";
	$q = $bdd->prepare($sql);
	$q->execute(array(':rep_id'=>$repsid, ':title'=>$title, ':start'=>$start, ':end'=>$end,  ':url'=>$url));
?>