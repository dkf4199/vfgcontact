<?php
session_start();
// If no session variable exists, redirect the user:
if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {

	require_once ('./includes/phpfunctions.php');
	$url = absolute_url('index.php');
	//javascript redirect using window.location
	echo '<script language="Javascript">';
	echo 'window.location="' . $url . '"';
	echo '</script>';
	exit();
	
} else { // Cancel the session.

	$_SESSION = array(); // Clear the variables.
	session_destroy(); // Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.
	
	//session_destroy();
	//Reset the timezone to mine....because I live here on west coast
	date_default_timezone_set('America/Los_Angeles');
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
<title>VFG Contact - Rep Logout</title>
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
	<!-- Header -->
	<?php include('includes/html/newheader_nolog.html'); ?>
	
	<div class="maincontent">
			
		<div class="logoutspan opacity75"><p>You are now logged out. <a href="rep_login.php">Click Here</a> to log back in.</p></div>
		<div class="cleardiv"></div>
	</div> <!-- close main content -->
	
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>