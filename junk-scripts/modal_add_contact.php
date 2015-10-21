<?php
//add contact form
include ('includes/selectlists.php');
include ('includes/config.inc.php');
include ('includes/selectlists.php');
//$currcid = 'TC46024';
$currcid = $_GET['currentcontactid'];
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
		
		//Format the update_date - This is the "last updated by" field
		if ($row['update_date'] != ''){
			$updatedt = strtotime( $row['update_date'] );
			$formatted_updatedt = date( 'm-d-Y h:i:s a', $updatedt );
		} else {
			$formatted_updatedt = '';
		}
		//display timezone data
		//rep time:
		#date_default_timezone_set($_SESSION['rep_tz']);
		date_default_timezone_set('America/Los_Angeles');
		
		$reptime = date("h:i:s a");
		//contact time:
		date_default_timezone_set($row['timezone']);
		$contacttime = date("h:i:s a");
		
		$displayform = "<div style='display:none'>
			<div class='contact-top'></div>
			<div class='contact-content'>
				<h1 class='contact-title'>Edit Contact:</h1>
				<div class='contact-loading' style='display:none'></div>
				<div class='contact-message' style='display:none'></div>";
				$displayform .= '<form name="editcontact_form" id="editcontact_form" onSubmit="return updateContact()" >
					<div id="timedivwrap">
						<div id="reptime">Rep\'s Local Time:  '.$reptime.'</div>
						<div id="contacttime">Contact\'s Local Time: '.$contacttime.'</div>
						<div class="cleardiv"></div>
					</div>
					<p />
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
									value="'.$row['email'].'" />
							</td>
							<td>
								<label>Phone:</label>
								<input type="text" name="phone" id="phone" class="input125"
											value="'.$row['phone'].'" />
							</td>
						</tr>
						<tr>	<!-- City, State, Timezone -->
							<td>
								<label for="city">City:</label>
								<input type="city" id="city" name="city" class="input125" maxlength="40"  
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
							<tr>
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
							<td>&nbsp;</td>
						</tr>
						<tr> <!-- Tier, Tierstep -->
							<td>
								<label>Tier:</label>
								<input type="radio" name="tier" id="tier1" value="1" onClick="javascript: setList(this.value);" '.
								($tier == '1' ? 'checked' : '').' /><span>1</span>
								<input type="radio" name="tier" id="tier2" value="2" onClick="javascript: setList(this.value);" '.
								($tier == '2' ? 'checked' : '').' /><span>2</span>
								<input type="radio" name="tier" id="tier3" value="3" onClick="javascript: setList(this.value);" '.
								($tier == '3' ? 'checked' : '').' /><span>3</span>
								<input type="radio" name="tier" id="tier4" value="4" onClick="javascript: setList(this.value);" '.
								($tier == '4' ? 'checked' : '').' /><span>4</span>
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
						<tr>	<!-- Team Member, Contact Type -->
							<td>
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
						<tr>	<!-- Next Action Date, Notes -->
							<td>
								<label for="next_action_date">Next Action Date:</label>
								<input type="text" name="next_action_date" id="datepicker" value="'.$nd.'" />
							</td>
							<td>
								<label for="notes">Notes:</label>
								<textarea id="notes" name="notes" rows="6" cols="40">'.$row['notes'].'</textarea> 
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center" >
								<input type="hidden" name="submitted" value="updatecontact" />
								<input type="submit" class="modalbutton" value="Update" /><br />
								<div id="ajax_verify_update">Text Text</div>
							</td>
						</tr>
					</table>
				</form>
		</div>
		</div>';
		echo $displayform;
	}	//end while
	
  } else {
	//mysqli_num_rows != 1
	echo "<div style='display:none'>
			<div class='contact-top'></div>
			<div class='contact-content'>
				<h1 class='contact-title'>Contact Does Not Exist:</h1>
				<div class='contact-loading' style='display:none'></div>
				<div class='contact-message' style='display:none'></div>
			</div>
			</div>";
  } //end num_rows == 1
  
	mysqli_free_result ($r); // Free up the resources.

} else { // If it did not run OK.

		// Public message:
		echo "<div style='display:none'>
			<div class='contact-top'></div>
			<div class='contact-content'>
				<h1 class='contact-title'>System Error:</h1>
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