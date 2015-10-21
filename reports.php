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
//Link back fields for edit screen
$_SESSION['from_page'] = 'admin_reps.php';
$_SESSION['back_btn_text'] = 'Back To Reps';
$_SESSION['srch_string'] = '';
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
<title>VFG Contact - Ad Hoc Reports</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_reports.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/smoothness4/jquery-ui-1.10.4.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.4.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/admin_report_handlers.js'></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php include('includes/html/admin_header_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['admin_firstname'].' '.$_SESSION['admin_lastname'].'.'; ?></div>
		
		<!-- REPORT LINKS LEFT-SIDE -->
		<div id="reports_list" class="opacity80">
			<div id="accordion">
				<h3>Rep Reports</h3>
				<div>
					<a href="" class="gen_rep_summary">General Rep Summary</a>
					<a href="" class="rep_purchased_policy">Reps - Purchased Policies</a>
				</div>
				<h3>Prospect Reports</h3>
				<div>
					<a href="" class="tier2_calls_scheduled">Tier 2 - Calls Scheduled</a>
					<a href="" class="tier2_calls_made">Tier 2 - Calls Made</a>
					<br />
					<a href="" class="tier3_calls_scheduled">Tier 3 - Calls Scheduled</a>
					<a href="" class="tier3_calls_made">Tier 3 - Calls Made</a>
					<br />
					<a href="" class="tier4_calls_scheduled">Tier 4 - Calls Scheduled</a>
					<a href="" class="tier4_calls_made">Tier 4 - Calls Made</a>
					<br />
					<a href="" class="tier_calls_scheduled_by_rep">Tier Calls Scheduled By Rep</a>
				</div>
			</div>
			
			
			<!--<span>Reports</span>
			<a href="" class="report_1">Report 1</a>
			<a href="" class="report_2">Report 2</a> -->
			
			
		</div> <!-- close report_list -->
		
		<div id="ajax_viewreports" class="opacity80">
		</div> <!-- close ajax_viewcontacts -->
		
		<div class="cleardiv"></div>
		
		
	</div> <!-- close main content -->

</div>	<!-- close wrapper -->
<?php //include('includes/html/footer.html'); ?>
</body>
</html>