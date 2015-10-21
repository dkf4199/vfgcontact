<?php
session_start();
// Include the database sessions file:
// The file starts the session.
//require('includes/db_sessions.inc.php');
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
<title>VFG Contacts - Admin Login</title>
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
	
	<?php include('includes/html/admin_header_nolog.html'); ?>
	
	<div class="maincontent">
		<?php
			include ('./includes/phpfunctions.php');
			include ('./includes/selectlists.php');
		    include ('./includes/config.inc.php');
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "login"){
				
				//DB Connection
				require_once (MYSQL);
				
				// Check the login:  USER and PASS are post vars
				list ($check, $repdata) = tcs_admin_login($dbc, $_POST['email'], $_POST['password']);

				if ($check) { // OK! it's true
				
					//Store the HTTP_USER_AGENT
					$_SESSION['staff_agent'] = md5($_SERVER['HTTP_USER_AGENT']);
					//$data is the list returned from rep_login in phpfunctions.php
					$_SESSION['admin_firstname'] = $repdata['firstname'];
					$_SESSION['admin_lastname'] = $repdata['lastname'];
					$_SESSION['admin_vfgrepid'] = $repdata['vfg_repid'];
										
					// Redirect to rep_add_vfgrepid.php if vfgrepid is NOT set up yet:
					
					// Redirect:
					$url = absolute_url ('admin_maindash.php');
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
		<div class="formbox roundcorners opacity85" >
			<h2>VFG Contacts - Admin Login</h2>
			<div class="webform">
				<form name="vfg_admin" id="vfg_admin" action="vfg_admin.php" method="POST" >
					
					<h2>Administrative Login:</h2>
					<ul>
						<li>
							<label for="email">Email:</label>
							<input type="text" id="email" name="email" class="input200"  
								value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
						</li>
						<li>
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" class="input125" />
						</li>
						<li>
							<input type="hidden" name="submitted" value="login" />
							<input type="submit" class="button" value="Login" />
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
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>