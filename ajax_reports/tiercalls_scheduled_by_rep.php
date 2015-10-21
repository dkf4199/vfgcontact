<?php
	include ('../includes/config.inc.php');

	function getStandardTZname($tzone){
		$timeZones = array(
		  'America/New_York'=>'Eastern', 
		  'America/Chicago'=>'Central', 
		  'America/Boise'=>'Mountain', 
		  'America/Phoenix'=>'Arizona Mountain', 
		  'America/Los_Angeles'=>'Pacific', 
		  'America/Juneau'=>'Alaska', 
		  'Pacific/Honolulu'=>'Hawaii' 
		);

		$standard_tz = '';
		foreach($timeZones as $id=>$name ){
			if ($tzone == $id){
				$standard_tz = $name;
			}
		}
		return $standard_tz;
	}
	
	
	//DB Connection
	require_once (MYSQL2);

	//Get listing of all Assigned Managers for dropdown filter
	//
	$q = "SELECT DISTINCT a.assigned_manager as 'agent', b.lastname, b.firstname
			FROM contacts a INNER JOIN reps b on a.assigned_manager = b.vfgrepid
			WHERE a.tier_status = '2A' 
		  UNION
		  SELECT DISTINCT a.assigned_consultant as 'agent', b.lastname, b.firstname
		    FROM contacts a INNER JOIN reps b on a.assigned_consultant = b.vfgrepid
			WHERE a.tier_status IN('3A','4A') ";
	$rs = mysqli_query($dbc, $q);
	
	if (mysqli_num_rows($rs) > 0){
	
		echo '<p align="center">
			    <label>Select Agent:</label>
				<select name="assigned_agent_filter" id="assigned_agent_filter" onChange="return filterReport(\'TierCallsScheduledByAgent\');">
					<option value="">Select Agent</option>';

		while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
			echo '<option value="'.$row['agent'].'">'.$row['lastname'].', '.$row['firstname'].'</option>';
		}
		echo '</select></p>';
		mysqli_free_result($rs);
		
	}
	

	mysqli_close($dbc);

?>