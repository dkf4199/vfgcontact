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
		
		//check form submission
		if (isset($_POST['submitted']) && $_POST['submitted'] == "thisisme"){
		
		
		}	//close isset $_POST['submitted']
		
		$fn = $ln = $ph = $vfgid = $vfgtype = $tz = $gmailacct = '';
		$foundrec = false;
		
		$q = "SELECT rep_id, firstname, lastname, 
					 phone, rep_timezone, 
					 vfgrepid, vfgrepid_type, gmail_acct
			  FROM reps
			  WHERE gmail_acct = '$thisgmail' 
			  AND vfgrepid_type = 'T' LIMIT 1";
		$rs = mysqli_query ($dbc, $q);
		if ($rs){
			if (mysqli_num_rows($rs) == 1) {
				$foundrec = true;
				while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
					$_SESSION['rid'] = $row['rep_id'];
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
	  <div class="formbox roundcorners opacity95">
		<?php if ($foundrec){ ?>
			<h2>Is This You?</h2>
			<div class="webform">
				<form name="rep_isthisyou" id="rep_isthisyou" action="change_vfgid_temptoperm.php" method="POST" >
					<span>NOTE: You have reached this page because you entered a Gmail account that is in use on another rep's record, and
						you indicated that you had previously signed up using a Temporary VFG ID.
					<br /><br />Is the following data from the account you previously created, and do you now have your Permanent VFG ID?</span>
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
							<label for="vfgrepid">VFG ID:</label>
							<input type="text" id="vfgrepid" name="vfgrepid" class="input100"   
								value="<?php echo $vfgid; ?>" readonly />
						</li>
						<li>
							<label for="vfgtype">VFG ID Type:</label>
							<input type="text" id="vfgtype" name="vfgtype" class="input125" 
								value="<?php echo $vfgtype; ?>" readonly />
						</li>
						<li>
							<label for="gmail_acct">Your Gmail Acct:</label>
							<input type="text" id="gmail_acct" name="gmail_acct" class="input225"
								value="<?php echo $gmail; ?>" readonly />
						</li>
						<li>
							<input type="hidden" name="submitted" value="thisisme" />
							<input type="submit" class="button" value="This is me, and I have my permanent VFG ID." />
						</li>
						</form>
						<li>
							<form name="not_me" id="not_me" action="not_duplicate_gmail_record.php" method="POST">
								<input type="submit" class="button" value="No, this isn't my data." />
								</form>
						</li>
					</ul>
			</div> <!-- close webform -->
		  <?php } else { ?>
		    <div class="webform">
				<span>NOTE: The VFG ID associated with the Gmail Account you are trying to use is NOT designated as a Temporary ID.</span>
			</div>
		  <?php } ?>
		</div> <!-- close formbox -->
		
		
		<div class="cleardiv"></div>
				
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>