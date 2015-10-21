<?php
session_start();

	include ('includes/config.inc.php');
		
	// List of events
	$json = array();

	// Query	
	// 08/10/2015 dkf add company events to query
	$q = "SELECT * FROM fc_admin_events";
	
	// connection to the database
	try {
		$bdd = new PDO(PDO_HOSTSTRING, PDO_USER, PDO_PASS);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}
	// Execute the query
	$rs = $bdd->query($q) or die(print_r($bdd->errorInfo()));

	// sending the encoded result to success page
	echo json_encode($rs->fetchAll(PDO::FETCH_ASSOC),JSON_NUMERIC_CHECK);

?>