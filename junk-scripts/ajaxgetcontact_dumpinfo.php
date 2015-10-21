<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	
	$currcid = $_GET['currentcontactid'];
		
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	// Make the query:
	$q = "SELECT a.firstname, a.lastname, a.email, a.phone,
				a.city, a.state, a.timezone, a.tier_status, a.notes, a.update_date, a.team_member, b.status_desc
		  FROM contacts a INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
		  WHERE a.contact_id = '$currcid' LIMIT 1";		
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.

		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
			//split the tier_status
			$tier = substr($row['tier_status'],0,1);
			$tierstep = substr($row['tier_status'],1,1);
			
			//Format the update_date
			if ($row['update_date'] != ''){
				$updatedt = strtotime( $row['update_date'] );
				$formatted_updatedt = date( 'm-d-Y h:i:s a', $updatedt );
			} else {
				$formatted_updatedt = '';
			}
			//display timezone data
			//rep time:
			date_default_timezone_set($_SESSION['rep_tz']);
			$reptime = date("h:i:s a");
			//contact time:
			date_default_timezone_set($row['timezone']);
			$contacttime = date("h:i:s a");
			echo '<p align="center">
					<span class="editheader">Dump Contact: '.$row['firstname'].' '.$row['lastname'].'</span></p>';
			/*echo '<div id="timedivwrap">';
			echo '<div id="reptime">Rep\'s Local Time:  '.$reptime.'</div>';
			echo '<div id="contacttime">Contact\'s Local Time: '.$contacttime.'</div>';
			echo '<div class="cleardiv"></div>';
			echo '</div>';*/
			$displayform = 
					'<div class="dumpdiv">
						
						<form name="dumpcontact_form" id="dumpcontact_form" onSubmit="return dumpContact()" >
							<div id="timedivwrap">
							<div id="reptime">Rep\'s Local Time:  '.$reptime.'</div>
							<div id="contacttime">Contact\'s Local Time: '.$contacttime.'</div>
							<div class="cleardiv"></div>
							</div>
							<ul>
								<li>
									<label for="first_name">First Name:</label>
									<span>'.$row['firstname'].'</span>
								</li>
								<li>
									<label for="last_name">Last Name:</label>
									<span>'.$row['lastname'].'</span>
								</li>
								<li>
									<label for="email">Email:</label>
									<span>'.$row['email'].'</span>
								</li>
								<li>
									<label>Phone:</label>
									<span>'.$row['phone'].'</span>
								</li>
								<li>
									<label for="city">City:</label>
									<span>'.$row['city'].'</span>
								</li>
								<li>
									<label for="state">State</label>
									<span>'.$row['state'].'</span>
								</li>
								<li>
									<label for="timezone">Timezone:</label>
									<span>'.$row['timezone'].'</span>
								</li>
								<li>
									<label>Tier:</label>
									<span>'.substr($row['tier_status'],0,1).'</span>
								</li>
								<li>
									<label for="tierstep">Status:</label>
									<span>'.$row['status_desc'].'</span>
								</li>
								<li>
									<label for="team_member">Team Member:</label>
									<span>'.$row['team_member'].'</span>
								</li>
								<li>
									<label for="notes">Notes:</label>
									<span>'.$row['notes'].'</span>
								</li>
								<li>
									<input type="hidden" name="cid" value="'.$currcid.'" />
									<input type="submit" class="button" value="Dump" />
								</li>
								<li>
									<div id="ajax_verify_update"></div>
								</li>
							</ul>
							<p align="center">Dumping a contact will move it out of your contacts list.<br />
									You can find your dumped contacts on the "Dumped Contacts" page.</p>
						</form>
					</div>';
			echo $displayform;
		}
		mysqli_free_result ($r); // Free up the resources.	

	} else { // If it did not run OK.

		// Public message:
		echo '<p>Lead data not retrieved.</p>';
					
		// Debugging message:
		//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
		
	} // End of if ($r)
	
	unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>