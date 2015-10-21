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
$months = Array("January", "February", "March", 
				"April", "May", "June", "July", 
				"August", "September", "October", 
				"November", "December");
				
// Now we need to define "A DAY", which will be used later in the script:
define("ADAY", (60*60*24));

//NEXT AND PREVIOUS MONTHS - links
$cMonth = isset($_REQUEST["month"]) ? $_REQUEST["month"] : date("n");
$cYear = isset($_REQUEST["year"]) ? $_REQUEST["year"] : date("Y");
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {	//Going backwards.....subtract 1 year and set to December
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {	//January of Next year....add 1 to cYear
    $next_month = 1;
    $next_year = $cYear + 1;
}
/*
if ((!isset($_POST['month'])) || (!isset($_POST['year']))) {
	$nowArray = getdate();
	$month = $nowArray['mon'];
	$year = $nowArray['year'];
} else {
	$month = $_POST['month'];
	$year = $_POST['year'];
}*/

$start = mktime(12,0,0,$cMonth,1,$cYear);
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
<link rel="stylesheet" type="text/css" media="screen" href="./css/consultantcalendar.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/tcs_modal_handlers.js'></script>
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
		<?php
			$days = Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
			echo "<table id=\"consultantcalendar_table\" ><tr>\n";
			echo "<th colspan=\"7\">
					<strong><a href=\"".$_SERVER["PHP_SELF"]."?month=".$prev_month."&year=".$prev_year."\" ><<<</a>&nbsp;&nbsp;".
					$months[$cMonth-1].' '.$cYear.
					"&nbsp;&nbsp;<a href=\"".$_SERVER["PHP_SELF"]."?month=".$next_month."&year=".$next_year."\" >>>></a>
					</th></tr>\n";
			echo "<tr>\n";
			//Put the previous and next month link header here...
			foreach ($days as $day){
				echo "<th><strong>$day</strong></th>\n";
			}
			for ($count=0; $count < (6*7); $count++){
				$dayArray = getdate($start);
				if (($count % 7) == 0){
					if ($dayArray['mon'] != $cMonth){
						break;
					} else {
						echo "</tr><tr>\n";
					}
				}
				if ($count < $firstDayArray['wday'] || $dayArray['mon'] != $cMonth){
					echo "<td>&nbsp;</td>\n";
				} else {
					//Check for any Events
					$chkEvent_sql = "SELECT event_title 
									 FROM calendar_events 
									 WHERE rep_id = '$repsid'
									 AND month(event_start) = '$cMonth' 
									 AND dayofmonth(event_start) = '".$dayArray["mday"]."' ".
									 "AND year(event_start) = '$cYear' 
									 ORDER BY event_start";
					$r = mysqli_query($dbc, $chkEvent_sql);
					if (mysqli_num_rows($r) > 0) {
						$event_title = "<br/>";
						$evcount = 0;
						$morerows = false;
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							$evcount++;
							if ($evcount < 4){
								$event_title .= stripslashes($row['event_title'])."<br/>";
							} else {
								$morerows = true;
							}
						}
						mysqli_free_result($r);
						if ($morerows) {
							$event_title .= "<br />More Events....";
						}
					} else {
						$event_title = "";
					}

					//echo "<td valign=\"top\">
					//		<a href=\"javascript: eventWindow('events.php?m=".$cMonth."&d=".$dayArray["mday"]."&y=$cYear');\">".$dayArray["mday"]."</a><br/>".
					//		$event_title."</td>\n";
					
					//Modal Div - set month day year in session vars above
					echo "<td valign=\"top\">
							<a href=\"#\" onClick=\"javascript: return consultantEventModal('".$cMonth."','".$dayArray["mday"]."','".$cYear."');\" >".$dayArray["mday"]."</a><br/>".
							$event_title."</td>\n";
					
					unset($event_title);

					$start += ADAY;		//increment $start by a full day
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