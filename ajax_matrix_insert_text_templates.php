<?php
session_start();
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	include ('includes/config.inc.php');
	//DB Connection
	require_once (MYSQL);
	
	$repsid = $_SESSION['rep_id'];
	
	//Get POST vars
	$txt1 = $txt2 = $txt3 = $txt4 = $txt5 = '';
		
	$txt1 = $_POST['txt1'];
	$txt2 = $_POST['txt2'];
	$txt3 = $_POST['txt3'];
	$txt4 = $_POST['txt4'];
	$txt5 = $_POST['txt5'];
	
	$cid = $_POST['cid'];
	
	$rec_exists = false;
	//Does Record Exist in vfg_rep_text_templates
	$query = "SELECT rep_id FROM vfg_rep_text_templates WHERE rep_id = '$repsid'";
	$rs = mysqli_query ($dbc, $query);
	if ($rs){
		if (mysqli_num_rows($rs) == 1) {
			//id is unique
			$rec_exists = true;
		}
		mysqli_free_result($rs);
	}
	
	//Do Insert or Update
	if ($rec_exists){
		$updatesql = "UPDATE vfg_rep_text_templates 
					  SET text_template1='$txt1', 
							  text_template2='$txt2', 
							  text_template3='$txt3',
							  text_template4='$txt4',
							  text_template5='$txt5'
					  WHERE rep_id='$repsid' LIMIT 1"; 
		//RUN UPDATE QUERY
		$rs= mysqli_query($dbc, $updatesql);
		
		if (mysqli_affected_rows($dbc) == 1){
			echo 'Update successful.';
		} 
		
	} else {
		//insert
		$q = sprintf("INSERT INTO vfg_rep_text_templates (rep_id, text_template1, text_template2, text_template3, text_template4, text_template5)
						VALUES ('%s','%s','%s','%s','%s','%s')", $repsid, $txt1, $txt2, $txt3, $txt4, $txt5);
		$r = mysqli_query($dbc,$q);
		if (mysqli_affected_rows($dbc) == 1){
			echo 'Templates Added.';
		} else {
			echo 'Insert Error: '.mysqli_error($dbc);
		}
	}
	
	mysqli_close($dbc);
?>	
			