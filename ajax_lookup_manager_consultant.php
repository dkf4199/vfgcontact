<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$rid = $_POST['repid'];
	$firstname = $lastname = '';
	
	$q = "SELECT firstname, lastname
		  FROM reps
		  WHERE vfgrepid = '$rid' LIMIT 1";
	$rs = @mysqli_query ($dbc, $q); // Run the query.
	if ($rs){
		if (mysqli_num_rows($rs) == 1) {
			while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
				$firstname = $row['firstname'];
				$lastname = $row['lastname'];
			}
		}
	}
	echo $firstname.' '.$lastname;
	
	mysqli_close($dbc); // Close the database connection.		
?>