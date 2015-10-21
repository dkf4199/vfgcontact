<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	
	$currcid = $_GET['currentcontactid'];
		
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	// Make the query:
	$q = "SELECT firstname, lastname, email, phone,
				city, state, timezone, tier_status, 
				notes, update_date, team_member, next_action_date, contact_type
		  FROM contacts
		  WHERE contact_id = '$currcid' LIMIT 1";		
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.

		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
			//split the tier_status
			$tier = substr($row['tier_status'],0,1);
			$tierstep = substr($row['tier_status'],1,1);
			
			//reformat the next_action_date (yyyy-mm-dd to mm-dd-yyyy)
			$nd = '';
			$nd = $row['next_action_date'];
			if ($nd != ''){
				$next_action_dt = substr($nd,5,2).'-'.substr($nd,8,2).'-'.substr($nd,0,4);
				$nd = $next_action_dt;
			}
			
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
					<span class="editheader">Edit Contact Info: '.$row['firstname'].' '.$row['lastname'].'</span></p>';
			/*echo '<div id="timedivwrap">';
			echo '<div id="reptime">Rep\'s Local Time:  '.$reptime.'</div>';
			echo '<div id="contacttime">Contact\'s Local Time: '.$contacttime.'</div>';
			echo '<div class="cleardiv"></div>';
			echo '</div>';*/
			$displayform = 
					'<div class="formdiv">
						
						<form name="editcontact_form" id="editcontact_form" onSubmit="return updateContact()" >
							<div id="timedivwrap">
							<div id="reptime">Rep\'s Local Time:  '.$reptime.'</div>
							<div id="contacttime">Contact\'s Local Time: '.$contacttime.'</div>
							<div class="cleardiv"></div>
							</div>
							<ul>
								<li>
									<label for="first_name">First Name:</label>
									<input type="text" id="first_name" name="first_name" class="input200 capitalwords" maxlength="30"
										value="'.$row['firstname'].'" />
								</li>
								<li>
									<label for="last_name">Last Name:</label>
									<input type="text" id="last_name" name="last_name" class="input200 capitalwords" maxlength="45"  
										value="'.$row['lastname'].'" />
								</li>
								<li>
									<label for="email">Email:</label>
									<input type="text" id="email" name="email" class="input200" maxlength="80"
										value="'.$row['email'].'" />
								</li>
								<li>
									<label>Phone:</label>
									<input type="text" name="phone" id="phone" class="input125"
												value="'.$row['phone'].'" />
								</li>
								<li>
									<label for="city">City:</label>
									<input type="city" id="city" name="city" class="input125" maxlength="40"  
										value="'.$row['city'].'" />
								</li>
								<li>
								<label for="state">State</label>';
									$selected_state = "";
									$selected_state = $row['state'];
									
									$displayform .= '<select name="state" id="state">';
								
									foreach($states as $id=>$name){
										if($selected_state == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										$displayform .= "<option $sel value=\"$id\">$name</option>";
									}
										
									$displayform .= '</select>
								</li>
								<li>
									<label for="timezone">Timezone:</label>';
			 
									$selected_tz = "";
									$selected_tz = $row['timezone'];

									$displayform .= '<select id="timezone" name="timezone">';
										
									foreach($americaTimeZones as $id=>$name){
										if($selected_tz == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										$displayform .= "<option $sel value=\"$id\">$name</option>";
									}
										
									$displayform .= '</select>
								</li>
								<li>
									<label>Tier:</label>
									<input type="radio" name="tier" id="tier1" value="1" onClick="javascript: setList(this.value);" '.
											($tier == '1' ? 'checked' : '').' /><label for="tier1">1</label>
									<input type="radio" name="tier" id="tier2" value="2" onClick="javascript: setList(this.value);" '.
											($tier == '2' ? 'checked' : '').' /><label for="tier2">2</label>
									<input type="radio" name="tier" id="tier3" value="3" onClick="javascript: setList(this.value);" '.
											($tier == '3' ? 'checked' : '').' /><label for="tier3">3</label>
									<input type="radio" name="tier" id="tier4" value="4" onClick="javascript: setList(this.value);" '.
											($tier == '4' ? 'checked' : '').' /><label for="tier4">4</label>
								</li>';
								
								//get the tierstep array based on the tier level
								$tierarray = '';
								switch ($tier){
									case '1':
										$tierarray = $tier1steps;
										break;
									case '2':
										$tierarray = $tier2steps;
										break;
									case '3':
										$tierarray = $tier3steps;
										break;
									case '4':
										$tierarray = $tier4steps;
										break;
								}
								$displayform .= '<label for="tierstep">Status:</label>
												<select name="tierstep" id="tierstep">';
								foreach($tierarray as $id=>$name){
									if($tierstep == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									$displayform .= "<option $sel value=\"$id\">$name</option>";
								}
								$displayform .= '</select>
												 </li>
									<li>
									<label for="team_member">Team Member:</label>';
									$selected_teammember = "";
									$selected_teammember = $row['team_member'];
									
									$displayform .= '<select name="team_member" id="team_member">';
								
									foreach($yes_or_no as $id=>$name){
										if($selected_teammember == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										$displayform .= "<option $sel value=\"$id\">$name</option>";
									}
										
									$displayform .= '</select>
												</li>
												<li>
									<label for="contact_type">Contact Type:</label>';
									$selected_contacttype = "";
									$selected_contacttype = $row['contact_type'];
									
									$displayform .= '<select name="contact_type" id="contact_type">';
								
									foreach($direct_indirect as $id=>$name){
										if($selected_contacttype == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										$displayform .= "<option $sel value=\"$id\">$name</option>";
									}
										
									$displayform .= '</select>
												</li>
												<li>
									<label for="notes">Notes:</label>
									<textarea id="notes" name="notes" rows="6" cols="40">'.stripslashes($row['notes']).'</textarea> 
											
								</li>
								<li>
									<label for="next_action_date">Next Action Date:</label>
									<input type="text" name="next_action_date" id="datepicker" value="'.$nd.'" />
								</li>
								<li>
									<input type="hidden" name="submitted" value="updatecontact" />
									<input type="hidden" id="original_firstname" name="original_firstname" value="'.$row['firstname'].'" />
									<input type="hidden" id="original_lastname" name="original_lastname" value="'.$row['lastname'].'" />
									<input type="hidden" id="original_email" name="original_email" value="'.$row['email'].'" />
									<input type="hidden" id="original_phone" name="original_phone" value="'.$row['phone'].'" />
									<input type="hidden" id="original_city" name="original_city" value="'.$row['city'].'" />
									<input type="hidden" id="original_state" name="original_state" value="'.$row['state'].'" />
									<input type="hidden" id="original_timezone" name="original_timezone" value="'.$row['timezone'].'" />
									<input type="hidden" id="original_tier" name="original_tier" value="'.$tier.'" />
									<input type="hidden" id="original_tierstep" name="original_tierstep" value="'.$tierstep.'" />
									<input type="hidden" id="original_teammember" name="original_teammember" value="'.$row['team_member'].'" />
									<input type="hidden" id="original_notes" name="original_notes" value="'.$row['notes'].'" />
									<input type="hidden" id="original_nextactiondate" name="original_nextactiondate" value="'.$nd.'" />
									<input type="hidden" id="original_contacttype" name="original_contacttype" value="'.$row['contact_type'].'" />
									<input type="hidden" id="data_changed" name="data_changed" value="false" />
									<input type="hidden" id="cid" name="cid" value="'.$currcid.'" />
									<input type="submit" class="button" value="Update" />
								</li>
								<li>
									<div id="ajax_verify_update"></div>
								</li>
							</ul>
							<p align="center">Last Update was on '.$formatted_updatedt.'</p>
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