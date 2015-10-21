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
<title>VFG Contacts - Change Pwd</title>
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
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
	
	  <?php
		include ('includes/selectlists.php');
		include ('./includes/config.inc.php');
		include ('./includes/phpfunctions.php');
		
		$repsid = $_SESSION['rep_id'];
		$repsvfgid = $_SESSION['vfgrep_id'];
		$pwd_hash = $_SESSION['rep_pwdhash'];
		
		//DB Connection
		require_once (MYSQL);
		
		$messages = array();	//initialize an error array
		$messages[] = 'Provide your current password and create your new one in the form here.';
		
		//check form submission
		if (isset($_POST['submitted']) && $_POST['submitted'] == "changepwd"){
			
			$errors = array();		//initialize error array
			$messages = array();	// re-initialize messages
			
			//VALIDATE FIELDS
			//*******************************************************
						
			$pass_set = $confirmpass_set = false;
			
			// Current Password
			if (empty($_POST['oldpass'])){
				$errors[] = 'Provide current password.';
			} else {
				$currentpass = mysqli_real_escape_string($dbc, trim($_POST['oldpass']));
				
				// Encrypt it and compare to current pwd
				$e_currentpass = crypt($currentpass, $pwd_hash);
				if ($e_currentpass != $pwd_hash){
					$errors[] = 'Incorrect current password.';
				}
			}
			
			//Password
			if (empty($_POST['newpass'])){
				$errors[] = 'Please create new password.';
			} else {
				$passwrd = mysqli_real_escape_string($dbc, trim($_POST['newpass']));
				$pass_set = true;
			}
			//Confirm Password
			if (empty($_POST['confirmnewpass'])){
				$errors[] = 'Please confirm your password.';
			} else {
				$confirmpass = mysqli_real_escape_string($dbc, trim($_POST['confirmnewpass']));
				$confirmpass_set = true;
			}
			
			// New Password Match
			if ( $pass_set && $confirmpass_set) {
				// is new pwd same as old?
				if ( $_POST['oldpass'] == $_POST['newpass'] ) {
					$errors[] = 'New Password is same as current password. Create a different new password.';
				}
				// passwords match?
				if ( $_POST['newpass'] != $_POST['confirmnewpass'] ) {
					$errors[] = 'New Passwords do not match. Re-enter.';
				}
			}
			
			//*************** END FORM FIELD VALIDATION ***********************
			
			if (empty($errors)){
				
				// Encrypt new password
				$e_newpass = better_crypt($passwrd);
				
				// Old password correct, New Password Confirmed - Make the change
				//prepared statement - INSERT DATA into rep_logins
				$lq = "UPDATE rep_login_id 
							SET password=?,
							    pwd=?
						WHERE rep_id=? ";

				//prepare statement
				$lstmt = mysqli_prepare($dbc, $lq);
				//bind variables to statement
				mysqli_stmt_bind_param($lstmt, 'sss', $passwrd, $e_newpass, $repsid );
				//execute query
				mysqli_stmt_execute($lstmt);
				
				if (mysqli_stmt_affected_rows($lstmt) == 1) {	//login data insert successful
					
					mysqli_stmt_close($lstmt);
					$messages[] = 'Password successfully changed. Use new password at next login.';
					
					// Send Email to Rep Confirming Password Change
					//
					// Turn this ON when in PRODUCTION
					//
					/*$to = $_SESSION['rep_gmail'];
					$sub = "VFGCONTACT.COM - Password Change";
					$headers = 'From: support@vfgcontact.com';
					$body = $_SESSION['rep_firstname'].",\n\n";
					$body .= "You changed your password for your vfgcontact account recently.\n";
					$body .= "Your new password is ".$passwrd.".\n\n";
					$body .= "If you have any issues, please contact us via the support page. Thank you.";
										
					$body = wordwrap($body, 70);
					mail($to, $sub, $body, $headers);
					*/
					
				} else {
					$messages[] = 'There was a system issue changing your password.';
				}
			
			} 
			
		}  //close ISSET SUBMITTED
		//*****************************************************************************************
		
		
		//CLOSE connection
		mysqli_close($dbc);	  
		
		
	  ?>
	  <!-- Change Password -->
		<div class="rep_signup_container roundcorners opacity80">
			<h2>Agent Change Password</h2>
			<div class="webform">
				<form name="rep_changepass" id="rep_changepass" action="rep_chgpass.php" method="POST" >
					<ul>
						<li>
							<label for="oldpass">Current Password:</label>
							<input type="password" id="oldpass" name="oldpass" class="input150" maxlength="40"  
								value="<?php if (isset($_POST['oldpass'])) echo $_POST['oldpass']; ?>" />
						</li>
					</ul>
					<ul>
						<li>
							<label for="newpass">New Password:</label>
							<input type="password" id="newpass" name="newpass" class="input125"  
								value="<?php if (isset($_POST['newpass'])) echo $_POST['newpass']; ?>" />
						</li>
						<li>
							<label for="confirmnewpass">Confirm New Password:</label>
							<input type="password" id="confirmnewpass" name="confirmnewpass" class="input125"  
								value="<?php if (isset($_POST['confirmnewpass'])) echo $_POST['confirmnewpass']; ?>"/>
						</li>
					
						<li>
							<input type="hidden" name="submitted" value="changepwd" />
							<input type="submit" class="button" value="Change Password" />
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