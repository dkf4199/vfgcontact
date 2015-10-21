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
	
	// FILTER VALUE
	$assigned_agent = $_GET['assigned_agent'];
	
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
	
	// Get the data - ALL TIERS, BY SELECTED AGENT
	$q = "SELECT 2 as 'tierrank', a.rep_id, a.firstname, a.lastname, a.email, a.phone, a.assigned_manager as 'agentid', a.next_action_date,
				 b.lastname as agentlast, b.firstname as agentfirst, b.purchased_policy,
				 c.lastname as invfirst, c.firstname as invlast
		  FROM contacts a 
		  INNER JOIN reps b ON a.assigned_manager = b.vfgrepid
		  INNER JOIN reps c ON a.rep_id = c.rep_id
		  WHERE a.tier_status = '2A' 
		  AND a.assigned_manager = '$assigned_agent' 
		  UNION
		  SELECT 3 as 'tierrank', a.rep_id, a.firstname, a.lastname, a.email, a.phone, a.assigned_consultant as 'agentid', a.next_action_date,
				 b.lastname as agentlast, b.firstname as agentfirst, b.purchased_policy,
				 c.lastname as invfirst, c.firstname as invlast
		  FROM contacts a 
		  INNER JOIN reps b ON a.assigned_consultant = b.vfgrepid
		  INNER JOIN reps c ON a.rep_id = c.rep_id
		  WHERE a.tier_status = '3A' 
          AND a.assigned_consultant = '$assigned_agent'
		  UNION
		  SELECT 4 as 'tierrank', a.rep_id, a.firstname, a.lastname, a.email, a.phone, a.assigned_consultant as 'agentid', a.next_action_date,
				 b.lastname as agentlast, b.firstname as agentfirst, b.purchased_policy,
				 c.lastname as invfirst, c.firstname as invlast
		  FROM contacts a 
		  INNER JOIN reps b ON a.assigned_consultant = b.vfgrepid
		  INNER JOIN reps c ON a.rep_id = c.rep_id
		  WHERE a.tier_status = '4A' 
          AND a.assigned_consultant = '$assigned_agent'
		  ORDER BY tierrank ";

	$r = mysqli_query($dbc, $q);

	if ($r) {

		echo '<table id="reports_table">
						<tr>
							<th colspan=6>Tier Calls Scheduled</th>
						</tr>
						<tr>
							<th scope="col">Prospect Name</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Inviter</th>
							<th scope="col">Tier Call</th>
							<th scope="col">Next Action Date</th>
						</tr>';
						
		// Ran OK
		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			echo '<tr>';
			//echo '<td>'.$row['rep_id'].'</td>';
			echo '<td>'.$row['lastname'].', '.$row['firstname'].'</td>';
			echo '<td>'.$row['email'].'</td>';
			echo '<td>'.$row['phone'].'</td>';
			echo '<td>'.$row['invlast'].', '.$row['invfirst'].'</td>';
			echo '<td>'.$row['tierrank'].'</td>';
			/*
			if ($row['purchased_policy'] == 'Y'){
				echo '<td>'.$row['agentlast'].', '.$row['agentfirst'].' ('.$row['agentid'].') *</td>';
			} else {
				echo '<td>'.$row['agentlast'].', '.$row['agentfirst'].' ('.$row['agentid'].')</td>';
			}
			*/
			echo '<td>'.$row['next_action_date'].'</td>';
			echo '</tr>';
		}
	
		echo '</table>';
		
	}	// close if ($r)
	mysqli_close($dbc);

?>