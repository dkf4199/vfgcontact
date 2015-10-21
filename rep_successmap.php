<?php
session_start();
// Include the database sessions file:
// The file starts the session.
//require('includes/db_sessions.inc.php');
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
date_default_timezone_set($_SESSION['rep_tz']);
$repsid = $_SESSION['rep_id'];
$repsvfgid = $_SESSION['vfgrep_id'];

include ('includes/selectlists.php');
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
<title>VFG Contacts - Rep Dashboard</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">

<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>

<script>
$(document).ready(function() {

		
});
function cbChanged(checkboxElem, step) {
	var yesno = '';
	if (checkboxElem.checked) {
		// CHECKED
		//alert('Checkbox checked. Value = '+step);
		yesno = 'Y';
		
		//AJAX POST
		$.ajax({
			url:"ajax_update_dss.php",
			type: "POST",
			data: { stepnum: step,
					yesno: yesno },
			dataType: "html",		
			success:function(result){
				//$("#ajax_edit_consultant_event").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				//$("#ajax_edit_consultant_event").html(errorThrown);
			}
		}); //end $.ajax


	} else {
		// UNCHECKED
		//alert('Checkbox Unchecked. Value = '+step);
		yesno = 'N';
		//AJAX POST
		$.ajax({
			url:"ajax_update_dss.php",
			type: "POST",
			data: { stepnum: step,
					yesno: yesno },
			dataType: "html",		
			success:function(result){
				//$("#ajax_edit_consultant_event").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				//$("#ajax_edit_consultant_event").html(errorThrown);
			}
		}); //end $.ajax

	}
}
</script>

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
				//RETRIEVE DAILY SUCCESS STEPS VALUES
				//DB Connection
				require_once (MYSQL);
				
				// GET Success Steps values
				$step1 = $step2 = $step3 = $step4 = $step5 = $step6 = $step7 = '';
				$q = "SELECT step1, step2, step3, step4, step5, step6, step7
					  FROM daily_success_steps
					  WHERE rep_id = '$repsid' ";
				$r = mysqli_query($dbc, $q);
				if ($r){
					if (mysqli_num_rows($r) > 0){
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							$step1 = $row['step1'];
							$step2 = $row['step2'];
							$step3 = $row['step3'];
							$step4 = $row['step4'];
							$step5 = $row['step5'];
							$step6 = $row['step6'];
							$step7 = $row['step7'];
						}
					}
					mysqli_free_result($r);
				}
				
				// Set the CHECKED PROPERTY value for checkboxes
				$s1_checked = $s2_checked = $s3_checked = $s4_checked = '';
				$s5_checked = $s6_checked = $s7_checked = '';
				if ($step1 == 'Y') {
					$s1_checked = ' checked="checked" ';
				}
				if ($step2 == 'Y') {
					$s2_checked = ' checked="checked" ';
				}
				if ($step3 == 'Y') {
					$s3_checked = ' checked="checked" ';
				}
				if ($step4 == 'Y') {
					$s4_checked = ' checked="checked" ';
				}
				if ($step5 == 'Y') {
					$s5_checked = ' checked="checked" ';
				}
				if ($step6 == 'Y') {
					$s6_checked = ' checked="checked" ';
				}
				if ($step7 == 'Y') {
					$s7_checked = ' checked="checked" ';
				}				
			?>
			<div id="successmap_list">
				<h3>VFG SUCCESS ROADMAP</h3>
				<p align="center" style="font-size:.8em">"It's a beautiful day in Zamunda!!"</p>
				<dl>
					<dt>Step 1</dt>
						<dd>
							<input type="checkbox" name="dss_step1" id="dss_step1" value="1" <?php echo $s1_checked; ?>  onClick="cbChanged(this,'1');">
							<span>Check Appointments and Tasks:</span>
							<p>Check your VFG Calendar and your Tasks and Events by Date.</p>
						</dd>
						
					<dt>Step 2</dt>
						<dd>
							<input type="checkbox" name="dss_step2" id="dss_step2" value="2" <?php echo $s2_checked; ?> onClick="cbChanged(this,'2');">
							<span>Check for Assigned Prospects:</span>
							<p>Check your 'Assigned Prospects' page for any new prospects that have been assigned to you.</p>
						</dd>
					<dt>Step 3</dt>
						<dd>
							<input type="checkbox" name="dss_step3" id="dss_step3" value="3" <?php echo $s3_checked; ?> onClick="cbChanged(this,'3');">
							<span>Perform Tasks for the Day:</span>
							<p>Get on those tasks!  Make those calls, and send those texts and emails.</p>
						</dd>
					<dt>Step 4</dt>
						<dd>
							<input type="checkbox" name="dss_step4" id="dss_step4" value="4" <?php echo $s4_checked; ?> onClick="cbChanged(this,'4');">
							<span>Memory Jogger - 15 Minutes Minimum:</span>
							<p>Spend at least 15 minutes in the Memory Jogger.  Take a close look at the categories you don't have leads for.
							Chances are someone is going to pop into your head that isn't on the list.</p>
						</dd>
					<dt>Step 5</dt>
						<dd>
							<input type="checkbox" name="dss_step5" id="dss_step5" value="5" <?php echo $s5_checked; ?> onClick="cbChanged(this,'5');">
							<span>Review Leads List - Convert to Prospects:</span>
							<p>Review your current leads.  Work your magic and get them converted to Prospects.</p>
						</dd>
					<dt>Step 6</dt>
						<dd>
							<input type="checkbox" name="dss_step6" id="dss_step6" value="6" <?php echo $s6_checked; ?> onClick="cbChanged(this,'6');">
							<span>Additional Follow-Up:</span>
							<p>Do you have any follow-up that needs to be done today at this point?  Now is the perfect time to get to it.</p>
						</dd>
					<dt>Step 7</dt>
						<dd>
							<input type="checkbox" name="dss_step7" id="dss_step7" value="7" <?php echo $s7_checked; ?> onClick="cbChanged(this,'7');">
							<span>Work VFG Pro Marketing System:</span>
							<p>Get the word out to those you know need what you have found and believe in.  People are looking for an opportunity to do
							something different, they just don't know what that is yet.  Show them.</p>
						</dd>
				</dl>
			</div>
				
		<div class="cleardiv"></div>
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>