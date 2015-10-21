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
			if (isset($_POST['submitted']) && $_POST['submitted'] == "addvfgid"){
				$repsid = $_SESSION['rep_id'];
				//DB Connection
				require_once (MYSQL);
				$errors = array();		//initialize an error array
				$iomsgs = array();		//initialize a message array

				//VALIDATE FIELDS
				//*******************************************************
				//VFGREPID
				//check vfgrepid
				/*
				if (empty($_POST['vfgid'])){
					$errors[] = 'Please enter your VFG Rep ID.';
				} elseif (!preg_match("/^\d{2}[A-Z]{3}$/", trim(strtoupper($_POST['vfgid'])))) {
					$errors[] = 'Invalid VFG Rep ID format.  Format is (2digit 3letters). ie: 99XXX';
				} 	else {
					$vfgrepid = strip_tags(trim(strtoupper($_POST['vfgid'])));

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
				*/
				if (empty($_POST['vfgid'])){
					$errors[] = 'Please enter your VFG Rep ID.';
				} else {
					$vfgrepid = strip_tags(trim(strtoupper($_POST['vfgid'])));

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
				//phone 
				/*if (empty($_POST['phone'])){
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
				}*/
						
				//*************** END FORM FIELD VALIDATION ***********************
				
				if (empty($errors)){
				
					//update
					$updatesql = "UPDATE reps
								SET vfgrepid ='$vfgrepid'
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
			<h2>VFG Contacts - Rep Login</h2>
			<div class="webform">
				<form name="rep_vfgrepid_form" id="rep_vfgrepid_form" action="rep_add_vfgrepid.php" method="POST" >
					
					<h2>Enter Your VFG Rep id:</h2>
					<ul>
						<li>
							<label for="vfgid">VFG Rep ID:<br />(WFG Number)</label>
							<input type="text" id="vfgid" name="vfgid" class="input200"  
								value="<?php if (isset($_POST['vfgid'])) echo $_POST['vfgid']; ?>" />
						</li>
						
						<li>
							<input type="hidden" name="submitted" value="addvfgid" />
							<input type="submit" class="button" value="Add ID" />
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