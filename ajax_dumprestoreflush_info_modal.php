<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	
	$currcid = $_GET['currentcontactid'];
	$action = $_GET['action'];		// $action is 'dump' 'restore' or 'flush'
		
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	$q = '';
	// Make the query based on action:  NO DEFAULT on switch statement!!
	switch ($action) {
		case 'dump':
			$q = "SELECT a.firstname, a.lastname, a.email, a.phone,
				     a.city, a.state, a.timezone, a.tier_status, a.notes, a.update_date, a.team_member, b.status_desc
		          FROM contacts a LEFT JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
		          WHERE a.contact_id = '$currcid' LIMIT 1";
			break;
		case 'restore':
			$q = "SELECT a.firstname, a.lastname, a.email, a.phone,
				     a.city, a.state, a.timezone, a.tier_status, a.notes, a.update_date, a.team_member, b.status_desc
				  FROM dumped_contacts a LEFT JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
				  WHERE a.contact_id = '$currcid' LIMIT 1";	
			break;
		case 'flush':
			$q = "SELECT a.firstname, a.lastname, a.email, a.phone,
				     a.city, a.state, a.timezone, a.tier_status, a.notes, a.update_date, a.team_member, b.status_desc
				  FROM dumped_contacts a LEFT JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
				  WHERE a.contact_id = '$currcid' LIMIT 1";
			break;
	}
	
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.
	  
	  if (mysqli_num_rows($r) == 1){
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
			if ($row['timezone'] != ''){
				date_default_timezone_set($row['timezone']);
			}
			$contacttime = date("h:i:s a");
			
			//get standard name for the timezone....not the PHP constant
			$standard_timezone = '';
			foreach($americaTimeZones as $id=>$name){
				if ($row['timezone'] == $id){
					$standard_timezone = $name;
				}
			}
			
			$displayform = "<div style='display:none'>
				<!--<div class='contact-top'></div>-->
				<div class='contact-content'>
				<h1 class='contact-title'>Dump Recruit:     ".$row['firstname']." ".$row['lastname'].".</h1>
				<div class='contact-loading' style='display:none'></div>
				<div class='contact-message' style='display:none'></div>";
			
			// Set the form tag based on action
			switch ($action){
				case 'dump':
					$displayform .= '<form name="dumpcontact_form" id="dumpcontact_form" onSubmit="return dumpContact()" > ';
					break;
				case 'restore':
					$displayform .= '<form name="viewdumpcontact_form" id="viewdumpcontact_form" onSubmit="return restoreContact()" > ';
					break;
				case 'flush':
					$displayform .= '<form name="flushdumpcontact_form" id="flushdumpcontact_form" onSubmit="return flushContact()" > ';
					break;
			}
			switch ($action){
				case 'dump':
					break;
				case 'restore':
					break;
				case 'flush':
					break;
			}
			$displayform .= '<div class="reptime">Your Local Time:  '.$reptime.'</div>
							<div class="contacttime">'.$row['firstname'].'\'s Local Time: '.$contacttime.'</div>
							<div class="cleardiv"></div>
						
						<table align="center" border="0">
							<tr>	<!-- First name, Last name -->
								<td>
									<label for="first_name">First Name:</label>
									<span class="readonly">'.$row['firstname'].'</span>
								</td>
								<td>
									<label for="last_name">Last Name:</label>
									<span class="readonly">'.$row['lastname'].'</span>
								</td>
							</tr>
							<tr>	<!-- Email, Phone -->
								<td>
									<label for="email">Email:</label>
									<span class="readonly">'.$row['email'].'</span>
								</td>
								<td>
									<label>Phone:</label>
									<span class="readonly">'.$row['phone'].'</span>
								</td>
							</tr>
							<tr>	<!-- City, State, Timezone -->
								<td>
									<label for="city">City:</label>
									<span class="readonly">'.$row['city'].'</span>
								</td>
								<td>
									<label for="state">State</label>
									<span class="readonly">'.$row['state'].'</span>
								</td>
								</tr>
								<tr>	<!-- Timezone -->
								<td>
									<label for="timezone">Timezone:</label>
									<span class="readonly">'.$standard_timezone.'</span>
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr> <!-- Tier, Tierstep -->
								<td>
									<label>Tier:</label>
									<span class="readonly">'.substr($row['tier_status'],0,1).'</span>
								</td>
								<td>
									<label for="tierstep">Status:</label>
									<span class="readonly">'.$row['status_desc'].'</span>
								</td>
							</tr>
							<tr>	<!-- Team Member, Contact Type -->
								<td>
									<label for="team_member">Team Member:</label>
									<span class="readonly">'.$row['team_member'].'</span>
								</td>
								<td>
									<label for="notes">Notes:</label>
									<textarea rows="3" cols="40">'.stripslashes($row['notes']).'</textarea>
								</td>
							</tr>';
			// Set the submit section based on action
			switch ($action){
				case 'dump':
					$displayform .= '<tr>
								<td colspan="2" align="center" >
									<input type="hidden" name="cid" id="cid" value="'.$currcid.'" />
									<input type="submit" class="modalbutton" value="Dump" />
									<br />
									<div id="ajax_verify_dump">
										Dumping a recruit will move it to your "Dumped Recruits" list.
									</div>
								</td>
							</tr>';
					break;
				case 'restore':
					$displayform .= '<tr>
								<td colspan="2" align="center" >
									<input type="hidden" name="cid" value="'.$currcid.'" />
									<input type="submit" class="modalbutton" value="Restore" />
									<br />
									<div id="ajax_verify_restore">
										Restoring a contact will move it out of your "Dumped Contacts" list.<br />
										You can then find the contact on the "Manage Contacts" page.
									</div>
								</td>
							</tr>';
					break;
				case 'flush':
					$displayform .= '<tr>
								<td colspan="2" align="center" >
									<input type="hidden" name="cid" value="'.$currcid.'" />
									<input type="submit" class="modalbutton" value="Flush" />
									<br />
									<div id="ajax_verify_flush">
										<font color="red">WARNING:</font>  Flushing this recruit will PERMANENTLY remove their data from the system!
									</div>
								</td>
							</tr>';
					break;
			}				
							
			$displayform .= '</table>
							</form>
						  </div>
						 </div>
						</div>';
			echo $displayform;
		}
		
	  } else {
		//mysqli_num_rows != 1
		echo "<div style='display:none'>
				<div class='contact-content'>
					<h1 class='contact-title'>Contact Does Not Exist:</h1>
					<div class='contact-loading' style='display:none'></div>
					<div class='contact-message' style='display:none'></div>
				</div>
				</div>";
	  } //end mysqli_num_rows == 1
  
	  mysqli_free_result ($r); // Free up the resources.

	} else { // If it did not run OK.

		// Public message:
		echo "<div style='display:none'>
				<div class='contact-content'>
					<h1 class='contact-title'>System Problem. Contact Support.</h1>
					<div class='contact-loading' style='display:none'></div>
					<div class='contact-message' style='display:none'></div>
				</div>
			</div>";
					
		// Debugging message:
		//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
		
	} // End of if ($r)
	
	unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>