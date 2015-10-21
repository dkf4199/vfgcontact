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
		yesno = 'Y';
		//alert('Checkbox checked. Step = '+step+'  YesNo = '+yesno);
		
		//AJAX POST
		$.ajax({
			url:"ajax_update_uss.php",
			type: "POST",
			data: { stepnum: step,
					yesno: yesno },
			dataType: "html",		
			success:function(result){
				$("#ajax_message").html(result);
			},	//end success:function
			error:function(jqXHR, textStatus, errorThrown){
				//$("#ajax_edit_consultant_event").html(errorThrown);
			}
		}); //end $.ajax


	} else {
		// UNCHECKED
		yesno = 'N';
		//alert('Checkbox checked. Step = '+step+'  YesNo = '+yesno);
		
		//AJAX POST
		$.ajax({
			url:"ajax_update_uss.php",
			type: "POST",
			data: { stepnum: step,
					yesno: yesno },
			dataType: "html",		
			success:function(result){
				$("#ajax_message").html(result);
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
				$step1 = $step2 = $step3a = $step3b = $step4 = $step5 = $step6 = $step7 = $step8 = $step9 = '';
				$q = "SELECT step1, step2, step3a, step3b, step4, step5, step6, step7, step8, step9
					  FROM ultimate_success_steps
					  WHERE repid = '$repsid' ";
				$r = mysqli_query($dbc, $q);
				if ($r){
					if (mysqli_num_rows($r) > 0){
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							$step1 = $row['step1'];
							$step2 = $row['step2'];
							$step3a = $row['step3a'];
							$step3b = $row['step3b'];
							$step4 = $row['step4'];
							$step5 = $row['step5'];
							$step6 = $row['step6'];
							$step7 = $row['step7'];
							$step8 = $row['step8'];
							$step9 = $row['step9'];
						}
					}
					mysqli_free_result($r);
				}
				
				// Set the CHECKED PROPERTY value for checkboxes
				$s1_checked = $s2_checked = $s3a_checked = $s3b_checked = $s4_checked = '';
				$s5_checked = $s6_checked = $s7_checked = $s8_checked = $s9_checked = $s10_checked = '';
				if ($step1 == 'Y') {
					$s1_checked = ' checked="checked" ';
				}
				if ($step2 == 'Y') {
					$s2_checked = ' checked="checked" ';
				}
				if ($step3a == 'Y') {
					$s3a_checked = ' checked="checked" ';
				}
				if ($step3b == 'Y') {
					$s3b_checked = ' checked="checked" ';
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
				if ($step8 == 'Y') {
					$s8_checked = ' checked="checked" ';
				}
				if ($step9 == 'Y') {
					$s9_checked = ' checked="checked" ';
				}	
			?>
			<div id="ajax_message"></div>
			<div id="successmap_list">
				<h3>10 Steps to Unlimited Success</h3>
				<p align="center" style="font-size:.8em"></p>
				<dl>
					<dt>Step 1, Day 1:</dt>
						<dd>
							<input type="checkbox" name="uss_step1" id="uss_step1" value="1" <?php echo $s1_checked; ?>  onClick="cbChanged(this,'1');">
							<span>View the Fast Start School Video:</span>
							<ul>
								<li>2 hours - this can be done in pieces.</li>
								<li>Must be completed prior to your Tier 3 appointment.</li>
								<li>Located in your VFGpro.com back office on the "VFG System Fast Start" tab.</li>
							</ul>
						</dd>
						
					<dt>Step 2, Day 2 or Day 3:</dt>
						<dd>
							<input type="checkbox" name="uss_step2" id="uss_step2" value="2" <?php echo $s2_checked; ?> onClick="cbChanged(this,'2');">
							<span>Complete your Tier 3 Meeting to get your FNA done:</span>
							<ul>
								<li>If you have a spouse, be sure they attend the appointment.</li>
								<li>You must complete the Fast Start School video before attending the meeting.</li>
								<li>Spouse must have watched 5 & 20 minute overviews - OR the 11 minute product video beforehand.</li>
							</ul>
						</dd>
					<dt>Step 3, Day 1, 2, or 3:</dt>
						<dd>
							<input type="checkbox" name="uss_step3A" id="uss_step3A" value="3a" <?php echo $s3a_checked; ?> onClick="cbChanged(this,'3a');">
							<span>A: View Tier 1 Videos 1 & 2:</span>
							<ul>
								<li>This will teach you how to drive traffic to your webpage and how to use the CRM to manage your prospects.</li>
								<li>Located in your VFGPro.com back office on the "VFG System Fast Start" tab under "VFG System Tier 1-4 Videos Step 2.</li>
							</ul>
						</dd>
						<dd>
							<input type="checkbox" name="uss_step3B" id="uss_step3B" value="3b" <?php echo $s3b_checked; ?> onClick="cbChanged(this,'3b');">
							<span>B: Complete a Fast Start Call with Your Manager:</span>
							<ul>
								<li>To review what you have learned from the videos and get oriented with your marketing and inviting efforts.</li>
							</ul>
						</dd>
					<dt>Step 4 ASAP:</dt>
						<dd>
							<input type="checkbox" name="uss_step4" id="uss_step4" value="4" <?php echo $s4_checked; ?> onClick="cbChanged(this,'4');">
							<span>Do your pre-licensing with Testeachers & Pass your state life insurance exam:</span>
							<ul>
								<li><b>If currently licensed</b> - Begin your Contracting Transamerica, WRL, ING, and Nationwide - and sign up for automatic E & O payments 
								    (The latter will save you $10/month and enable you to qualify for the $30/month rebate).</li>
								<li>Complete your anti-money laundering training course and the IUL training course.</li>
								<li>Sign up for direct deposit. This will get you set up to get paid.</li>
								<li>All the above are located in VFGpro on the "Carrier Links & Contracts" menu tab.</li>
								<li>Also found in your myVFG.com Transamerica Platform Back Office.</li>
							</ul>
						</dd>
					<dt>Step 5 - Must be complete in first 7 days to earn first half of Fast Track:</dt>
						<dd>
							<input type="checkbox" name="uss_step5" id="uss_step5" value="5" <?php echo $s5_checked; ?> onClick="cbChanged(this,'5');">
							<span>Complete Tier 4 with your spouse....Start a suitable plan or create referral sale:</span>
							<ul>
								<li>This completes Part 1 of your Fast Track and earns you a 25% reduction in your VFG Guidelines to SVP (Senior Vice President).</li>
							</ul>
						</dd>
					<dt>Step 6:</dt>
						<dd>
							<input type="checkbox" name="uss_step6" id="uss_step6" value="6" <?php echo $s6_checked; ?> onClick="cbChanged(this,'6');">
							<span>View VBS Manual Video and read cover to cover:</span>
							<ul>
								<li>This is your <b>business Bible</b>.</li>
								<li>It is critical to your success to know and understand all the steps in the Virtual Business System Manual.</li>
							</ul>
						</dd>
					<dt>Step 7 - During your first 30 days to complete your fast track & earn another 25% reduction in your guidelines to SVP, totaling 50%:</dt>
						<dd>
							<input type="checkbox" name="uss_step7" id="uss_step7" value="7" <?php echo $s7_checked; ?> onClick="cbChanged(this,'7');">
							<span>Drive Traffic to Your Landing Pages - Get 10 Prospects Minimum to Tier 2 Webinars:</span>
							<ul>
								<li>Drive traffic to your landing pages using the VFG Online Marketing Plan (Video from Step 3 above).</li>
								<li>Drive traffic to your landing pages by implementing Steps 8 and 9 below.</li>
								<li>Follow up effectively with what you have learned to send at least 10 prospects to Tier 2 webinars.</li>
								<li>Use the 3x3 contacting process.</li>
								<li>Take advantage of Chris' Live Monday and Wednesday sessions to learn and develop your own skill.</li>
								<li><b>Follow up with your Tier 2 guests with your manager to create at least 2 Personal Recruits that start
								    a plan or refer a plan in 7 days on Fast Track.</b></li>
							</ul>
						</dd>
					<dt>Step 8 - During your first 30 days:</dt>
						<dd>
							<input type="checkbox" name="uss_step8" id="uss_step8" value="8" <?php echo $s8_checked; ?> onClick="cbChanged(this,'8');">
							<span>Develop Your Marketing Plan and Databases:</span>
							<ul>
								<li>Facebook Friends and Business Page "likes".</li>
								<li>Linkedin Connections and up to 50 groups.</li>
								<li>Google+ Circles.</li>
								<li>VFG Contact Lead Wizard.</li>
								<li>Email accounts and phone contacts databases.</li>
								<li>This step will reward you with quality prospects and help you become a social network influencer whom people will follow.</li>
							</ul>
						</dd>
					<dt>Step 9 - Every Week - Weekly Action Plan:</dt>
						<dd>
							<input type="checkbox" name="uss_step9" id="uss_step9" value="9" <?php echo $s9_checked; ?> onClick="cbChanged(this,'9');">
							<span>Plan Your Work - Work Your Plan:</span>
							<ul>
								<li>Send out a minimum of 50 emails and/or social media messages weekly to your warm markets.</li>
								<li>Run classified ads 1 paid $25 per week minimum, or 8 non-paid weekly.</li>
								<li>Enroll in the VFG Co-op $59 (Must follow up on leads daily).</li>
								<li>Attend ALL VFG Webinars Tues/Sat and Chris' LIVE call back webinars Mon 9:30am and Wed 10am PST.</li>
								<li>View "Classified Marketing 101 Video" in VFGPro.com under "VFG Online Marketing" tab.</li>
								<li>This step will will drive more traffic to your landing pages, creating more and more people to follow up with and get
									you to be increasingly proficient with our business model.</li>
							</ul>
						</dd>
					<dt>Step 10 - To Do List Every Day:</dt>
						<dd>
							<ul>
								<li>Plan each day in advance on a calendar and work your plan - No interruptions, Stay focused on your results.  3-4 times weekly minimum.</li>
								<li>Devote 2 uninterrupted hours daily following up with your prospects live.</li>
								<li>1 hour developing your Prospect lists email, Facebook, Linkedin.</li>
								<li>1 hour posting, sharing, and commenting on Facebook, Google+, and Linkedin Page and Groups.</li>
								<li><b>We research and share the articles and information</b> for you to share and post on your social media on our VFG Facebook page
									at facebook.com/ceoteam.</li>
								<li>Download and Personalize the "VFG 2014 Business Plan" to read daily in VFGPro "VFG Documents and Support" then "VFG Important Links & Docs".</li>
								<li>Having a written plan you read daily will increase your chances of success by 45% and help you accomplish the most work in the least amount of time.</li>
							</ul>
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