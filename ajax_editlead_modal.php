<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	require_once (MYSQL);
	
	$repsid = $_SESSION['rep_id'];	
	//GET var
	$thislid = $_GET['lid'];
	
	$q = "SELECT firstname, lastname, phone, email, notes,
				priority_number, category		
		  FROM lead_list
		  WHERE l_id = $thislid LIMIT 1";		
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.
	  
		if (mysqli_num_rows($r) == 1){
			// Fetch and print all the records:
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
				$displayform = '<div style="display:none">
					<div class="contact-content">
						<h1 class="contact-title">Edit Lead: '.$row['firstname'].' '.$row['lastname'].'.</h1>
						<input type="button" id="make_prospect" class="generalbutton" value="Move to Prospects" />
						<br />
						<div class="leadcontentleft">
							<ul>
								<li>
									<label for="lead_category">Category:</label>
									<select name="lead_category" id="lead_category">';
								$selected_cat = '';
								$selected_cat = $row['category'];
								foreach($lead_category as $id=>$name){
									if($selected_cat == $id){
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
									<label for="first_name">First Name:</label>
									<input type="text" id="first_name" name="first_name" class="input150 capitalwords" 
												maxlength="30" value="'.$row['firstname'].'" />
								</li>
								<li>
									<label for="last_name">Last Name:</label>
									<input type="text" id="last_name" name="last_name" class="input150 capitalwords" 
											maxlength="30" value="'.$row['lastname'].'" />
								</li>
								<li>
									<label for="phone">Phone:</label>
									<input type="text" id="phone" name="phone" class="input150 capitalwords" 
											maxlength="15" value="'.$row['phone'].'" />
								</li>
								<li>
									<label for="email">Email:</label>
									<input type="text" id="email" name="email" class="input200 capitalwords" 
											maxlength="80" value="'.$row['email'].'" />
								</li>
								<li>
									<label for="lead_priority">Priority:</label>
									<select name="lead_priority" id="lead_priority">';
								$selected_pri = '';
								$selected_pri = $row['priority_number'];
								foreach($lead_priority as $id=>$name){
									if($selected_pri == $id){
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
									<label for="lead_notes">Notes:</label>
									<textarea id="lead_notes" name="lead_notes" rows="6" cols="40">'.stripslashes($row['notes']).'</textarea> 		
								</li>
								<li>
									<input type="hidden" id="lid" name="lid" value="'.$thislid.'" />
									<input type="hidden" id="og_category" name="og_category" value="'.$row['category'].'" />
									<input type="hidden" id="og_firstname" name="og_firstname" value="'.$row['firstname'].'" />
									<input type="hidden" id="og_lastname" name="og_lastname" value="'.$row['lastname'].'" />
									<input type="hidden" id="og_phone" name="og_phone" value="'.$row['phone'].'" />
									<input type="hidden" id="og_email" name="og_email" value="'.$row['email'].'" />
									<input type="hidden" id="og_priority" name="og_priority" value="'.$row['priority_number'].'" />
									<input type="hidden" id="og_notes" name="og_notes" value="'.stripslashes($row['notes']).'" />
									<input type="hidden" id="data_changed" name="data_changed" value="false" />
								</li>
							</ul>
							<p align="center"><input type="button" id="edit_lead" class="generalbutton" value="Edit Lead" /></p>
							
						</div> <!-- close contentleft -->
						<div class="leadcontentright">
							Fill out all fields to add a new lead to your list.
						</div>
						<div class="cleardiv"></div>
					</div></div>';
			
			}
		} else {
			//mysqli_num_rows != 1
			echo "<div style='display:none'>
					<!--<div class='contact-top'></div>-->
					<div class='contact-content'>
						<h1 class='contact-title'>Lead Does Not Exist:</h1>
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
	
	
	echo $displayform;
?>