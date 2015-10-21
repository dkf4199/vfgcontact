<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	
	$current_repid = $_GET['currentrepid'];
		
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	// Make the query:
	$q = "SELECT firstname, lastname, email, phone, rep_timezone, signup_date, vfgrepid, recruiter_vfgid
		  FROM reps
		  WHERE rep_id = '$current_repid' LIMIT 1";		
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.
	  
	  if (mysqli_num_rows($r) == 1){
		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
			//Format the update_date
			if ($row['signup_date'] != ''){
				$signupdt = strtotime( $row['signup_date'] );
				$formatted_signupdt = date( 'm-d-Y h:i:s a', $signupdt );
			} else {
				$formatted_signupdt = '';
			}
			//display timezone data
			//rep time:
			date_default_timezone_set($_SESSION['rep_tz']);
			$mytime = date("h:i:s a");
			//contact time:
			date_default_timezone_set($row['rep_timezone']);
			$otherreptime = date("h:i:s a");
			
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
				<h1 class='contact-title'>Rep Data for :   ".$row['firstname']." ".$row['lastname'].".</h1>
				<div class='contact-loading' style='display:none'></div>
				<div class='contact-message' style='display:none'></div>";
			$displayform .= '<form name="rep_form" id="repform_form" >
						<div id="timedivwrap">
							<div id="reptime">Your Local Time:  '.$mytime.'</div>
							<div id="contacttime">'.$row['firstname'].'\'s Local Time: '.$otherreptime.'</div>
							<div class="cleardiv"></div>
						</div>
						<p />
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
									<span class="readonly">'.$row['email'].'</span>&nbsp;
									<a href="#" class="rep_email" onClick="javascript: return repEmailer(\''.$row['email'].'\');" ><img src="./images/mail-icon.png" /></a>
									<div id="email_response"></div>
								</td>
								<td>
									<label>Phone:</label>
									<span class="readonly">'.$row['phone'].'</span>&nbsp;
									<a href="#" class="rep_text" onClick="javascript: return repTexter(\''.$row['phone'].'\');" >
											<img src="./images/text_message.jpg"  height="25px" width="25px" /></a>&nbsp;
									<a href="#" class="rep_phone" onClick="javascript: return repDialer(\''.$row['phone'].'\');" >
											<img src="./images/mobile_phone.png"  height="25px" width="25px" /></a>
								</td>
							</tr>
							<tr>	<!-- Timezone and Signup Date-->
								<td>
									<label for="timezone">Timezone:</label>
									<span class="readonly">'.$standard_timezone.'</span>
								</td>
								<td>
									<label for="signupdate">Signup Date:</label>
									<span class="readonly">'.$formatted_signupdt.'</span>
								</td>
							</tr>
							<tr>	<!-- VFGID and Recruiter VFGID -->
								<td>
									<label for="vfgid">VFG Rep ID:</label>
									<span class="readonly">'.$row['vfgrepid'].'</span>
								</td>
								<td>
									<label for="recruiter_vfgid">Recruiter\'s VFG ID:</label>
									<span class="readonly">'.$row['recruiter_vfgid'].'</span>
								</td>
							</tr>
						</table>
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