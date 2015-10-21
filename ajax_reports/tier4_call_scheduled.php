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
	// TIER 3 CALLS SCHEDULED:
	//******************************************
	$q = "SELECT a.firstname, a.lastname, a.email, a.phone, a.assigned_consultant, a.next_action_date,
				 b.lastname as conlast, b.firstname as confirst, b.purchased_policy as 'cpolicy',
				 c.lastname as invfirst, c.firstname as invlast, c.purchased_policy as 'ipolicy'
		  FROM contacts a 
		  INNER JOIN reps b ON a.assigned_consultant = b.vfgrepid
		  INNER JOIN reps c ON a.rep_id = c.rep_id
		  WHERE a.tier_status = '4A'
		  ORDER BY a.next_action_date DESC";
	$r = mysqli_query($dbc, $q);

	if ($r) {

		echo '<table id="reports_table">
						<tr>
							<th colspan=6>Tier 4 - Calls Scheduled</th>
						</tr>
						<tr>
							<th scope="col">Prospect Name</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Inviter</th>
							<th scope="col">Assigned Consultant</th>
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
			if ($row['ipolicy'] == 'Y'){
				echo '<td>'.$row['invlast'].', '.$row['invfirst'].' *</td>';
			} else {
				echo '<td>'.$row['invlast'].', '.$row['invfirst'].'</td>';
			}
			if ($row['cpolicy'] == 'Y'){
				echo '<td>'.$row['conlast'].', '.$row['confirst'].' ('.$row['assigned_consultant'].') *</td>';
			} else {
				echo '<td>'.$row['conlast'].', '.$row['confirst'].' ('.$row['assigned_consultant'].')</td>';
			}
			echo '<td>'.$row['next_action_date'].'</td>';
			echo '</tr>';
		}
	
		echo '</table>';
		
	}	// close if ($r)

	mysqli_close($dbc);

?>