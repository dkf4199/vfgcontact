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
<title>VFG Contacts - Rep Maindash</title>
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
			include ('./includes/phpfunctions.php');
			include ('./includes/selectlists.php');
		    include ('./includes/config.inc.php');
			
			$repmail = $_SESSION['rep_gmail'];
			
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "emailus"){
				
				//DB Connection
				require_once (MYSQL);
				
				$errors = array();		//initialize an error array
				
				//Send $_POST data thru the scrubber
				$scrubbed = array_map('spam_scrubber',$_POST);
				
				//VALIDATE FIELDS
				//from_email
				if (empty($scrubbed['from_email'])){
					$errors[] = 'Please enter your email.';
				} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($scrubbed['from_email']))) {
					$errors[] = 'Enter a valid email address.';
				} 	else {
					$fromemail = strip_tags(trim($scrubbed['from_email']));
				}
				
				//comments
				if (empty($scrubbed['comments'])){
					$errors[] = 'Comments are blank, or had bad data in them, and were scrubbed. Please re-enter.';
				} else {
					$comments = mysqli_real_escape_string($dbc, trim($scrubbed['comments']));
				}
				//*************** END FIELD VALIDATION ***********************
				
				if (empty($errors)){
					
					//Send ME the email
					$to = "support@vfgcontact.com";
					$sub = "VFGCONTACT.COM: Contact Us Email.";
					$body = $scrubbed['comments']."\n";
					$from = $scrubbed['from_email'];
					
					$body = wordwrap($body, 70);
					mail($to, $sub, $body, "From: ".$from);
					
					echo '<div id="messages">';
					echo '<p>Thanks for your email.  We\'ll be in contact with you shortly if there is a problem.  Thanks again.</p>';
					echo '</div>';
					
					unset($scrubbed);
				}
				
			}  //end isset submitted
		?>
		<!-- Add Contact Form -->
		<div class="formbox roundcorners opacity80">
			<h2>Contact Us</h2>
			<div class="webform">
				<form name="rep_contactus_form" id="rep_contactus" action="rep_contact_us.php" method="POST" >
					<ul>
						<li>
							This support page is for the Tier Manager system only.  If you have an issue with the marketing system in your backoffice,
							you need to contact the support team for them.
						</li>
						<li>
							Email us with any concerns or problems you may be experiencing with the system. Thanks
							for your input.
						</li>
						<li>
							<label for="from_email">From:</label>
							<input type="text" id="from_email" name="from_email" class="input200"  
								value="<?php echo $repmail; ?>" />
						</li>
						<li>
							<label for="comments">Comments:</label>
							<textarea id="comments" name="comments" rows="6" cols="40"><?php if (isset($scrubbed['comments'])) echo $scrubbed['comments']; ?></textarea> 		
						</li>
						
						<li>
							<input type="hidden" name="submitted" value="emailus" />
							<input type="submit" class="button" value="Send" />
						</li>
					</ul>
					<div id="messages">
						<?php
							//Display error messages, if any.
							if (!empty($errors)) {
								echo 'ERROR:<br />';
								foreach ($errors as $msg) {
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