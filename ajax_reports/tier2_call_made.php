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

	
	//******************************************
	// TIER 2 CALLS SCHEDULED:
	//******************************************
	/*$q = "SELECT a.firstname, a.lastname, a.email, a.phone, a.assigned_manager, a.next_action_date,
				 b.lastname as manlast, b.firstname as manfirst, b.purchased_policy
		  FROM contacts a INNER JOIN reps b ON a.assigned_manager = b.vfgrepid
		  WHERE a.tier_status = '2B'
		  ORDER BY a.next_action_date DESC";
	*/
	$q = "SELECT a.rep_id, a.firstname, a.lastname, a.email, a.phone, a.assigned_manager, a.next_action_date,
				 b.lastname as manlast, b.firstname as manfirst, b.purchased_policy,
				 c.lastname as invfirst, c.firstname as invlast
		  FROM contacts a 
		  INNER JOIN reps b ON a.assigned_manager = b.vfgrepid
		  INNER JOIN reps c ON a.rep_id = c.rep_id
		  WHERE a.tier_status = '2B'
		  ORDER BY a.next_action_date DESC;";
	$r = mysqli_query($dbc, $q);

	if ($r) {

		echo '<table id="reports_table">
						<tr>
							<th colspan=6>Tier 2 - Calls Made</th>
						</tr>
						<tr>
							<th scope="col">Prospect Name</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Inviter</th>
							<th scope="col">Assigned Manager</th>
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
			if ($row['purchased_policy'] == 'Y'){
				echo '<td>'.$row['manlast'].', '.$row['manfirst'].' ('.$row['assigned_manager'].') *</td>';
			} else {
				echo '<td>'.$row['manlast'].', '.$row['manfirst'].' ('.$row['assigned_manager'].')</td>';
			}
			
			echo '<td>'.$row['next_action_date'].'</td>';
			echo '</tr>';
		}
	
		echo '</table>';
		
	}	// close if ($r)

	mysqli_close($dbc);

?>