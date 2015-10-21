<?php
session_start();

	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	
	//DB Connection
	require_once (MYSQL);
	
	// POST VARS COMING IN
	// rep vfgid, date
	$rep_vfgid = $_POST['repvfgid'];
	
	$return_tz = '';
	
	//Get the reps vfgid
	$q = "SELECT rep_id, rep_timezone FROM reps WHERE vfgrepid = '$rep_vfgid' LIMIT 1";
	$r = mysqli_query($dbc, $q);

	if ($r) { 
		// Ran OK
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			//$_SESSION['consultants_gmail_acct'] = $row['gmail_acct'];
			//$_SESSION['consultants_gmail_pass'] = $row['gmail_pass'];
			$_SESSION['consultants_repid'] = $row['rep_id'];
			$_SESSION['consultants_tz'] = $row['rep_timezone'];
			if ($row['rep_timezone'] != ''){
				foreach($americaTimeZones as $id=>$name){
					if ($row['rep_timezone'] == $id){
						$return_tz = $name;
					}
				}
			}
		}
		mysqli_free_result($r);
	}
	mysqli_close($dbc);
	
	echo $return_tz;
?>