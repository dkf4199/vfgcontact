<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repsid = $_SESSION['rep_id'];
	$currcid = $_GET['currentcontactid'];
		
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$hist = '';
	$type = '';
	// Make query to pull 3x3 history (need cid and repid)
	$cq = "SELECT comm_type, comm_note, comm_date
		  FROM communication_matrix
		  WHERE rep_id='$repsid'
		  AND contact_id='$currcid'";
	$cr = mysqli_query($dbc, $cq);
	if ($cr){
		if (mysqli_num_rows($cr) > 0){
			while ($row = mysqli_fetch_array($cr, MYSQLI_ASSOC)) {
			
				switch ($row['comm_type']){
					case 'EM':
						$type = 'EMAIL';
						break;
					case 'PC':
						$type = 'PHONE CALL';
						break;
					case 'TM':
						$type = 'TEXT MSG';
						break;
				}
				//Format comm_date
				$commdt = strtotime( $row['comm_date'] );
				$formatted_commdt = date( 'm-d-Y h:i:s a', $commdt );
				$hist.= $formatted_commdt.' '.$type."\n".$row['comm_note']."\n\n";
			}
		}
		mysqli_free_result($cr);
	}
	
	// Make the query to pick the contact data:
	$q = "SELECT firstname, lastname, email, phone,
				city, state, timezone, tier_status, 
				notes, update_date, team_member, next_action_date,
				contact_type, recruit_or_customer, assigned_manager, assigned_consultant
		  FROM contacts
		  WHERE contact_id = '$currcid' LIMIT 1";		
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.
	  
	  if (mysqli_num_rows($r) == 1){
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
			if ($row['timezone'] != ''){
				date_default_timezone_set($row['timezone']);
				$contacttime = date("h:i:s a");
			} else {
				date_default_timezone_set($_SESSION['rep_tz']);
				$contacttime = date("h:i:s a");
			}
			//echo '<p align="center">
			//		<span class="editheader">Edit Contact Info: '.$row['firstname'].' '.$row['lastname'].'</span></p>';
			/*echo '<div id="timedivwrap">';
			echo '<div id="reptime">Rep\'s Local Time:  '.$reptime.'</div>';
			echo '<div id="contacttime">Contact\'s Local Time: '.$contacttime.'</div>';
			echo '<div class="cleardiv"></div>';
			echo '</div>';*/
			$displayform = "<div style='display:none'>
				<div class='contact-content'>
				<h1 class='contact-title'>Edit: ".$row['firstname']." ".$row['lastname'].".</h1>";
			$displayform .= '<form name="editcontact_form" id="editcontact_form" onSubmit="return updateContact()" >
						<div id="timedivwrap">
							<div id="reptime">Your Local Time:  '.$reptime.'</div>
							<div id="contacttime">'.$row['firstname'].'\'s Local Time: '.$contacttime.'</div>
							<div class="cleardiv"></div>
						</div>
						<table align="center" border="0">
							<tr>	<!-- First name, Last name -->
								<td><label for="first_name">First Name:</label>
									<input type="text" id="first_name" name="first_name" class="input150 capitalwords" maxlength="30"
											value="'.$row['firstname'].'" />
								</td>
								<td>
									<label for="last_name">Last Name:</label>
									<input type="text" id="last_name" name="last_name" class="input200 capitalwords" maxlength="45"  
										value="'.$row['lastname'].'" />
								</td>
							</tr>
							<tr>	<!-- Email, Phone -->
								<td>
									<label for="email">Email:</label>
									<input type="text" id="email" name="email" class="input200" maxlength="80"
										value="'.$row['email'].'" />&nbsp;
									<a href="'.$currcid.'" class="contact-mailer"><img src="./images/mail-icon.png" /></a>
									<div id="email_response"></div>
								</td>
								<td>
									<label>Phone:</label>
									<input type="text" name="phone" id="phone" class="input125"
												value="'.$row['phone'].'" />&nbsp;
									<!--<a href="'.$currcid.'" class="matrix-mail">
										<img src="./images/mail_icon.png" height="25px" width="30px" />
									</a>-->
									<a href="'.$currcid.'" class="matrix-phone">
										<img src="./images/phone_2.png" height="25px" width="25px" />
									</a>
									<a href="'.$currcid.'" class="matrix-text">
										<img src="./images/text_message.jpg" height="25px" width="25px" />
									</a>
								</td>
							</tr>
							<tr>	<!-- City, State, Timezone -->
								<td>
									<label for="city">City:</label>
									<input type="text" id="city" name="city" class="input125" maxlength="40"  
										value="'.$row['city'].'" />
								</td>
								<td>
									<label for="state">State:</label>';
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
								</td>
								</tr>
								<tr>	<!-- Timezone -->
								<td>
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
								</td>
								<td>	<!-- Tier, Tierstep -->
									<label>Tier:</label>
									<input type="radio" name="tier" id="tier1" value="1" onClick="javascript: setList(this.value);" '.
									($tier == '1' ? 'checked' : '').' /><span>1</span>
									<input type="radio" name="tier" id="tier2" value="2" onClick="javascript: setList(this.value);" '.
									($tier == '2' ? 'checked' : '').' /><span>2</span>
									<input type="radio" name="tier" id="tier3" value="3" onClick="javascript: setList(this.value);" '.
									($tier == '3' ? 'checked' : '').' /><span>3</span>
									<input type="radio" name="tier" id="tier4" value="4" onClick="javascript: setList(this.value);" '.
									($tier == '4' ? 'checked' : '').' /><span>4</span>
								</td>
								</tr>
								<tr>
								<td><!-- Team Member -->
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
								</td>';
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
								$displayform .= '<td>
									<label for="tierstep">Status:</label>
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
								</td>
							</tr>
							<tr>	
								<td> <!-- Recruit Customer -->
									<label for="team_member">Recruit/Customer:</label>';
									$selected_rorc = "";
									$selected_rorc = $row['recruit_or_customer'];
									
									$displayform .= '<select name="rorc" id="rorc">';
								
									foreach($recruit_or_customer as $id=>$name){
										if($selected_rorc == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										$displayform .= "<option $sel value=\"$id\">$name</option>";
									}
							$displayform .= '</select>
							</td>
								<td>
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
								</td>
							</tr>
							<tr>	
								<td> <!-- ASSIGNED MANAGER -->
									<label for="assigned_manager">Manager VFG ID:</label>
									<input type="text" id="assigned_manager" name="assigned_manager" class="input100 capitalwords" maxlength="5"
											value="'.$row['assigned_manager'].'" />
								<td>
								<label for="next_action_date">Next Action Date:</label>
								<input type="text" name="next_action_date" id="datepicker" value="'.$nd.'" />
							</td>
							</tr>
							<tr>	<!-- ASSIGNED CONSULTANT -->
								<td>
									<label for="assigned_consultant">Consultant VFG ID:</label>
									<input type="text" id="assigned_consultant" name="assigned_consultant" class="input100 capitalwords" maxlength="5"  
										value="'.$row['assigned_consultant'].'" />
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>	<!-- 3x3 History, Notes -->
								<td>
									<label for="matrix_history">Communication History:</label>
									<textarea id="matrix_history" name="matrix_history" rows="10" cols="40">'.$hist.'</textarea> 
								</td>
								<td valign="top">
									<label for="notes">Notes:</label>
									<textarea id="notes" name="notes" rows="6" cols="40">'.stripslashes($row['notes']).'</textarea> 
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center" >
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
									<input type="hidden" id="original_rorc" name="original_rorc" value="'.$row['recruit_or_customer'].'" />
									<input type="hidden" id="original_contacttype" name="original_contacttype" value="'.$row['contact_type'].'" />
									<input type="hidden" id="original_assignedmanager" name="original_assignedmanager" value="'.$row['assigned_manager'].'" />
									<input type="hidden" id="original_assignedconsultant" name="original_assignedconsultant" value="'.$row['assigned_consultant'].'" />
									<input type="hidden" id="data_changed" name="data_changed" value="false" />
									<input type="hidden" id="cid" name="cid" value="'.$currcid.'" />
									<input type="submit" class="modalbutton" value="Update" /><br />
									<div id="ajax_verify_update"></div>
								</td>
							</tr>
						</table>
					</form>
			  </div>
			 </div>';
			echo $displayform;
		}
		
	  } else {
		//mysqli_num_rows != 1
		echo "<div style='display:none'>
				<!--<div class='contact-top'></div>-->
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
				<!--<div class='contact-top'></div>-->
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