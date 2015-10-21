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
date_default_timezone_set($_SESSION['rep_tz']);
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
</script>

</head>

<body style="background-image:url(images/tcs.jpg);">
<div id="wrapper">
	<!-- Header -->
	<?php 
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<!-- success map -->
		<div class="successmapdiv">
			<p>10 Steps to Unlimited Success</p>
			<p>"If you are true to the system, you will be successful in this business."</p>
			<p>
				<a href="rep_success_steps.php"><img src="./images/successmap.jpg" /></a>
			</p>
			<p>
				<a href="rep_successmap.php">VFG Daily Steps Roadmap</a>
			</p>
		</div>
		<!-- notifications -->
		<div class="notifications">
			<p>VFG Notifications:</p>
			<ul>
				<li>
					<span style="color:red;">12/18/2013 :</span> Please go to the Google Play Store and download the latest version of the VFG Dialer
					mobile app.  The previous version contained a bug that has been fixed in the latest version.  Thank you for your patience!
				</li>
				<li>
					The <span style="color:blue;">ANDROID VERSION</span> of the phone Dialer is now available on the Google Play Store.  Search for "VFG Dialer" and you will find
					it.  You'll see the our Swirl logo and the name "VFG Dialer" for the app.
				</li>
				<li style="list-style:none;">&nbsp;</li>
				<li>
					Remember: Training calls every Tuesday and Saturday.
				</li>
			</ul>
		</div>
		
		<div class="cleardiv"></div>
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>