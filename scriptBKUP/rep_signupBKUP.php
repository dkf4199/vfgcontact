<!DOCTYPE html>
<html>

<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<title>VFG Contacts - Agent Login</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>

</head>

<body style="background-image:url(images/tcs.jpg);">
<div id="wrapper">
	
	<?php include('includes/html/newheader_nolog.html'); ?>
	
	<div class="maincontent">
	  <!-- jQuery menu -->
	  <?php //include('includes/html/vfg_header_nolog.html'); ?>
	
	  <?php
		include ('includes/selectlists.php');
		include ('./includes/config.inc.php');
		
		//check form submission
		if (isset($_POST['submitted']) && $_POST['submitted'] == "register"){
			
			//DB Connection
			require_once (MYSQL);
			$errors = array();		//initialize an error array

			//VALIDATE FIELDS
			//*******************************************************
			//FIRST NAME
			if (empty($_POST['first_name'])){
				$errors[] = 'Please enter your first name.';
			} elseif (!preg_match("/^[A-Za-z]+$/", trim($_POST['first_name']))) {
				$errors[] = 'Your first name contains at least 1 invalid character.';
			} else {
				$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
				$fn = ucwords(strtolower($fname));
			}
			
			//LAST NAME
			if (empty($_POST['last_name'])){
				$errors[] = 'Please enter your last name.';
			} elseif (!preg_match("/^[A-Za-z -]+$/", trim($_POST['last_name']))) {
				$errors[] = 'Your last name contains at least 1 invalid character.';
			} else {
				$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
				$ln = ucwords(strtolower($lname));
			}
			
			//VFGREPID
			if (empty($_POST['vfgrepid'])){
				$errors[] = 'Please enter your VFG Rep ID.';
			} else {
				$vfgrepid = strip_tags(trim(strtoupper($_POST['vfgrepid'])));

				//IS VFGREPID ALREADY IN DB?
				$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$vfgrepid'";
				//RUN QUERY
				$rs = @mysqli_query ($dbc, $query);
				if (mysqli_num_rows($rs) == 1) {
					//email already exists!
					$errors[] = 'VFG Rep ID already exists in database.';
				}
				mysqli_free_result($rs);
			}
			
			//PASSWORDS
			$pass_set = $confirmpass_set = false;
			//Password
			if (empty($_POST['password'])){
				$errors[] = 'Please create a password.';
			} else {
				$passwrd = mysqli_real_escape_string($dbc, trim($_POST['password']));
				$pass_set = true;
			}
			//Confirm Password
			if (empty($_POST['confirmpassword'])){
				$errors[] = 'Please confirm your password.';
			} else {
				$confirmpass = mysqli_real_escape_string($dbc, trim($_POST['confirmpassword']));
				$confirmpass_set = true;
			}
			
			if ( $pass_set && $confirmpass_set) {
				if ( $_POST['password'] != $_POST['confirmpassword'] ) {
					$errors[] = 'Passwords do not match. Re-enter.';
				}
			}
			
			//phone 
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
				$tz = mysqli_real_escape_string($dbc, trim($_POST['timezone']));
			}
			
			//Recruiter's VFG Rep ID
			$recruiter_vfgrepid = '';
			if (empty($_POST['recruiter_vfgrepid'])){
				//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
			} else {
				$recruiter_vfgrepid = strip_tags(trim(strtoupper($_POST['recruiter_vfgrepid'])));

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
			
			
			//GMAIL
			$gmail_acct = '';
			if (empty($_POST['gmail_acct'])){
				//$errors[] = 'Please enter your email.';
			} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['gmail_acct']))) {
				$errors[] = 'Gmail address not in proper email format.';
			} else {
				$gmail_acct = strip_tags(strtolower(trim($_POST['gmail_acct'])));
				
				/*list($addr, $box) = explode('@',$gmail_acct);
				if ($box != 'gmail.com'){
					$errors[] = 'Gmail account entered is not a gmail address.';
				}*/
				
				//IS GMAIL ALREADY IN ANOTHER'S RECORD?
				$query = "SELECT gmail_acct 
						  FROM reps 
						  WHERE gmail_acct = '$gmail_acct' LIMIT 1";
				//RUN QUERY
				$rs = @mysqli_query ($dbc, $query);
				if (mysqli_num_rows($rs) == 1) {
					//email already exists!
					$errors[] = 'Gmail address already exists in rep\'s record.';
				}
				mysqli_free_result($rs);
				
			}
			
			//Gmail Password
			$gmail_pass = '';
			if (empty($_POST['gmail_pass'])){
				$errors[] = 'Please enter password for your gmail account.';
			} else {
				$gmail_pass = mysqli_real_escape_string($dbc, trim($_POST['gmail_pass']));
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
			
			//Terms and Conditions
			$terms_agree = '';
			if (empty($_POST['terms_agree'])){
				$errors[] = 'You must agree to the Terms of Use to sign up.';
			} else {
				$terms_agree = $_POST['terms_agree'];
			}
			
			//Email BCC option
			$email_bcc = $_POST['email_bcc'];
			//*************** END FORM FIELD VALIDATION ***********************
			
			if (empty($errors)){
			
				date_default_timezone_set($tz);
				
				//Create md5 string for activation link sent in email
				//$activecode = md5(uniqid(rand(),true));
				
				//Create Unique ID for this company - 5 digit number
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
					$repid = substr($fn,0,1).substr($ln,0,1).$finalnum;
					
					//IS UNIQUEID ALREADY IN DB?
					$query = "SELECT rep_id FROM reps WHERE rep_id = '$repid'";
					//RUN QUERY
					$rs = @mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) != 1) {
						//id is unique
						$idexists = false;
					}
					mysqli_free_result($rs);
				} while ($idexists);
				
				$rightnow = date("Y-m-d H:i:s");
				
				//prepared statement - INSERT DATA into reps
				$q = "INSERT INTO reps (rep_id, firstname, lastname, phone, rep_timezone, 
										signup_date, vfgrepid, recruiter_vfgid, gmail_acct, gmail_pass,
										eventlevel_inviter, eventlevel_manager, eventlevel_consultant, 
										terms_agreed, email_bcc) 
						VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

				//prepare statement
				$stmt = mysqli_prepare($dbc, $q);

				//bind variables to statement
				mysqli_stmt_bind_param($stmt, 'sssssssssssssss', $repid, $fn, $ln, $phone, $tz, $rightnow, 
															$vfgrepid, $recruiter_vfgrepid, $gmail_acct, $gmail_pass, 
															$event_inviter, $event_manager, $event_consultant, 
															$terms_agree, $email_bcc);

				//execute query
				mysqli_stmt_execute($stmt);

				if (mysqli_stmt_affected_rows($stmt) == 1) {	//rep data insert successful

					// CLOSE rep data insert statement
					mysqli_stmt_close($stmt);
					
					//Email ME there was a new signup
					//
					//TURN ON WHEN THIS HITS PRODUCTION
					/*
					$to = "dkf4199@gmail.com";
					$sub = "EAPCRM.COM: New Company Signup.";
					$body = "New rep registered.  Here are the details.\n\n";
					$body .= "Firstname: ".$_POST['first_name']."\n";
					$body .= "Lastname: ".$_POST['last_name']."\n";
					$body .= "Email: ".$_POST['email']."\n";
										
					$body = wordwrap($body, 70);
					mail($to, $sub, $body);
					
					
					//redirect to thank you page
					require_once ('./includes/phpfunctions.php');
					$url = absolute_url('registerthankyou.php');
					//javascript redirect using window.location
					echo '<script language="Javascript">';
					echo 'window.location="' . $url . '"';
					echo '</script>';
					exit();
					*/
					/********************************************************/
					/* 07/04/2013 dkf comment out the company_login insert
								  We are going to verify company signups
								  with staff and set their username at that
								  point.
								  Move the redirect to registerthankyou.php
								  right above here for now.
					*/
					/********************************************************/
					
					$notset = 'notset';
					
					//prepared statement - INSERT DATA into rep_logins
					$lq = "INSERT INTO rep_login_id (rep_id, vfgid, password) 
						VALUES (?, ?, ?)";

					//prepare statement
					$lstmt = mysqli_prepare($dbc, $lq);
					//bind variables to statement
					mysqli_stmt_bind_param($lstmt, 'sss', $repid, $vfgrepid, $passwrd);
					//execute query
					mysqli_stmt_execute($lstmt);
					
					if (mysqli_stmt_affected_rows($lstmt) == 1) {	//login data insert successful
						
						mysqli_stmt_close($lstmt);
						
						// LAST INSERTS - rep_license table
						//prepared statement - INSERT DATA into rep_license
						$rl = "INSERT INTO rep_license (rep_id) 
							VALUES (?)";

						//prepare statement
						$rstmt = mysqli_prepare($dbc, $rl);
						//bind variables to statement
						mysqli_stmt_bind_param($rstmt, 's', $repid);
						//execute query
						mysqli_stmt_execute($rstmt);
						//IF I want to check this insert.....could do it here.
						mysqli_stmt_close($rstmt);
						
						// LAST INSERTS - daily_success_steps and ultimate_success_steps table
						//prepared statement - INSERT DATA into daily_success_steps
						$ds = "INSERT INTO daily_success_steps (rep_id) 
							VALUES (?)";

						//prepare statement
						$dstmt = mysqli_prepare($dbc, $ds);
						//bind variables to statement
						mysqli_stmt_bind_param($dstmt, 's', $repid);
						//execute query
						mysqli_stmt_execute($dstmt);
						//IF I want to check this insert.....could do it here.
						mysqli_stmt_close($dstmt);
						
						$uss = "INSERT INTO ultimate_success_steps (rep_id) 
							VALUES (?)";

						//prepare statement
						$ustmt = mysqli_prepare($dbc, $uss);
						//bind variables to statement
						mysqli_stmt_bind_param($ustmt, 's', $repid);
						//execute query
						mysqli_stmt_execute($ustmt);
						//IF I want to check this insert.....could do it here.
						mysqli_stmt_close($ustmt);
						
						mysqli_close($dbc);
						
						//redirect to thank you page
						require_once ('./includes/phpfunctions.php');
						$url = absolute_url('repsignupthankyou.php');
						//javascript redirect using window.location
						echo '<script language="Javascript">';
						echo 'window.location="' . $url . '"';
						echo '</script>';
						exit();
					
					} else {
						$errors[] = 'There was a system issue with log data.';
						mysqli_close($dbc);
					}
					
				} else {	//stmt_affected_row != 1 for base data

					$errors[] = 'There was a system issue with your data.';

					//echo '<p>' . mysqli_stmt_error($stmt) . '</p>';
					//echo '</div>';
					
					//CLOSE connection
					//mysqli_close($dbc);
				
				}	//close base data insert
				
				//echo '<div id="message_ajax">Thank you for registering with ConfirmAdLeads.  We will contact you very soon.</div>';
				//CLOSE connection
				mysqli_close($dbc);			
			
			} 
			
		} 
	  ?>
	  
	  <!-- Registration Form -->
	  <div class="formbox roundcorners opacity95">
			<h2>Enter The Following Information to Register:</h2>
			<div class="webform">
				<form name="rep_signupform" id="rep_signupform" action="rep_signup.php" method="POST" >
					<span>This contact system uses Google's Calendar and Email functions.  You must have a gmail account to use this system.</span>
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
					</ul>
					<span>Enter your VFG ID and create a password. You will use your ID and password to log into vfgcontact.com.</span>
					<ul>
						<li>
							<label for="vfgrepid">VFG Rep ID:</label>
							<input type="text" id="vfgrepid" name="vfgrepid" class="input100" maxlength="5"  
								value="<?php if (isset($_POST['vfgrepid'])) echo $_POST['vfgrepid']; ?>" />
						</li>
						<li>
							<label for="password">Login Password:</label>
							<input type="password" id="password" name="password" class="input125"  
								value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>" />
						</li>
						<li>
							<label for="confirmpassword">Confirm Password:</label>
							<input type="password" id="confirmpassword" name="confirmpassword" class="input125"  
								value="<?php if (isset($_POST['confirmpassword'])) echo $_POST['confirmpassword']; ?>"/>
						</li>
					</ul>
					<ul>
						<li>
							<label>Phone:</label>
							<input type="text" name="phone" id="phone" class="input125"
										value="<?php if (isset($_POST['phone'])) echo $_POST['phone']; ?>" />
						</li>
						
						<li>
							<label for="timezone">Your Timezone:</label>
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
						
					</ul>
					<span>Enter the person's VFG ID who recruited you.  If you don't know your direct recruiter's VFG ID, or they
					      aren't in the system, leave this field blank.</span>
					<ul>
						<li>
							<label for="recruiter_vfgrepid">Recruiter ID:</label>
							<input type="text" id="recruiter_vfgrepid" name="recruiter_vfgrepid" class="input100" maxlength="5"  
								value="<?php if (isset($_POST['recruiter_vfgrepid'])) echo $_POST['recruiter_vfgrepid']; ?>" />
						</li>
					</ul>
					<span>Provide your Gmail account credentials.  This will be the email address used for any emails you send out to your
					contacts from the vfgcontact.com system.</span>
					<ul>
						<li>
							<label for="gmail_acct">Your Gmail Acct:</label>
							<input type="text" id="gmail_acct" name="gmail_acct" class="input225" maxlength="80"  
								value="<?php if (isset($_POST['gmail_acct'])) echo $_POST['gmail_acct']; ?>" />
						</li>
						<li>
							<label for="gmail_pass">Gmail Password:</label>
							<input type="password" id="gmail_pass" name="gmail_pass" class="input150" maxlength="40"  
								value="<?php if (isset($_POST['gmail_pass'])) echo $_POST['gmail_pass']; ?>" />
						</li>
						<li>
							<label for="email_bcc">My Emails: BCC Me?</label>
							<?php
								if (isset($_POST['email_bcc'])){ 
									$selected_bcc = $_POST['email_bcc'];
								} else { 
									$selected_bcc = 'N';
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
					</ul>
					<span>Select your current VFG Position</span>
					<ul>
							<?php 
								$inviter = $manager = $consultant = $termsagree = '';
								$inviter_checked = $manager_checked = $consultant_checked = $terms_checked = '';
								if (isset($_POST['eventlevel_inviter'])){
									$inviter = $_POST['eventlevel_inviter'];
									if ($inviter == 'Y') {
										$inviter_checked = ' checked="checked" ';
									}
								}
								if (isset($_POST['eventlevel_manager'])){
									$manager = $_POST['eventlevel_manager'];
									if ($manager == 'Y') {
										$manager_checked = ' checked="checked" ';
									}
								}
								if (isset($_POST['eventlevel_consultant'])){
									$consultant = $_POST['eventlevel_consultant'];
									if ($consultant == 'Y') {
										$consultant_checked = ' checked="checked" ';
									}
								}
								if (isset($_POST['terms_agree'])){
									$termsagree = $_POST['terms_agree'];
									if ($termsagree == 'Y') {
										$terms_checked = ' checked="checked" ';
									}
								}
							?>
						<li>
							<label>VFG Position:</label>
							<input type="checkbox" name="eventlevel_inviter" value="Y" <?php echo $inviter_checked; ?>>Inviter&nbsp;
							<input type="checkbox" name="eventlevel_manager" value="Y" <?php echo $manager_checked; ?>>Manager&nbsp;
							<input type="checkbox" name="eventlevel_consultant" value="Y" <?php echo $consultant_checked; ?>>Consultant
						</li>
						<li>
							<label>Terms & Conditions:</label>
							<input type="checkbox" name="terms_agree" value="Y" <?php echo $terms_checked; ?>>
									I Agree To the <a href="termsofuse.html" target="_blank">Terms/Conditions</a>
						<li>
							<input type="hidden" name="submitted" value="register" />
							<input type="submit" class="button" value="Register" />
						</li>
					</ul>
					<div id="messages">
						<?php
							//Display and error messages
							if (!empty($errors)) {
								echo 'ERROR:<br />';
								foreach ($errors as $msg) {
									echo " - $msg<br />\n";
								}
							}
						?>
					</div>
				</form>
			</div>
		</div>
		<div class="cleardiv"></div>
				
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>