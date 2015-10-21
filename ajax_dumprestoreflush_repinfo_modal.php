<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	
	$rid = $_GET['rid'];			// reps id
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
			$q = "SELECT firstname, lastname, phone,
						rep_timezone, signup_date, vfgrepid,
						recruiter_vfgid, gmail_acct, rep_licensed, purchased_policy
				  FROM reps
				  WHERE rep_id = '$rid' LIMIT 1";
			break;
		case 'restore':
			$q = "SELECT firstname, lastname, phone,
						rep_timezone, signup_date, vfgrepid,
						recruiter_vfgid, gmail_acct, rep_licensed, purchased_policy
				  FROM dumped_reps
				  WHERE rep_id = '$rid' LIMIT 1";	
			break;
		case 'flush':
			$q = "SELECT firstname, lastname, phone,
						rep_timezone, signup_date, vfgrepid,
						recruiter_vfgid, gmail_acct, rep_licensed, purchased_policy
				  FROM dumped_reps
				  WHERE rep_id = '$rid' LIMIT 1";
			break;
	}
	
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.
	  
	  if (mysqli_num_rows($r) == 1){
		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
			//get standard name for the timezone....not the PHP constant
			$standard_timezone = '';
			foreach($americaTimeZones as $id=>$name){
				if ($row['rep_timezone'] == $id){
					$standard_timezone = $name;
				}
			}
			
			$displayform = "<div style='display:none'>
				<!--<div class='contact-top'></div>-->
				<div class='contact-content'>
				<h1 class='contact-title'>Dump Rep:     ".$row['firstname']." ".$row['lastname'].".</h1>
				<div class='contact-loading' style='display:none'></div>
				<div class='contact-message' style='display:none'></div>";
			
			// Set the form tag based on action
			switch ($action){
				case 'dump':
					$displayform .= '<form name="dumprep_form" id="dumprep_form" onSubmit="return dumpRep()" > ';
					break;
				case 'restore':
					$displayform .= '<form name="restorerep_form" id="restorerep_form" onSubmit="return restoreRep()" > ';
					break;
				case 'flush':
					$displayform .= '<form name="flushrep_form" id="flushrep_form" onSubmit="return flushRep()" > ';
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
			$displayform .= '<table align="center" border="0">
							<tr>	<!-- First name, Last name -->
								<td>
									<label>First Name:</label>
									<span class="readonly">'.$row['firstname'].'</span>
								</td>
								<td>
									<label>Last Name:</label>
									<span class="readonly">'.$row['lastname'].'</span>
								</td>
							</tr>
							<tr>	<!-- Email, Phone -->
								<td>
									<label>Gmail:</label>
									<span class="readonly">'.$row['gmail_acct'].'</span>
								</td>
								<td>
									<label>Phone:</label>
									<span class="readonly">'.$row['phone'].'</span>
								</td>
							</tr>
							<tr>	<!-- Timezone, Signup Date -->
								<td>
									<label>Timezone:</label>
									<span class="readonly">'.$standard_timezone.'</span>
								</td>
								<td>
									<label>Signup Date:</label>
									<span class="readonly">'.$row['signup_date'].'</span>
								</td>
							</tr>
							<tr> <!-- VFG ID, Recruiter VFG ID -->
								<td>
									<label>VFG ID:</label>
									<span class="readonly">'.$row['vfgrepid'].'</span>
								</td>
								<td>
									<label>Recruiter VFG ID:</label>
									<span class="readonly">'.$row['recruiter_vfgid'].'</span>
								</td>
							</tr>
							<tr>	<!-- Rep Licensed, Purchased Policy -->
								<td>
									<label>Rep Licensed:</label>
									<span class="readonly">'.$row['rep_licensed'].'</span>
								</td>
								<td>
									<label>Purchased Policy:</label>
									<span class="readonly">'.$row['purchased_policy'].'</span>
								</td>
							</tr>';
			// Set the submit section based on action
			switch ($action){
				case 'dump':
					$displayform .= '<tr>
								<td colspan="2" align="center" >
									<input type="hidden" name="rid" id="rid" value="'.$rid.'" />
									<input type="submit" class="modalbutton" value="Dump" />
									<br />
									<div id="ajax_verify_repdump">
										Dumping a recruit will move it to the "Dumped Reps" list.
									</div>
								</td>
							</tr>';
					break;
				case 'restore':
					$displayform .= '<tr>
								<td colspan="2" align="center" >
									<input type="hidden" name="rid" value="'.$rid.'" />
									<input type="submit" class="modalbutton" value="Restore" />
									<br />
									<div id="ajax_verify_represtore">
										Restoring a rep will move it out of your "Dumped Reps" list.<br />
										You can then find the rep on the "Manage Reps" page.
									</div>
								</td>
							</tr>';
					break;
				case 'flush':
					$displayform .= '<tr>
								<td colspan="2" align="center" >
									<input type="hidden" name="rid" value="'.$rid.'" />
									<input type="submit" class="modalbutton" value="Flush" />
									<br />
									<div id="ajax_verify_repflush">
										<font color="red">WARNING:</font>  Flushing this rep will PERMANENTLY remove their data from the system!
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
					<h1 class='contact-title'>Rep Does Not Exist:</h1>
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