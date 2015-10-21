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
<title>VFG Contact - Add Your VFG ID</title>
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
			
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "termsagree"){
				$repsid = $_SESSION['rep_id'];
				//DB Connection
				require_once (MYSQL);
				$errors = array();		//initialize an error array
				$iomsgs = array();		//initialize a message array

				//VALIDATE FIELDS
				//*******************************************************
				//Terms and Conditions
				$terms_agree = '';
				if (empty($_POST['terms_agree'])){
					$errors[] = 'You must agree to the Terms of Use to continue.';
				} else {
					$terms_agree = $_POST['terms_agree'];
				}
						
				//*************** END FORM FIELD VALIDATION ***********************
				
				if (empty($errors)){
				
					//update
					$updatesql = "UPDATE reps
								SET terms_agreed ='$terms_agree'
								WHERE rep_id = '$repsid'"; 

					//RUN UPDATE QUERY
					$rs= mysqli_query($dbc, $updatesql);
					
					if (mysqli_affected_rows($dbc) == 1){
						//redirect to maindash
						$url = absolute_url('rep_maindash.php');
						//javascript redirect using window.location
						echo '<script language="Javascript">';
						echo 'window.location="' . $url . '"';
						echo '</script>';
						exit();
					} else {
						$iomsgs[] = '<p>Update failed.</p>';
						//echo '<p>'.mysqli_error($dbc).'</p>';
					}
					
					mysqli_close($dbc);			
				
				}
				
			}	//close isset
		?>
		<!-- signin form -->
		<div class="formbox roundcorners opacity85" >
			<h2>VFG Contacts - Terms & Conditions</h2>
			<div class="webform">
				<form name="terms_form" id="terms_form" action="terms.php" method="POST" >
					<ul>
						<?php 
								$termsagree = '';
								$terms_checked = '';
								if (isset($_POST['terms_agree'])){
									$termsagree = $_POST['terms_agree'];
									if ($termsagree == 'Y') {
										$terms_checked = ' checked="checked" ';
									}
								}
							?>
						<li>
							<label>Terms & Conditions:</label>
							<input type="checkbox" name="terms_agree" value="Y" <?php echo $terms_checked; ?>>
									I Agree To the <a href="termsofuse.html" target="_blank">Terms/Conditions</a>
						<li>
						
						<li>
							<input type="hidden" name="submitted" value="termsagree" />
							<input type="submit" class="button" value="I Agree" />
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
							if (!empty($iomsgs)) {
								foreach ($iomsgs as $msg) {
									echo "$msg<br />\n";
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