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
// Now we need to define "A DAY", which will be used later in the script:
define("ADAY", (60*60*24));

if ((!isset($_POST['month'])) || (!isset($_POST['year']))) {
	$nowArray = getdate();
	$month = $nowArray['mon'];
	$year = $nowArray['year'];
} else {
	$month = $_POST['month'];
	$year = $_POST['year'];
}
$start = mktime(12,0,0,$month,1,$year);
$firstDayArray = getdate($start);

$repsid = $_SESSION['rep_id'];
			
include ('includes/config.inc.php');
include ('includes/phpfunctions.php');
//DB Connection
require_once (MYSQL);
	
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
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfg_jquery_ui_menu.css" />
<script src="./js/jquery-1.9.1.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {
	
});
</script>
<script type="text/javascript">
function eventWindow(url){
	event_popupWin = window.open(url, 'event', 'resizable=yes, scrollbars=yes, toolbar=no,width=400,height=400');
	event_popupWin.opener = self;
}
</script>

</head>

<body style="background-image:url(images/tcsfade.jpg);">
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<!-- CALENDAR CODE -->
		<h1>Select a Month/Year</h1>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<select name="month">
		<?php
			$months = Array("January", "February", "March", 
							"April", "May", "June", "July", 
							"August", "September", "October", 
							"November", "December");

			for ($x=1; $x<=count($months); $x++){
				echo "<option value=\"$x\"";
				if ($x == $month){
					echo " selected";
				}
				echo ">".$months[$x-1]."</option>";
			}
			?>
			</select>
			<select name="year">
			<?php
			for ($x=1980; $x<=2020; $x++){
				echo "<option";
				if ($x == $year){
					echo " selected";
				}
				echo ">$x</option>";
			}
		?>
		</select>
		<input type="submit" name="submit" value="Go!">
		</form>
		<br />
		<?php
			$days = Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
			echo "<table border=\"1\" cellpadding=\"5\"><tr>\n";
			foreach ($days as $day){
				echo "<td style=\"background-color: #CCCCCC; text-align: center; width: 14% \"><strong>$day</strong></td>\n";
			}
			for ($count=0; $count < (6*7); $count++){
				$dayArray = getdate($start);
				if (($count % 7) == 0){
					if ($dayArray['mon'] != $month){
						break;
					} else {
						echo "</tr><tr>\n";
					}
				}
				if ($count < $firstDayArray['wday'] || $dayArray['mon'] != $month){
					echo "<td>&nbsp;</td>\n";
				} else {
					//Check for any Events
					$chkEvent_sql = "SELECT event_title 
									 FROM calendar_events 
									 WHERE month(event_start) = '$month' 
									 AND dayofmonth(event_start) = '".$dayArray["mday"]."' ".
									 "AND year(event_start) = '$year' 
									 ORDER BY event_start";
					$r = mysqli_query($dbc, $chkEvent_sql);
					if (mysqli_num_rows($r) > 0) {
						$event_title = "<br/>";
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							$event_title .= stripslashes($row['event_title'])."<br/>";
						}
						mysqli_free_result($r);
					} else {
						$event_title = "";
					}

					echo "<td valign=\"top\">
							<a href=\"javascript: eventWindow('events.php?m=".$month."&d=".$dayArray["mday"]."&y=$year');\">".$dayArray["mday"]."</a><br/>".
							$event_title."</td>\n";

					unset($event_title);

					$start += ADAY;
				}
			}
			echo "</tr></table>";
		?>
		<!-- End Calendar code -->
		
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>