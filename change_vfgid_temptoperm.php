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
<title>VFG Contact - Duplicate Gmail</title>
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
	  	
	  <?php
		include ('./includes/selectlists.php');
		include ('./includes/config.inc.php');
		//DB Connection
		require_once (MYSQL);
		$thisgmail = $_SESSION['duplicate_gmail'];
		$rid = $_SESSION['rid'];
		
		//check form submission
		if (isset($_POST['submitted']) && $_POST['submitted'] == "changeid"){
		
			$errors = array();		// initialize an error array
		
			//VALIDATE FIELDS
			//***********************************************************************
			// PERMVFGID
			if (empty($_POST['permvfgid'])){
				$errors[] = 'Please enter your Permanent VFG ID.';
			} else {
				$permvfgid = strip_tags(trim(strtoupper($_POST['permvfgid'])));

				//IS VFGREPID ALREADY IN DB?
				$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$permvfgid'";
				//RUN QUERY
				$rs = @mysqli_query ($dbc, $query);
				if (mysqli_num_rows($rs) == 1) {
					//email already exists!
					$errors[] = 'VFG ID already exists in another reps record.';
				}
				mysqli_free_result($rs);
			}
			//***** END VALIDATION **************************************************
			
			if (empty($errors)){
			
				$updatesql = "UPDATE reps 
								SET vfgrepid = '$permvfgid', 
									vfgrepid_type = 'P'
							  WHERE rep_id = '$rid' LIMIT 1";
							  
				//RUN UPDATE QUERY
				$rs = mysqli_query($dbc, $updatesql);
				
				if (mysqli_affected_rows($dbc) == 1){
					$messages[] = 'Your VFG ID has been updated.';	
				} //end mysqli_affected_rows == 1
				
				// UPDATE THE rep_login_id table if the vfgrepid field has changed
				$updvfgrepid_sql = "UPDATE rep_login_id
										SET vfgid = '$permvfgid'
									WHERE rep_id = '$rid' LIMIT 1";
				$r = mysqli_query($dbc, $updvfgrepid_sql);
				if (mysqli_affected_rows($dbc) == 1){
					$messages[] = 'Use your new VFG ID the next time you log in.';	
				} //end mysqli_affected_rows == 1
				
			
			}	//END empty errors
			
		}	//close isset $_POST['submitted']
		
		$fn = $ln = $phn = $vfgid = $vfgtype = $tz = $gmailacct = '';
		
		$q = "SELECT firstname, lastname, 
					 phone, rep_timezone, 
					 vfgrepid, vfgrepid_type, gmail_acct
			  FROM reps
			  WHERE gmail_acct = '$thisgmail' LIMIT 1";
		$rs = mysqli_query ($dbc, $q);
		if ($rs){
			if (mysqli_num_rows($rs) == 1) {
				while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
					$fn = $row['firstname'];
					$ln = $row['lastname'];
					//$em = $row['email'];
					$ph = $row['phone'];
					$tz = $row['rep_timezone'];
					$vfgid = $row['vfgrepid'];
					$vfgtype = $row['vfgrepid_type'];
					$gmail = $row['gmail_acct'];
				}
			}
			mysqli_free_result($rs);
		}
		//CLOSE connection
		mysqli_close($dbc);	
		
	  ?>
	  
	  <!-- Registration Form -->
	  <div class="rep_signup_container roundcorners opacity95">
			<h2>Provide your Permanent VFG ID</h2>
			<div class="webform">
				<form name="rep_changeid" id="rep_changeid" action="change_vfgid_temptoperm.php" method="POST" >
					<span><?php echo $fn.', '; ?>below you will find your data once again.  In the 'Permanent VFG ID' field - please type your new VFG ID to update your record.</span>
					<ul>
						<li>
							<label for="first_name">First Name:</label>
							<input type="text" id="first_name" name="first_name" class="input200"
								value="<?php echo $fn; ?>" readonly />
						</li>
						<li>
							<label for="last_name">Last Name:</label>
							<input type="text" id="last_name" name="last_name" class="input200"  
								value="<?php echo $ln; ?>" readonly />
						</li>
						<li>
							<label>Phone:</label>
							<input type="text" name="phone" id="phone" class="input125"
										value="<?php echo $ph; ?>" readonly />
						</li>
						
						<li>
							<label for="timezone">Your Timezone:</label>
							<input type="text" name="timezone" id="timezone" class="input150"
										value="<?php echo $tz; ?>" readonly />
						</li>
						<li>
							<label for="tempvfgid">Temporary VFG ID:</label>
							<input type="text" id="tempvfgid" name="tempvfgid" class="input100"   
								value="<?php echo $vfgid; ?>" readonly />
						</li>
						<li>
							<label for="permvfgid">Permanent VFG ID:</label>
							<input type="text" id="permvfgid" name="permvfgid" class="input100" maxlength=6   
								value="<?php if (isset($_POST['permvfgid'])) echo $_POST['permvfgid']; ?>" />
						</li>
						<li>
							<label for="gmail_acct">Your Gmail Acct:</label>
							<input type="text" id="gmail_acct" name="gmail_acct" class="input225"
								value="<?php echo $gmail; ?>" readonly />
						</li>
						<li>
							<input type="hidden" name="submitted" value="changeid" />
							<input type="submit" class="button" value="Update VFG ID." />
						</li>
					</ul>
				</form>
			</div> <!-- close webform -->
		</div> <!-- close formbox -->
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
		
		<div class="cleardiv"></div>
				
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>