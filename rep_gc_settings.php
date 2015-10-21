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
//select list form element arrays
include ('includes/selectlists.php');
//include function libs
include ('includes/phpfunctions.php');
include ('includes/config.inc.php');
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
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/google_calendar.css" />
<link href="./css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<script src="./js/jquery1-9-1.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {
	//$("#gc_email").focus();
	//$("#datepicker_startdate").datepicker({ dateFormat: "mm-dd-yy" });
	//$("#datepicker_enddate").datepicker({ dateFormat: "mm-dd-yy" });
		
});	//end jquery ready
</script>
<script>
//js functions for ajax
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<!-- Header include -->
	<?php include('includes/html/header_log.html'); ?>
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		<div id="gcActions">
			<a class="googlelink" href="rep_calendar.php">Calendar</a>
		</div>
		<?php
			$repsid = $_SESSION['rep_id'];
			//DB Connection
			require_once (MYSQL);
			
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "updategcsettings"){
				
				$errors = array();		//initialize errors array
				$iomsgs = array();
				
				//VALIDATE FORM FIELDS
				//*******************************************************
				//gc_email
				if (empty($_POST['gc_email'])){
					$errors[] = 'Please enter your email.';
				} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['gc_email']))) {
					$errors[] = 'Enter a valid email address.';
				} 	else {
					$email = strip_tags(trim($_POST['gc_email']));
					
					//Check email if email changes
					if ( trim($_POST['gc_email']) != $_POST['original_email'] ) {
						//IS EMAIL ALREADY IN DB?
						$query = "SELECT gc_email FROM gc_settings 
								  WHERE gc_email = '$email'";
						//RUN QUERY
						$rs = @mysqli_query ($dbc, $query);
						if (mysqli_num_rows($rs) == 1) {
							//email already exists!
							$errors[] = 'Email address already exists in database.';
						}
						mysqli_free_result($rs);
					}
				}
				//gc_password
				if (empty($_POST['gc_pass'])){
					$errors[] = 'Please enter password for this Google account.';
				} else {
					$pass = mysqli_real_escape_string($dbc, trim($_POST['gc_pass']));
				}
				
				//*************** END FIELD VALIDATION ***********************
				date_default_timezone_set($_SESSION['rep_tz']);
				
				//*************** DB UPDATE **********************************
				if (empty($errors)){
				
					//First - determine if there is an entry for this rep
					$hasRecord = false;
					$query = "SELECT gc_email FROM gc_settings 
							  WHERE rep_id = '$repsid'";
					//RUN QUERY
					$rs = mysqli_query ($dbc, $query);
					if (mysqli_num_rows($rs) == 1) {
						//record exists
						$hasRecord = true;
					}
					mysqli_free_result($rs);
					
					
					//Do INSERT or UPDATE - Set up prepared statement
					if ($hasRecord){
						//UPDATE STMT
						$q = sprintf("UPDATE gc_settings
									  SET gc_email = '%s',
									      gc_pass = '%s'
									  WHERE rep_id = '%s'", $email, $pass, $repsid);
					} else {
						//INSERT STMT
						$q = sprintf("INSERT INTO gc_settings (rep_id, gc_email, gc_pass)
									VALUES ('%s', '%s', '%s')", $repsid, $email, $pass);
					}
					//RUN QUERY
					$rs = mysqli_query($dbc, $q);
					
					if (mysqli_affected_rows($dbc) == 1){
						if ($hasRecord){
							$iomsgs[] = 'Update Successful';
						} else {
							$iomsgs[] = 'Insert Successful';
						}
						
					} else {
					
						if ($hasRecord){
							$iomsgs[] = 'Update Failed';
						} else {
							$iomsgs[] = 'Insert failed';
						}
					} #end affected rows == 1
					
					//DISPLAY iomsgs[]
					//DISPLAY ERRORS[] ARRAY MESSAGES
					echo '<div id="messages">';
					foreach ($iomsgs as $msg){
						echo "$msg<br />\n";
					}
					echo '</div>';
					
				} else {	//Have errors
				
					//DISPLAY ERRORS[] ARRAY MESSAGES
					echo '<div id="messages">';
					echo '<p><u>ERRORS:</u><br /><br />';

					foreach ($errors as $msg){

						echo " - $msg<br />\n";
					}

					echo '</p></div>';
				}	//end have errors		
			} //end form submit

			//******************************************
			// PULL RECORD FOR DISPLAY
			//******************************************
			// Make the query:
			$q = "SELECT gc_email, gc_pass
				  FROM gc_settings
				  WHERE rep_id = '$repsid'";	
				
			$r = @mysqli_query ($dbc, $q); // Run the query.
			
			$gcemail = $gcpass = '';
			if (mysqli_num_rows($r) == 1) {
				//FETCH ROW
				$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
				$gcemail = $row['gc_email'];
				$gcpass = $row['gc_pass'];
				mysqli_free_result($r);
			}
		?>
		
		<div class="formdiv">
			<form name="gc_settings_form" id="gc_settings_form" action="rep_gc_settings.php" method="POST" >
				<ul>
					<li>
						<label>Google Email:</label>
						<input name="gc_email" id="gc_email" type="text" class="input200" 
								value="<?php echo $gcemail; ?>" />
					</li>
					<li>
						<label>Google Password:</label>
						<input name="gc_pass" id="gc_pass" type="password" value="<?php echo $gcpass; ?>" />
					</li>
					<li>
						<input type="hidden" name="submitted" value="updategcsettings" />
						<input type="hidden" name="original_email" value="<?php echo $gcemail; ?>" />
						<input name="submit" type="submit" class="button" value="Update" />
					</li>
				</ul>	
			</form>
		</div>
		
		<?php include('includes/html/footer.html'); ?>
		
	</div> <!-- close main content -->
	
	

</div>	<!-- close wrapper -->
</body>
</html>