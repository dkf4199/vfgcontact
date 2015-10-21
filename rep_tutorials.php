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
<title>VFG Contacts - Rep Maindash</title>
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

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
				
		<!-- Add Contact Form -->
		<div class="formbox roundcorners opacity80">
			<h2>Tutorial Videos</h2>
			<div class="webform">
				<p align="center">
					<a href="https://www.youtube.com/watch?v=4-LruouBotA" target="_blank">Registration and Rep Data Edit</a><br /><br />
					<a href="https://www.youtube.com/watch?v=L7nVZ__3hH0" target="_blank">Contact and Edit Screen Overview</a><br /><br />
					<a href="https://www.youtube.com/watch?v=w6_QvgTwfMs" target="_blank">Edits and Communication</a><br /><br />
					<a href=" https://www.youtube.com/watch?v=Fq4LNpdBYco" target="_blank">Tasks and Templates</a><br /><br />
					<a href="https://www.youtube.com/watch?v=DZfDgag47sc" target="_blank">Calendar and Other Features</a><br /><br />
					<a href="http://www.youtube.com/watch?v=cQLbCuI4BNo" target="_blank">VFG Android App Install</a>
					
				</p>
			</div>
		</div>	
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>