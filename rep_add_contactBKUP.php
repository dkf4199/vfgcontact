<?php
session_start();
// If no agent session value is present, redirect the user:
if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {
	require_once ('./includes/phpfunctions.php');
	$url = absolute_url('rep_login.php');
	//javascript redirect using window.location
	echo '<script language="Javascript">';
	echo 'window.location="' . $url . '"';
	echo '</script>';
	exit();	
}
?>
<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>EAPCRM Leads Management - It's Easy As Pie</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<script type="text/javascript" src="./js/autotab.js"></script>
<script src="./js/jquery1-9-1.js"></script>
<script src="./js/jquery_migrate1-1-0.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>

</head>

<body>
<div class="wrapper showgoldborder">
	<div class="header">
		<span>EAPCRM.com.  Leads Management - "It's easy as pie..."</span>
	</div>
	<div class="topnav">
		<ul id="menu_top">
			<li><a href="rep_maindash.php">Home</a></li>
			<li><a href="rep_add_contact.php">Add Contact</a></li>
			<li><a href="#">Active Contacts</a>
				<ul id="static_rpt_submenu">
					<li><a href="rep_manage_contacts.php">Manage Contacts</a></li>
					<li><a href="rep_manage_tasks.php">Manage Tasks</a></li>
					<li><a href="rep_manage_team.php">Manage Team</a></li>
				</ul>
			</li>
			<li><a href="rep_dumped_contacts.php">Dumped Contacts</a></li>
			<li><a href="rep_logout.php">Logout</a></li>
		</ul>
	</div>
	
	<div class="maincontent">
		<p class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></p>
		
		<?php
			include ('includes/selectlists.php');
			include ('includes/phpfunctions.php');
			include ('includes/config.inc.php');
			
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "addcontact"){
				
				date_default_timezone_set($_SESSION['rep_tz']);
				//DB Connection
				require_once (MYSQL);
				$errors = array();		//initialize errors array
				$messages = array();	//initialize messages array
				$contactadded = false;  //boolean switch for base contact data
				
				//VALIDATE FIELDS
				// 08/02/2013 dkf - Change to allow for blank fields
				//                  fill with 'none'
				//*******************************************************
				//FIRST NAME - Have to have first name
				if (empty($_POST['first_name'])){
					$errors[] = 'Please enter contact\'s first name.';
				} elseif (!preg_match("/^[A-Za-z]+$/", trim($_POST['first_name']))) {
					$errors[] = 'Your first name contains at least 1 invalid character.';
				} else {
					$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
					$fn = ucwords($fname);
				}
				
				//LAST NAME
				if (empty($_POST['last_name'])){
					//$errors[] = 'Please enter contact\'s last name.';
					$ln = 'none';
				} elseif (!preg_match("/^[A-Za-z -]+$/", trim($_POST['last_name']))) {
					$errors[] = 'Your last name contains at least 1 invalid character.';
				} else {
					$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
					$ln = ucwords($lname);
				}
				
				//EMAIL
				//check email
				if (empty($_POST['email'])){
					//$errors[] = 'Enter contact\'s email.';
					$email = 'none';
				} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['email']))) {
					$errors[] = 'Enter a valid email address.';
				} else {
					$email = strip_tags(trim($_POST['email']));

					//IS EMAIL ALREADY IN DB?
					$query = "SELECT email FROM contacts WHERE email = '$email'";
					//RUN QUERY
					$rs = @mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) == 1) {
						//email already exists!
						$errors[] = 'Email address already exists in database.';
					}
					mysqli_free_result($rs);
				}
				
				//phone area, prefix, suffix
				if (empty($_POST['areacode'])){
					$errors[] = 'Please enter contact\'s area code.';
				} elseif (!valid_phone(trim($_POST['areacode']), "area")) {
					$errors[] = 'Area code must be 3 digits and numeric.';
				} else{
					$phoneareacode = trim($_POST['areacode']);
				}

				if (empty($_POST['prefix'])){
					$errors[] = 'Please enter contact\'s phone prefix.';
				} elseif (!valid_phone(trim($_POST['prefix']), "prefix")) {
					$errors[] = 'Phone prefix must be 3 digits and numeric.';
				} else{
					$phoneprefix = trim($_POST['prefix']);
				}

				if (empty($_POST['suffix'])){
					$errors[] = 'Please enter contact\'s phone suffix.';
				} elseif (!valid_phone(trim($_POST['suffix']), "suffix")) {
					$errors[] = 'Phone suffix must be 4 digits and numeric.';
				} else{
					$phonesuffix = trim($_POST['suffix']);
				}
				
				if (empty($_POST['city'])){
					$errors[] = 'Please enter contact\'s city.';
				} elseif (!preg_match("/^[A-Za-z ]+$/", trim($_POST['city']))) {
					$errors[] = 'City contains at least 1 invalid character.';
				} else {
					$city = strip_tags(ucwords(trim(strtolower($_POST['city']))));
				}

				if ($_POST['state'] == ""){
					$errors[] = 'Select contact\'s state.';
				} else {
					$state = strip_tags(trim($_POST['state']));
				}
				
				//Timezone
				if (empty($_POST['timezone'])){
					$errors[] = 'Please select contact\'s time zone.';
				} else {
					$tz = mysqli_real_escape_string($dbc, trim($_POST['timezone']));
				}
				
				//tier
				$tier = strip_tags(trim($_POST['tier']));
				
				//tierstep
				if ($_POST['tierstep'] == ""){
					$errors[] = 'Select contact\'s Tier 1 step.';
				} else {
					$tierstep = strip_tags(trim($_POST['tierstep']));
				}
				
				//Team Member
				$teammember = $_POST['team_member'];
				
				//notes
				if (empty($_POST['notes'])){
					$notes = 'none';
				} else {
					$notes = mysqli_real_escape_string($dbc, trim($_POST['notes']));
				}
				//*************** END FIELD VALIDATION ***********************
				
				if (empty($errors)){
				
					$rightnow = date("Y-m-d H:i:s");
					$repid = $_SESSION['rep_id'];
					$phone = $phoneareacode.$phoneprefix.$phonesuffix;
					$tierstatus = $tier.$tierstep;
					
					//Create Unique ID for this contact - FNinit.LNinit.5 digit number
					$idexists = true;
					do {
						$randnum = mt_rand(1,99999);
						$strnum = strval($randnum);

						switch (strlen($strnum)) {
							case 1:
								$finalnum = '0000'.$strnum;
								break;
							case 2:
								$finalnum = '000'.$strnum;
								break;
							case 3:
								$finalnum = '00'.$strnum;
								break;
							case 4:
								$finalnum = '0'.$strnum;
								break;
							case 5:
								$finalnum = $strnum;
								break;
						}

						//make the rep's id
						$contactid = substr($fn,0,1).substr($ln,0,1).$finalnum;
						
						//IS UNIQUEID ALREADY IN contactid_lookup DB?
						$query = "SELECT contact_id FROM contactid_lookup WHERE contact_id = '$contactid'";
						//RUN QUERY
						$rs = @mysqli_query ($dbc, $query);
						if (mysqli_num_rows($rs) != 1) {
							//id is unique
							$idexists = false;
						}
						mysqli_free_result($rs);
					} while ($idexists);
				
					//prepared statement - INSERT DATA into reps
					$q = "INSERT INTO contacts (contact_id, firstname, lastname, email, 
								phone, city, state, timezone, tier_status, notes, entry_date, rep_id, update_date, team_member) 
						  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

					//prepare statement
					$stmt = mysqli_prepare($dbc, $q);

					//bind variables to statement
					mysqli_stmt_bind_param($stmt, 'ssssssssssssss', $contactid, $fn, $ln, $email, 
							$phone, $city, $state, $tz, $tierstatus, $notes, $rightnow, $repid, $rightnow, $teammember);

					//execute query
					mysqli_stmt_execute($stmt);

					if (mysqli_stmt_affected_rows($stmt) == 1) {	//rep data insert successful

						$contactadded = true;
						
						// CLOSE rep data insert statement
						mysqli_stmt_close($stmt);
						
						//echo '<div id="messages">Contact Added.  Add another if you wish.</div>';					
						unset($_POST);
						
					} else {	//stmt_affected_row != 1 for base data

						//echo '<div id="messages">There was a system issue with your data.</div>';
						$messages[] = 'There was a system issue with base data.';

						//echo '<p>' . mysqli_stmt_error($stmt) . '</p>';
						//echo '</div>';
					
					}	//close base data insert
					
					/*
					//Add FIRST TIER value for this contact to contact_progress table
					if ($contactadded) {
						$q = sprintf("INSERT INTO contact_progress (contact_id, contact_status)
										VALUES ('%s', '%s')", $contactid, "1A");
						$r = mysqli_query($dbc,$q);
						if (mysqli_affected_rows($dbc) == 1){
							$messages[] = 'Contact Added.  Add another if you wish.';
						} else {
							$messages[] = 'There was a system issue with status data.';
						}
					}
					*/
					//Add contact_id value for this contact to contactid_lookup table
					if ($contactadded) {
						$q = sprintf("INSERT INTO contactid_lookup (contact_id)
										VALUES ('%s')", $contactid);
						$r = mysqli_query($dbc,$q);
						if (mysqli_affected_rows($dbc) == 1){
							$messages[] = 'Contact Added.  Add another if you wish.';
						} else {
							$messages[] = 'There was a system issue with the data.';
						}
					}
					
					//Display messages
					echo '<div id="messages">';
					foreach ($messages as $msg){

						echo "$msg<br />\n";
					}

					echo '</div>';
					
					//CLOSE connection
					mysqli_close($dbc);			
				
				} else {	//Have errors
				
					//DISPLAY ERRORS[] ARRAY MESSAGES
					echo '<div id="messages">';
					echo '<p><u>ERRORS:</u><br /><br />';

					foreach ($errors as $msg){

						echo " - $msg<br />\n";
					}

					echo '</p></div>';
				}
				
			} else { //close isset submitted

				echo '<div id="messages">Fill out form to add contact.</div>';
				
			}
		  ?>
		<div class="formdiv">
				<form name="rep_addcontact" id="rep_addcontact" action="rep_add_contact.php" method="POST" >
					
					<h2>Enter Contact Info:</h2>
					<ul>
						<li>
							<label for="first_name">First Name:</label>
							<input type="text" id="first_name" name="first_name" class="input200 capitalwords" maxlength="30"
								value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" />
						</li>
						<li>
							<label for="last_name">Last Name:</label>
							<input type="text" id="last_name" name="last_name" class="input200 capitalwords" maxlength="45"  
								value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" />
						</li>
						<li>
							<label for="email">Email:</label>
							<input type="text" id="email" name="email" class="input200" maxlength="80"
								value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
						</li>
						<li>
							<label>Phone:</label>
							<input type="text" name="areacode" id="areacode" size="3" maxlength="3" onKeyup="autotab(this, document.rep_addcontact.prefix)"
										value="<?php if (isset($_POST['areacode'])) echo $_POST['areacode']; ?>" />
							<input type="text" name="prefix" id="prefix" size="3" maxlength="3" onKeyup="autotab(this, document.rep_addcontact.suffix)"
										value="<?php if (isset($_POST['prefix'])) echo $_POST['prefix']; ?>" />
							<input type="text" name="suffix" id="suffix" size="4" maxlength="4"
										value="<?php if (isset($_POST['suffix'])) echo $_POST['suffix']; ?>" />
						</li>
						<li>
							<label for="city">City:</label>
							<input type="city" id="city" name="city" class="input125" maxlength="40"  
								value="<?php if (isset($_POST['city'])) echo $_POST['city']; ?>"/>
						</li>
						<li>
						<label for="state">State</label>
							<?php 
								$selected_state = "";
								if (isset($_POST['state'])){
									$selected_state = $_POST['state'];
								} 
							?>
							<select name="state" id="state">
								<?php
									foreach($states as $id=>$name){
										if($selected_state == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
							</select>
						</li>
						<li>
							<label for="timezone">Timezone:</label>
							<?php 
								$selected_tz = "";
								if (isset($_POST['timezone'])){
									$selected_tz = $_POST['timezone'];
								}
							?>
							<select id="timezone" name="timezone">
								<?php
									foreach($americaTimeZones as $id=>$name){
										if($selected_tz == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
							</select>
						</li>
						<li>
							<?php
								if (!isset($tier)){
									$tier = '1';
								}
							?>
							<label>Tier:</label>
							<input type="radio" name="tier" id="tier1" value="1" <?php echo ($tier == '1' ? 'checked' : ''); ?> 
									 onClick="javascript: setList(this.value);" /><label for="tier1">1</label>
							<input type="radio" name="tier" id="tier2" value="2" <?php echo ($tier == '2' ? 'checked' : ''); ?>  
									 onClick="javascript: setList(this.value);" /><label for="tier2">2</label>
							<input type="radio" name="tier" id="tier3" value="3" <?php echo ($tier == '3' ? 'checked' : ''); ?>  
									 onClick="javascript: setList(this.value);" /><label for="tier3">3</label>
							<input type="radio" name="tier" id="tier4" value="4" <?php echo ($tier == '4' ? 'checked' : ''); ?>  
									 onClick="javascript: setList(this.value);" /><label for="tier4">4</label>
						</li>
						<li>
							<label for="tierstep">Status:</label>
							<?php
								//get the tierstep array based on the tier level
								$tierarray = '';
								$thistier = '';
								if (isset($_POST['tier'])){
									$thistier = $_POST['tier'];
								}
								switch ($thistier){
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
									default:
										$tierarray = $tier1steps;
										break;
								}
								
								$selected_tier = "";
								if (isset($_POST['tierstep'])){
									$selected_tier = $_POST['tierstep'];
								} 
							?>
							<select name="tierstep" id="tierstep">
								<?php
									foreach($tierarray as $id=>$name){
										if($selected_tier == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
							</select>
						</li>
						<li>
						<label for="team_member">Team Member:</label>
							<?php 
								$selected_teammember = "";
								if (isset($_POST['team_member'])){
									$selected_teammember = $_POST['team_member'];
								} 
							?>
							<select name="team_member" id="team_member">
								<?php
									foreach($yes_or_no as $id=>$name){
										if($selected_teammember == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
							</select>
						</li>
						<li>
							<label for="notes">Notes:</label>
							<textarea id="notes" name="notes" rows="6" cols="40"><?php if (isset($_POST['notes'])) echo $_POST['notes']; ?></textarea> 
									
						</li>
						<li>
							<input type="hidden" name="submitted" value="addcontact" />
							<input type="submit" class="button" value="Add Contact" />
						</li>
					</ul>
				</form>
			</div>		
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
</body>
</html>