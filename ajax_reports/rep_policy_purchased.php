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

	
	//*************************************
	// TOTAL REPS
	//*************************************
	$q = "SELECT count(rep_id) FROM reps WHERE purchased_policy = 'Y' ";
	$r = mysqli_query($dbc, $q);

	$row = @mysqli_fetch_array($r, MYSQLI_NUM);
	$records = $row[0];
	mysqli_free_result($r);
	echo 'Total Reps:  '.$records.'<br /><br />'."\n";

	//*************************************
	// TOTAL CONTACTS
	//*************************************
	$q = "SELECT count(contact_id) FROM contacts";
	$r = mysqli_query($dbc, $q);

	$row = @mysqli_fetch_array($r, MYSQLI_NUM);
	$contacts = $row[0];
	mysqli_free_result($r);
	//echo 'Total Contacts:  '.$contacts.'<br /><br />'."\n";

	//******************************************
	// REP INFO:
	//******************************************
	$q = "SELECT rep_id, vfgrepid, 
				firstname, lastname, 
				gmail_acct, phone, 
				rep_timezone, DATE_FORMAT(signup_date,'%m-%d-%Y') as signup_dt, 
				purchased_policy
		  FROM reps
		  WHERE purchased_policy = 'Y' 
		  ORDER BY signup_date DESC";
	$r = mysqli_query($dbc, $q);

	if ($r) {

		echo '<table id="reports_table">
						<tr>
							<th colspan=7>Reps - Purchased Policy</th>
						</tr>
						<tr>
							<th scope="col">VFG ID</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Timezone</th>
							<th scope="col">Signup Date</th>
						</tr>';
						
		// Ran OK
		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			echo '<tr>';
			//echo '<td>'.$row['rep_id'].'</td>';
			echo '<td>'.$row['vfgrepid'].'</td>';
			echo '<td>'.$row['lastname'].' *</td>';
			echo '<td>'.$row['firstname'].'</td>';
			echo '<td>'.$row['gmail_acct'].'</td>';
			echo '<td>'.$row['phone'].'</td>';
			echo '<td>'.getStandardTZname($row['rep_timezone']).'</td>';
			echo '<td>'.$row['signup_dt'].'</td>';
			echo '</tr>';
		}
	
		echo '</table>';
		
	}	// close if ($r)

	mysqli_close($dbc);

?>