<?php	
	include ('includes/config.inc.php');
	//DB Connection
	require_once (MYSQL);
	
	$sql = "UPDATE daily_success_steps
				SET step1 = 'N',
				    step2 = 'N',
					step3 = 'N',
					step4 = 'N',
					step5 = 'N',
					step6 = 'N',
					step7 = 'N' ";
	$rs = mysqli_query($dbc, $sql);	
	mysqli_close($dbc);
?>