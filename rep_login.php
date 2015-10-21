<?php
session_start();
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
	<div class="cleardiv"></div>
	
	<div class="maincontent">
				
		<?php
		
			include ('./includes/phpfunctions.php');
			include ('./includes/selectlists.php');
		    include ('./includes/config.inc.php');
			
			$errors = '';
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "login"){
				
				//DB Connection
				require_once (MYSQL);
				
				// Check the login:  USER and PASS are post vars
				list ($check, $repdata) = rep_encrypt_login($dbc, $_POST['vfgrepid'], $_POST['password']);

				if ($check) { // OK! it's true
				
					//Store the HTTP_USER_AGENT
					$_SESSION['staff_agent'] = md5($_SERVER['HTTP_USER_AGENT']);
					//$data is the list returned from rep_login in phpfunctions.php
					$_SESSION['rep_pwdhash'] = $repdata['pwd'];
					$_SESSION['rep_firstname'] = $repdata['firstname'];
					$_SESSION['rep_lastname'] = $repdata['lastname'];
					$_SESSION['rep_email'] = $repdata['email'];
					$_SESSION['rep_phone'] = $repdata['phone'];
					$_SESSION['rep_tz'] = $repdata['rep_timezone'];
					$_SESSION['rep_id'] = $repdata['rep_id'];
					$_SESSION['vfgrep_id'] = $repdata['vfgrepid'];
					$_SESSION['rep_gmail'] = $repdata['gmail_acct'];
					$_SESSION['rep_gpass'] = $repdata['gmail_pass'];
					$_SESSION['replevel_manager'] = $repdata['replevel_manager'];
					$_SESSION['replevel_consultant'] = $repdata['replevel_consultant'];
					$_SESSION['replevel_svp'] = $repdata['replevel_svp'];
					$_SESSION['terms_agreed'] = $repdata['terms_agreed'];
					$_SESSION['rep_bcc'] = $repdata['email_bcc'];
					
					// TEAM STATS ID
					if(!empty($repdata['team_stats_id'])) {
					       $_SESSION['team_stats_id'] = $repdata['team_stats_id'];
					}
					
					// HOMEPAGE LINK
					if(!empty($repdata['homepage_link'])) {
					       $_SESSION['homepage_link'] = $repdata['homepage_link'];
					}
					
					// FOR DIALER.....unique_id field from reps
					if(!empty($repdata['unique_id'])) {
					       $_SESSION['unique_id'] = $repdata['unique_id'];
					}
					
					// Redirect to terms.php if terms_agreed is NOT set up yet:
					if ( $_SESSION['terms_agreed'] == 'N'){
						$url = absolute_url ('terms.php');
						//javascript redirect using window.location - CANT USE HEADER, ALREADY BEEN SENT
						echo '<script language="Javascript">';
						echo 'window.location="' . $url . '"';
						echo '</script>';
						exit(); // Quit the script.
					}
					
					// Redirect:
					$url = absolute_url ('rep_maindash.php');
					//javascript redirect using window.location - CANT USE HEADER, ALREADY BEEN SENT
					echo '<script language="Javascript">';
					echo 'window.location="' . $url . '"';
					echo '</script>';
					exit(); // Quit the script.
						
				} else { // Unsuccessful
					// Assign $data to $errors for error reporting
					$errors = $repdata;
				}
				mysqli_close($dbc); // Close the database connection.
							
			}
		?>
		<!-- signin form -->
		<div class="formbox roundcorners opacity85" >
			<h2>VFG Contacts - Rep Login</h2>
			<div class="webform">
				<form name="rep_loginform" id="rep_loginform" action="rep_login.php" method="POST" >
					<ul>
						<li>
							<label for="vfgrepid">Rep ID:</label>
							<input type="text" id="vfgrepid" name="vfgrepid" class="input125"  
								value="<?php if (isset($_POST['vfgrepid'])) echo $_POST['vfgrepid']; ?>" />
						</li>
						<li>
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" class="input125" 
								value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>" />
						</li>
						<li>
							<input type="hidden" name="submitted" value="login" />
							<input type="submit" class="button" value="Login" />
						</li>
					</ul>
					<a href="rep_pass.php" class="forgotpass">Forgot Password</a>
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
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>