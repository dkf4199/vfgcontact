<?php
session_start();

	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	$repsid = $_SESSION['rep_id'];
	//DB Connection
	require_once (MYSQL);
	
	$options = "<option value=\"\">Select</option>\n";
	//Get distinct templates from vfg_globalemail_settings table
	$q = "SELECT tt_id, text_template_name
		  FROM vfg_rep_text_templates
		  WHERE rep_id = '$repsid'";
	$r = mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) > 0){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$options .= "<option value=\"".$row['tt_id']."\">".$row['text_template_name']."</option>\n";
		}
		mysqli_free_result($r);
	}
	echo $options;
?>