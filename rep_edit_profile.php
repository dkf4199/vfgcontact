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
<title>VFG Contacts - Agent Data</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>

</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
	
	  <?php
		include ('includes/selectlists.php');
		include ('./includes/config.inc.php');
		
		$repsid = $_SESSION['rep_id'];
		$repsvfgid = $_SESSION['vfgrep_id'];
		
		//DB Connection
		require_once (MYSQL);
		
		$messages = array();	//initialize an error array
		$messages[] = 'Edit Profile Data';
		
		//check form submission
		if (isset($_POST['submitted']) && $_POST['submitted'] == "editdata"){
			
			$errors = array();		//initialize error array
			$messages = array();	// re-initialize messages
			
			$email_changed = false;		//boolean switch for email change
			
			//VALIDATE FIELDS
			//*******************************************************
			//FIRST NAME
			if (empty($_POST['first_name'])){
				$errors[] = 'Please enter your first name.';
			} elseif (!preg_match("/^[A-Za-z]+$/", trim($_POST['first_name']))) {
				$errors[] = 'Your first name contains at least 1 invalid character.';
			} else {
				$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
				$first = ucwords(strtolower($fname));
			}
			
			//LAST NAME
			if (empty($_POST['last_name'])){
				$errors[] = 'Please enter your last name.';
			} elseif (!preg_match("/^[A-Za-z -]+$/", trim($_POST['last_name']))) {
				$errors[] = 'Your last name contains at least 1 invalid character.';
			} else {
				$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
				$last = ucwords(strtolower($lname));
			}
			
					
			if (empty($_POST['phone'])){
				$errors[] = 'Please provide your phone number.';
			} elseif ( preg_match("/^\d{3}[-]\d{3}[-]\d{4}$/", trim($_POST['phone'])) || preg_match("/^\d{10}$/", trim($_POST['phone']))) {
				if (strlen(trim($_POST['phone'])) == 10) {	//no dashes ##########
					$formattedphone = trim($_POST['phone']);
					$phone = substr($formattedphone,0,3).'-'.substr($formattedphone,3,3).'-'.substr($formattedphone,6,4);
				}
				if (strlen(trim($_POST['phone'])) == 12) {	//dashes ###-###-####
					$phone = trim($_POST['phone']);
				}
			} else {
				$errors[] = 'Invalid phone number format. ###-###-#### or ##########.';
			}
			
						
			//Timezone
			if (empty($_POST['timezone'])){
				$errors[] = 'Please select your time zone.';
			} else {
				$tzone = mysqli_real_escape_string($dbc, trim($_POST['timezone']));
			}
			
			// VFGID AND TYPE - IF TEMPORARY
			$vfgrepid = 'xxx';
			$vfgrepid_changed = false;
			if (isset($_POST['vfgrepid'])){
			
				if (empty($_POST['vfgrepid'])){
					$errors[] = 'Please enter your VFG Rep ID.';
				} else {
					$vfgrepid = strip_tags(trim(strtoupper($_POST['vfgrepid'])));
					
					if ($vfgrepid != $_POST['ogvfgrepid']){		//run this ONLY if the VFG ID has changed from what is loaded
					
						//IS VFGREPID ALREADY IN ANOTHER REPS RECORD?
						$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$vfgrepid' AND rep_id <> '$repsid' LIMIT 1 ";
						//RUN QUERY
						$rs = @mysqli_query ($dbc, $query);
						if (mysqli_num_rows($rs) == 1) {
							// vfgid exists for another rep
							$errors[] = 'VFG ID already exists on another rep\'s record.';
						} else {
							$vfgrepid_changed = true;
						}
						mysqli_free_result($rs);
					}
				}
			}	//end isset $_POST['vfgrepid']
			
			// VFG TYPE - IF SET
			$vfgrepidtype = 'xxx';
			if (isset($_POST['vfg_type'])){
				$vfgrepidtype = $_POST['vfg_type'];
			}
			
			$recruiter_vfgrepid = '';
			//Recruiter's VFG Rep ID
			if (empty($_POST['recruiter_vfgrepid'])){
				//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
			} elseif ( strtoupper($_POST['recruiter_vfgrepid']) == $repsvfgid ){
				$errors[] = 'Can\'t enter yourself as your own recruiter.';
			} else {
				$recruiter_vfgrepid = strip_tags(trim(strtoupper($_POST['recruiter_vfgrepid'])));

				if ($recruiter_vfgrepid != $_POST['ogrecruitervfgid']){
					//MAKE SURE RECRUITER VFGREPID EXISTS!
					$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$recruiter_vfgrepid' LIMIT 1";
					//RUN QUERY
					$rs = @mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) != 1) {
						//email already exists!
						$errors[] = 'Recruiter\'s VFG ID doesn\'t exist in our records.  Make sure you entered it correctly.';
					}
					mysqli_free_result($rs);
				}
			}
			
			$gmail_acct = '';
			//GMAIL email 
			if (empty($_POST['gmail_acct'])){
				//$errors[] = 'Please enter your email.';
			} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['gmail_acct']))) {
				$errors[] = 'Gmail address not in proper email format.';
			} else {
				$gmail_acct = mysqli_real_escape_string($dbc, strip_tags(strtolower(trim($_POST['gmail_acct']))));
				
				/*list($addr, $box) = explode('@',$gmail_acct);
				if ($box != 'gmail.com'){
					$errors[] = 'Gmail account entered is not a gmail address.';
				}*/
				
				if ($gmail_acct != $_POST['oggmail']){
					//IS GMAIL ALREADY IN ANOTHER'S RECORD?
					$query = "SELECT gmail_acct 
							  FROM reps 
							  WHERE gmail_acct = '$gmail_acct' AND rep_id <> '$repsid' LIMIT 1";
					//RUN QUERY
					$rs = @mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) == 1) {
						//email already exists!
						$errors[] = 'Gmail address already exists in another\'s record.';
					}
					mysqli_free_result($rs);
				}
			}
			
			//Gmail Password
			$gmail_pass = '';
			if (empty($_POST['gmail_pass'])){
				//$errors[] = 'Please select your time zone.';
			} else {
				$gmail_pass = mysqli_real_escape_string($dbc, trim($_POST['gmail_pass']));
			}
			
			//unique_id FOR DIALER....
			/*
				 elseif (!preg_match("/^\d{13,15}$/", trim($_POST['unique_id']))) {
				$errors[] = 'Dialer Unique ID invalid format. 13 to 15 digit number only';
			*/
			$uid = '';
			if(empty($_POST['unique_id'])) { 
				   //$errors[] = 'Please Enter Your Unique Id.';
			} else {
				   $uid = mysqli_real_escape_string($dbc, trim($_POST['unique_id']));
				   if ($uid != $_POST['oguniqueid']){
					//IS UNIQUE_ID ALREADY IN ANOTHER'S RECORD?
					$query = "SELECT unique_id 
							  FROM reps 
							  WHERE unique_id = '$uid' AND rep_id <> '$repsid' LIMIT 1";
					//RUN QUERY
					$rs = @mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) == 1) {
						//email already exists!
						$errors[] = 'Mobile phone unique id already exists in another rep\'s record.';
					}
					mysqli_free_result($rs);
				}
			}
			
			//Event Levels
			$event_inviter = '';
			if (empty($_POST['eventlevel_inviter'])){
				$event_inviter = 'N';
			} else {
				$event_inviter = $_POST['eventlevel_inviter'];
			}
			$event_manager = '';
			if (empty($_POST['eventlevel_manager'])){
				$event_manager = 'N';
			} else {
				$event_manager = $_POST['eventlevel_manager'];
			}
			$event_consultant = '';
			if (empty($_POST['eventlevel_consultant'])){
				$event_consultant = 'N';
			} else {
				$event_consultant = $_POST['eventlevel_consultant'];
			}
			
			$email_bcc = $_POST['email_bcc'];
			
			//View Stats 1
			$view_stats1 = '';
			if ( empty($_POST['view_stats1'])){
				//$errors[] = 'Please enter your recruiter\'s VFG ID.';
			} elseif ( strtoupper($_POST['view_stats1']) == $repsvfgid ){
				$errors[] = 'No need to enter yourself in "View Stats 1". You can view your stats.';
			} else {
				$view_stats1 = mysqli_real_escape_string($dbc, strip_tags(trim(strtoupper($_POST['view_stats1']))));

				if ($view_stats1 != $_POST['ogvs1']){
					//MAKE SURE VFG ID EXISTS
					$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$view_stats1' LIMIT 1";
					//RUN QUERY
					$rs = mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) != 1) {
						//email already exists!
						$errors[] = 'VFG ID entered in "View Stats 1" doesn\'t exist in our records.  Make sure you entered it correctly.';
					}
					mysqli_free_result($rs);
				}
			}
			
			//View Stats 2
			$view_stats2 = '';
			if ( empty($_POST['view_stats2'])){
				//$errors[] = 'Please enter your recruiter\'s VFG ID.';
			} elseif ( strtoupper($_POST['view_stats2']) == $repsvfgid ){
				$errors[] = 'No need to enter yourself in "View Stats 2". You can view your stats.';
			}  else {
				$view_stats2 = mysqli_real_escape_string($dbc, strip_tags(trim(strtoupper($_POST['view_stats2']))));

				if ($view_stats2 != $_POST['ogvs2']){
					//MAKE SURE VFG ID EXISTS
					$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$view_stats2' LIMIT 1";
					//RUN QUERY
					$rs = mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) != 1) {
						//email already exists!
						$errors[] = 'VFG ID entered in "View Stats 2" doesn\'t exist in our records.  Make sure you entered it correctly.';
					}
					mysqli_free_result($rs);
				}
			}
			
			//View Stats 3
			$view_stats3 = '';
			if ( empty($_POST['view_stats3'])){
				//$errors[] = 'Please enter your recruiter\'s VFG ID.';
			} elseif ( strtoupper($_POST['view_stats3']) == $repsvfgid ){
				$errors[] = 'No need to enter yourself in "View Stats 3". You can view your stats.';
			} else {
				$view_stats3 = mysqli_real_escape_string($dbc, strip_tags(trim(strtoupper($_POST['view_stats3']))));

				if ($view_stats3 != $_POST['ogvs3']){
					//MAKE SURE WFG ID EXISTS
					$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$view_stats3' LIMIT 1";
					//RUN QUERY
					$rs = mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) != 1) {
						//email already exists!
						$errors[] = 'VFG ID entered in "View Stats 3" doesn\'t exist in our records.  Make sure you entered it correctly.';
					}
					mysqli_free_result($rs);
				}
			}
			
			//Team Stats ID
			$team_stats_id = '';
			if (empty($_POST['team_stats_id'])){
				//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
			}  elseif (!preg_match("/^\d{4,5}$/", trim($_POST['team_stats_id']))) {
				$errors[] = 'Team Stats ID invalid format.  4 or 5 Digit number only';
			} else {
				$team_stats_id = mysqli_real_escape_string($dbc,strip_tags(trim($_POST['team_stats_id'])));
			}
			
			//Homepage Link
			$homepage_link = '';
			if (empty($_POST['homepage_link'])){
				//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
			}  else {
				$homepage_link = mysqli_real_escape_string($dbc,strip_tags(trim($_POST['homepage_link'])));
			}
			//*************** END FORM FIELD VALIDATION ***********************
			
			if (empty($errors)){
				
				$data_changed = false;
				//checks to see if data has changed from original state
				if ($first != $_POST['ogfirstname']){
					$data_changed = true;
				}
				if ($last != $_POST['oglastname']){
					$data_changed = true;
				}
				/*
				if ($email != $_POST['ogemail']){
					$data_changed = true;
				}
				*/
				if ($phone != $_POST['ogphone']){
					$data_changed = true;
				}
				if ($tzone != $_POST['ogtimezone']){
					$data_changed = true;
				}
				if ($vfgrepid != 'xxx'){
					if ($vfgrepid != $_POST['ogvfgrepid']){
						$data_changed = true;
					}
				}
				if ($vfgrepidtype != 'xxx'){
					if ($vfgrepidtype != $_POST['ogvfgtype']){
						$data_changed = true;
					}
				}
				if ($recruiter_vfgrepid != $_POST['ogrecruitervfgid']){
					$data_changed = true;
				}
				if ($gmail_acct != $_POST['oggmail']){
					$data_changed = true;
				}
				if ($gmail_pass != $_POST['oggmailpass']){
					$data_changed = true;
				}
				if($uid != $_POST['oguniqueid']) {
					$data_changed = true;
				}
				if ($event_inviter != $_POST['ogevtinviter']){
					$data_changed = true;
				}
				if ($event_manager != $_POST['ogevtmanager']){
					$data_changed = true;
				}
				if ($event_consultant != $_POST['ogevtconsultant']){
					$data_changed = true;
				}
				if ($email_bcc != $_POST['ogbcc']){
					$data_changed = true;
				}
				if ($view_stats1 != $_POST['ogvs1']){
					$data_changed = true;
				}
				if ($view_stats2 != $_POST['ogvs2']){
					$data_changed = true;
				}
				if ($view_stats3 != $_POST['ogvs3']){
					$data_changed = true;
				}
				if ($team_stats_id != $_POST['ogteamstatsid']){
					$data_changed = true;
				}
				if ($homepage_link != $_POST['oghomepagelink']){
					$data_changed = true;
				}
				//update reps table
				if ($data_changed) {
					$updatesql = "UPDATE reps 
								  SET firstname = '$first', 
									  lastname = '$last', 
									  phone ='$phone',
									  rep_timezone ='$tzone', ";
					if ($vfgrepid != 'xxx'){
						$updatesql .= "vfgrepid = '$vfgrepid', ";
					}
					if ($vfgrepidtype != 'xxx'){
						$updatesql .= "vfgrepid_type = '$vfgrepidtype', ";
					}
					$updatesql .= "recruiter_vfgid = '$recruiter_vfgrepid',
									  gmail_acct = '$gmail_acct',
									  gmail_pass = '$gmail_pass',
									  eventlevel_inviter = '$event_inviter',
									  eventlevel_manager = '$event_manager',
									  eventlevel_consultant = '$event_consultant',
									  unique_id = '$uid',
									  email_bcc = '$email_bcc',
									  view_stats1 = '$view_stats1',
									  view_stats2 = '$view_stats2',
									  view_stats3 = '$view_stats3',
									  team_stats_id = '$team_stats_id',
									  homepage_link = '$homepage_link'
								  WHERE rep_id = '$repsid' LIMIT 1"; 
					//RUN UPDATE QUERY
					$rs = mysqli_query($dbc, $updatesql);
					
					if (mysqli_affected_rows($dbc) == 1){
						$messages[] = 'Rep data update complete.';	
					} //end mysqli_affected_rows == 1
					
					// UPDATE THE rep_login_id table if the vfgrepid field has changed
					if ($vfgrepid_changed){
						$updvfgrepid_sql = "UPDATE rep_login_id
												SET vfgid = '$vfgrepid'
											WHERE rep_id = '$repsid' LIMIT 1";
						$r = mysqli_query($dbc, $updvfgrepid_sql);
						if (mysqli_affected_rows($dbc) == 1){
							$messages[] = 'Your VFG ID has been updated. Use your new VFG ID the next time you log in.';	
						} //end mysqli_affected_rows == 1
					}
				} else {
					$messages[] = 'No data change.';
					
				}	//end data_changed
			
			} 
			
		}  //close ISSET SUBMITTED
		//*****************************************************************************************
		
		// DISPLAY SQL

		$fn = $ln = $em = $ph = $tz = $vfg = $vfgtype = $rvfg = $gmail = $gpass = $u_id = $bcc ='';
		$vs1 = $vs2 = $vs3 = $ts_id = $h_link = '';
		//pull data for display
		$q = "SELECT firstname, lastname, 
					 phone, rep_timezone, vfgrepid, vfgrepid_type, 
					 recruiter_vfgid, gmail_acct, gmail_pass, 
					 eventlevel_inviter, eventlevel_manager,
					 eventlevel_consultant, eventlevel_svp, unique_id, email_bcc, 
					 view_stats1, view_stats2,
					 view_stats3, team_stats_id, homepage_link
			  FROM reps
			  WHERE rep_id = '$repsid' LIMIT 1";
		$rs = mysqli_query ($dbc, $q);
		if ($rs){
			if (mysqli_num_rows($rs) == 1) {
				while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
					$fn = $row['firstname'];
					$ln = $row['lastname'];
					//$em = $row['email'];
					$ph = $row['phone'];
					$tz = $row['rep_timezone'];
					$vfg = $row['vfgrepid'];
					$vfgtype = $row['vfgrepid_type'];
					$rvfg = $row['recruiter_vfgid'];
					$gmail = $row['gmail_acct'];
					$gpass = $row['gmail_pass'];
					//calendar event levels
					$evl_inviter = $row['eventlevel_inviter'];
					$evl_manager = $row['eventlevel_manager'];
					$evl_consultant = $row['eventlevel_consultant'];
					$u_id = $row['unique_id'];
					$bcc = $row['email_bcc'];
					$vs1 = $row['view_stats1'];
					$vs2 = $row['view_stats2'];
					$vs3 = $row['view_stats3'];
					$ts_id = $row['team_stats_id'];
					$h_link = $row['homepage_link'];
					
					//Update timezone setting if changed because 
					//they haven't logged out to refresh this
					$_SESSION['rep_tz'] = $row['rep_timezone'];
					$_SESSION['rep_phone'] = $row['phone'];
					//$_SESSION['rep_email'] = $row['email'];
					$_SESSION['vfgrep_id'] = $row['vfgrepid'];
					$_SESSION['rep_gmail'] = $row['gmail_acct'];
					$_SESSION['rep_gpass'] = $row['gmail_pass'];
					$_SESSION['rep_firstname'] = $row['firstname'];
					$_SESSION['rep_lastname'] = $row['lastname'];
					$_SESSION['rep_bcc'] = $row['email_bcc'];
					
					//Team Stats Id
					if ($ts_id != ''){
						$_SESSION['team_stats_id'] = $row['team_stats_id'];
					} else {
						unset($_SESSION['team_stats_id']);
					}
					
					//Dialer Unique ID
					if (isset($_SESSION['unique_id'])){
					    if($_SESSION['unique_id'] != $row['unique_id'] ) {
							$_SESSION['unique_id'] = $row['unique_id'];
					    }
					} else {	//unique_id has never been set
						if ($u_id != ''){
							$_SESSION['unique_id'] = $u_id;
						}
					}
					
					//Homepage Link
					if ($h_link != ''){
						$_SESSION['homepage_link'] = $row['homepage_link'];
					} else {
						unset($_SESSION['homepage_link']);
					}
					
				}
			}
			mysqli_free_result($rs);
		}
		//CLOSE connection
		mysqli_close($dbc);	  
		
		
	  ?>
	  <!-- Agent Data Edit Form -->
		<div class="rep_signup_container roundcorners opacity80">
			<h2>Agent Data Edit</h2>
			<div class="webform">
				<form name="rep_edit_profile" id="rep_edit_profile" action="rep_edit_profile.php" method="POST" >
					<ul>
						<li>
							<label for="first_name">First Name:</label>
							<input type="text" id="first_name" name="first_name" class="input200 capitalwords" maxlength="30"
								value="<?php if (isset($_POST['first_name'])){ echo $_POST['first_name']; } else { echo $fn; } ?>" />
						</li>
						<li>
							<label for="last_name">Last Name:</label>
							<input type="text" id="last_name" name="last_name" class="input200 capitalwords" maxlength="45"  
								value="<?php if (isset($_POST['last_name'])){ echo $_POST['last_name']; } else { echo $ln; } ?>" />
						</li>
						<li>
							<label for="gmail_acct">Your Gmail Acct:</label>
							<input type="text" id="gmail_acct" name="gmail_acct" class="input175" maxlength="80"  
								value="<?php if (isset($_POST['gmail_acct'])){ echo $_POST['gmail_acct']; } else { echo $gmail; } ?>" />
						</li>
						<li>
							<label for="gmail_pass">Gmail Password:</label>
							<input type="password" id="gmail_pass" name="gmail_pass" class="input150" maxlength="40"  
								value="<?php if (isset($_POST['gmail_pass'])){ echo $_POST['gmail_pass']; } else { echo $gpass; } ?>" />
						</li>
						<li>
							<label for="email_bcc">My Emails: BCC Me?</label>
							<?php
								if (isset($_POST['email_bcc'])){ 
									$selected_bcc = $_POST['email_bcc'];
								} else { 
									$selected_bcc = $bcc;
								}
							?>
							<select id="email_bcc" name="email_bcc">
								<?php
									foreach($yes_or_no as $id=>$name){
										if($selected_bcc == $id){
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
							<label>Phone:</label>
							<input type="text" name="phone" id="phone" class="input125"
									value="<?php if (isset($_POST['phone'])){ echo $_POST['phone']; } else { echo $ph; } ?>" />
						</li>
						
						<li>
							<label for="timezone">Your Timezone:</label>
							<?php
								if (isset($_POST['timezone'])){ 
									$selected_tz = $_POST['timezone'];
								} else { 
									$selected_tz = $tz;
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
							<label for="vfgrepid">Rep ID:</label>
							<?php if ($vfgtype == 'T'){ ?>
									<input type="text" id="vfgrepid" name="vfgrepid" class="input125" maxlength="20"  
											value="<?php echo $vfg; ?>" />
							<?php } else { ?>
									<label class="displayonly"><?php echo $vfg; ?></label>
							<?php } ?>
							<!--<input type="text" id="vfgrepid" name="vfgrepid" class="input125" maxlength="20"  
								value="" />-->
							<!--<label class="displayonly"></label>-->
						</li>
						<!-- VFG ID TYPE DROPDOWN, IF NECESSARY -->
						<?php if ($vfgtype == 'T'){ ?>
								<li>
									<label for="vfg_type">VFG ID Type:</label>
									<select id="vfg_type" name="vfg_type">
										<?php
											foreach($vfgid_type as $id=>$name){
												if($vfgtype == $id){
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
						<?php } ?>
						
						<li>
							<label for="recruiter_vfgrepid">Recruiter ID:</label>
							<input type="text" id="recruiter_vfgrepid" name="recruiter_vfgrepid" class="input125" maxlength="20"  
								value="<?php if (isset($_POST['recruiter_vfgrepid'])){ echo $_POST['recruiter_vfgrepid']; } else { echo $rvfg; } ?>" />
						</li>
						
					</ul>
					<span>Optional: Designate who else can view your statistics (ie: Your team leader, mentor in the business, etc). Enter their VFG IDs.</span>
					<ul>
						<li>
							<label for="view_stats1">View Stats 1:</label>
							<input type="text" id="view_stats1" name="view_stats1" class="input125" maxlength="7"  
								value="<?php if (isset($_POST['view_stats1'])){ echo $_POST['view_stats1']; } else { echo $vs1; } ?>" />
						</li>
						<li>
							<label for="view_stats2">View Stats 2:</label>
							<input type="text" id="view_stats2" name="view_stats2" class="input125" maxlength="7"  
								value="<?php if (isset($_POST['view_stats2'])){ echo $_POST['view_stats2']; } else { echo $vs2; } ?>" />
						</li>
						<li>
							<label for="view_stats3">View Stats 3:</label>
							<input type="text" id="view_stats3" name="view_stats3" class="input125" maxlength="7"  
								value="<?php if (isset($_POST['view_stats3'])){ echo $_POST['view_stats3']; } else { echo $vs3; } ?>" />
						</li>
					</ul>
					<span>Optional: This field is used to display stats for a team.  Enter the team number here if you are a part of one.</span>
					<ul>
						<li>
							<label for="team_stats_id">Team Stats ID:</label>
							<input type="text" id="team_stats_id" name="team_stats_id" class="input100" maxlength="5"  
								value="<?php if (isset($_POST['team_stats_id'])){ echo $_POST['team_stats_id']; } else { echo $ts_id; }  ?>" />
						</li>
					</ul>
					<span>Enter the URL for your VFG main landing page.  The link to the page where you want to direct your Leads.</span>
					<ul>
						<li>
							<label for="homepage_link">VFG Homepage Link:</label>
							http://<input type="text" id="homepage_link" name="homepage_link" class="input250" maxlength="50"  
								value="<?php if (isset($_POST['homepage_link'])){ echo $_POST['homepage_link']; } else { echo $h_link; }  ?>" />
						</li>
					</ul>
					<!--<span>We are adding functionality that will allow you to see company-wide calendar events for reps at the following
						  levels.  Choose which levels you wish to view on your calendar.  This will be very useful for you to learn the
						  business.</span> -->
					<ul>
						<li>
							<?php
								//set the checked values for each event level...
								$inviter_checked = $manager_checked = $consultant_checked = '';
								if ($evl_inviter == 'Y') {
									$inviter_checked = ' checked="checked" ';
								}
								if ($evl_manager == 'Y') {
									$manager_checked = ' checked="checked" ';
								}
								if ($evl_consultant == 'Y') {
									$consultant_checked = ' checked="checked" ';
								}
							?>
							<label>VFG Position:</label>
							<input type="checkbox" name="eventlevel_inviter" value="Y" <?php echo $inviter_checked; ?>>Inviter&nbsp;
							<input type="checkbox" name="eventlevel_manager" value="Y" <?php echo $manager_checked; ?>>Manager&nbsp;
							<input type="checkbox" name="eventlevel_consultant" value="Y" <?php echo $consultant_checked; ?>>Consultant
						</li>
						<li>
							<label for="unique_id">Unique ID:</label>
							<input type="text" id="unique_id" name="unique_id" class="input200" maxlength="100"  
								value="<?php if (isset($_POST['unique_id'])){ echo $_POST['unique_id']; } else { echo $u_id; }  ?>" />
						</li>
						<li>
							<input type="hidden" name="submitted" value="editdata" />
							<input type="hidden" name="ogfirstname" value="<?php echo $fn; ?>" />
							<input type="hidden" name="oglastname" value="<?php echo $ln; ?>" />
							<!--<input type="hidden" name="ogemail" value="" />-->
							<input type="hidden" name="ogphone" value="<?php echo $ph; ?>" />
							<input type="hidden" name="ogtimezone" value="<?php echo $tz; ?>" />
							<input type="hidden" name="ogvfgrepid" value="<?php echo $vfg; ?>" />
							<input type="hidden" name="ogvfgtype" value="<?php echo $vfgtype; ?>" />
							<input type="hidden" name="ogrecruitervfgid" value="<?php echo $rvfg; ?>" />
							<input type="hidden" name="oggmail" value="<?php echo $gmail; ?>" />
							<input type="hidden" name="oggmailpass" value="<?php echo $gpass; ?>" />
							<input type="hidden" name="ogevtinviter" value="<?php echo $evl_inviter; ?>" />
							<input type="hidden" name="ogevtmanager" value="<?php echo $evl_manager; ?>" />
							<input type="hidden" name="ogevtconsultant" value="<?php echo $evl_consultant; ?>" />
							<input type="hidden" name="oguniqueid" value="<?php echo $u_id; ?>" />
							<input type="hidden" name="ogbcc" value="<?php echo $bcc; ?>" />
							<input type="hidden" name="ogvs1" value="<?php echo $vs1; ?>" />
							<input type="hidden" name="ogvs2" value="<?php echo $vs2; ?>" />
							<input type="hidden" name="ogvs3" value="<?php echo $vs3; ?>" />
							<input type="hidden" name="ogteamstatsid" value="<?php echo $ts_id; ?>" />
							<input type="hidden" name="oghomepagelink" value="<?php echo $h_link; ?>" />
							<input type="submit" class="button" value="Update" />
						</li>
					</ul>
				</form>
			</div>
		</div>
		<div id="rep_signup_messages" class="roundcorners opacity95">
			<?php
				//Display error messages, if any.
				if (!empty($errors)) {
					echo 'ERROR:<br />';
					foreach ($errors as $msg) {
						echo " - $msg<br />\n";
					}
				}
				//Display script messages, if any.
				if (!empty($messages)) {
					foreach ($messages as $msg) {
						echo "$msg<br />\n";
					}
				}
			?>
		</div>
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>