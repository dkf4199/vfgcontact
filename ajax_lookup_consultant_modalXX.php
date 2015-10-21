<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	
	$curr_state = $_GET['state'];
	$state_text = $_GET['statetext'];
	$fname = $_GET['c_first'];
	$lname = $_GET['c_last'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$lic_consultants = array();
	//Lookup Consultant's in contact's state
	
	$lic_field = 'lic_'.strtolower($curr_state);
	
	$q = "SELECT a.rep_id, b.vfgrepid, b.firstname, b.lastname, b.replevel_consultant
		  FROM rep_license a INNER JOIN reps b ON a.rep_id = b.rep_id
		  WHERE $lic_field = 'Y' AND b.replevel_consultant = 'Y'";
	
	$r = mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) > 0){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$lic_consultants[] = $row['vfgrepid'].':'.$row['firstname'].':'.$row['lastname'];
		}
		mysqli_free_result($r);
	}
	
	$num_reps = sizeof($lic_consultants);
	
	$displayform = '<div style="display:none">
		<div class="modal-content">
			
			<h3 class="modal-title">Consultants for '.$fname.' '.$lname.' in '.$state_text.'.  Found '.$num_reps.' licensed consultants.</h3>
			<p align="center">
				<select name="selected_consultant" id="selected_consultant">
					<option value="">Select</option>';
			
	foreach ($lic_consultants as $agent){
		// 3 pieces: vfgid(0), firstname(1), lastname(2)
		$thisagent = explode(":", $agent);
		
		$displayform .= '<option value="'.$thisagent[0].'">'.$thisagent[1].' '.$thisagent[2].'</option>';
	}
	
	$displayform .= '</select>&nbsp;&nbsp;&nbsp;
			<a href="" class="linkbutton" id="set_consultant_button">Select Consultant</a><br /><br />
			<div id="set_consultant_response" style="text-align:center;">Choose A Rep From List</div>
			</p>
		 </div> <!-- close modal-content -->
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>