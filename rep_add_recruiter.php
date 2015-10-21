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
<title>VFG Contact - Add Your Recruiter's ID to System</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<script src="./js/jquery1-9-1.js"></script>
<script src="./js/jquery_migrate1-1-0.js"></script>
<script>
$(document).ready(function() {
	//
});
</script>
</head>

<body>
<div class="wrapper showgoldborder">

	<?php include('includes/html/header_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		<?php
			include ('includes/selectlists.php');
			include ('includes/phpfunctions.php');
			include ('./includes/config.inc.php');
			
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "addrecruiterid"){
				$repsid = $_SESSION['rep_id'];
				//DB Connection
				require_once (MYSQL);
				$errors = array();		//initialize an error array

				//VALIDATE FIELDS
				//*******************************************************
				if (empty($_POST['recruiter_id'])){
					$errors[] = 'Please enter your recruiter\'s VFG ID.';
				} else {
					$recruiterid = strip_tags(trim(strtoupper($_POST['recruiter_id'])));
				}
				
				//phone 
				if (empty($_POST['phone'])){
					$errors[] = 'Please provide your primary phone number.';
				} elseif (!preg_match("/^\d{3}[-]?\d{3}[-]?\d{4}$/", trim($_POST['phone']))) {
					$errors[] = 'Invalid phone number format. ########## or ###-###-####.';
				} else{
					if (strlen(trim($_POST['phone'])) == 10) {	//no dashes ##########
						$formattedphone = trim($_POST['phone']);
						$phone = substr($formattedphone,0,3).'-'.substr($formattedphone,3,3).'-'.substr($formattedphone,6,4);
					}
					if (strlen(trim($_POST['phone'])) == 12) {	//dashes ###-###-####
						$phone = trim($_POST['phone']);
					}
				}				
				//*************** END FORM FIELD VALIDATION ***********************
				
				if (empty($errors)){
				
					//update
					$updatesql = "UPDATE reps
								SET recruiter_vfgid ='$recruiterid',
									phone = '$phone'
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
						echo '<div id="messages"><p>Update failed.</p></div>';
						//echo '<p>'.mysqli_error($dbc).'</p>';
					}
					
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
				
			}	//close isset
		?>
		<div class="formdiv">
				<form name="rep_recruiterid_form" id="rep_recruiterid_form" action="rep_add_recruiter.php" method="POST" >
					
					<h2>Enter Your Recruiter's VFG Rep ID, and Your Phone Number:</h2>
					<p>When done, you will be taken back to the main dashboard. Thanks.</p>
					<ul>
						<li>
							<label for="vfgid">Your Recruiter's VFG ID:<br />(WFG Number)</label>
							<input type="text" id="recruiter_id" name="recruiter_id" class="input150"  
								value="<?php if (isset($_POST['recruiter_id'])) echo $_POST['recruiter_id']; ?>" />
						</li>
						<li>
							<label>Your Phone:</label>
							<input type="text" name="phone" id="phone" class="input125"
										value="<?php if (isset($_POST['phone'])) echo $_POST['phone']; ?>" />
						</li>
						<li>
							<input type="hidden" name="submitted" value="addrecruiterid" />
							<input type="submit" class="button" value="Add ID" />
						</li>
					</ul>
				</form>
			</div>
				
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
</body>
</html>