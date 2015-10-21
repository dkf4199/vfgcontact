<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<title>VFG Contact - Password Mgr</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfg_jquery_ui_menu.css" />
<script src="./js/jquery-1.9.1.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
</head>

<body style="background-image:url(images/tcs.jpg);">
<div id="wrapper">
	
	<?php include('includes/html/newheader_nolog.html'); ?>
	<div class="cleardiv"></div>

	<div class="maincontent">
		
		<?php
			include ('includes/selectlists.php');
			include ('includes/phpfunctions.php');
			include ('./includes/config.inc.php');
			
			//DB Connection
			require_once (MYSQL);
			$errors = array();
			$messages = array();
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "sendpass"){
				
				//DB Connection
				require_once (MYSQL);
				
				// Check the login:  USER and PASS are post vars
				list ($check, $getdata) = rep_forgot_pass($dbc, $_POST['email'], $_POST['vfgid']);

				if ($check) { // OK! it's true
				
					$pwd = $getdata['password'];
					
					//SEND EMAIL TO REP
					$to = trim($_POST['email']);
					$sub = "VFGCONTACT.COM: Your Password.";
					$body = "Your www.vfgcontact.com password is ".$pwd."\n";
					$from = 'support@vfgcontact.com';
					
					$body = wordwrap($body, 70);
					mail($to, $sub, $body, "From: ".$from);
					
					//echo '<div id="messages">';
					//echo '<p>Thanks for your email.  We\'ll be in contact with you shortly if there is a problem.  Thanks again.</p>';
					//echo '</div>';
					$messages[] = 'Email with your password has been sent.';

					
				} else { // Unsuccessful
					// Assign $data to $errors for error reporting
					$errors = $getdata;
				}
				mysqli_close($dbc); // Close the database connection.
				
				
			
			} //close isset($_POST['submitted'])
		
		?>
		<!-- signin form -->
		<div class="formbox roundcorners opacity85" >
			<h2>VFG Contacts - Forgot Password</h2>
			<div class="webform">
				<form name="rep_pass" id="rep_pass" action="rep_pass.php" method="POST" >
					<ul>
						<li>
							<label for="email">Gmail:</label>
							<input type="text" id="email" name="email" class="input200"  
								value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
						</li>
						<li>
							<label for="vfgid">VFG ID:</label>
							<input type="vfgid" id="vfgid" name="vfgid" class="input125" 
								value="<?php if (isset($_POST['vfgid'])) echo $_POST['vfgid']; ?>"/>
						</li>
						<li>
							<input type="hidden" name="submitted" value="sendpass" />
							<input type="submit" class="generalbutton indentbutton" value="Send My Password" />
						</li>
					</ul>
					<div id="messages">
						<?php
							//Display and error messages
							if (!empty($errors)) {
								echo 'ERROR:<br />';
								foreach ($errors as $msg) {
									echo "$msg<br />\n";
								}
							}
							if (!empty($messages)) {
								foreach ($messages as $msg) {
									echo "$msg<br />\n";
								}
							}
						?>
					</div>
				</form>
			</div>
		</div>
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>