<?php
session_start();
// Include the database sessions file:
// The file starts the session.
//require('includes/db_sessions.inc.php');
// If no agent session value is present, redirect the user:
if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {
	require_once ('./includes/phpfunctions.php');
	$url = absolute_url('vfg_admin.php');
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
<title>VFG Contacts - Admin Maindash</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfg_jquery_ui_menu.css" />
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
</head>

<body style="background-image:url(images/tcs.jpg);">
<div id="wrapper">
	<!-- Header -->
	<?php include('includes/html/admin_header_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['admin_firstname'].' '.$_SESSION['admin_lastname'].'.'; ?></div>
		
		<!-- notifications -->
		<div class="notifications opacity80">
			<p>VFG Notifications:</p>
			<ul>
				<li>Admin Notifications:</li>
			</ul>
		</div>
		
		</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>