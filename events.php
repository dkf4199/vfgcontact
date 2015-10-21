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
?>
<html>
<head>
<title>Show / Add Events</title>
</head>
<body>
<h1>Show / Add Events</h1>
<?php
$repsid = $_SESSION['rep_id'];
				
include ('includes/config.inc.php');
include ('includes/phpfunctions.php');
//DB Connection
require_once (MYSQL);

// Add our new events
if ($_POST){
	$m = $_POST['m'];
	$d = $_POST['d'];
	$y = $_POST['y'];
	$et = $_POST['event_title'];
	$ed = $_POST['event_shortdesc'];
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_start = $y."-".$m."-".$d." ".$_POST["event_start_hh"].":".$_POST["event_start_mm"].":00";
	
	// Formatting for SQL datetime (if this is edited, it will NOT work.)
	$event_end = $y."-".$m."-".$d." ".$_POST["event_end_hh"].":".$_POST["event_end_mm"].":00";

	$insEvent_sql = "INSERT INTO calendar_events (rep_id, event_title, event_shortdesc, event_start, event_end) 
					VALUES('$repsid', '$et', '$ed', '$event_start', '$event_end') ";
			
	$r = mysqli_query($dbc, $insEvent_sql) or die(mysqli_error($dbc));
	
	if (mysqli_affected_rows($dbc) == 1){
		//$messages[] = 'Rep data update complete.';	
	} //end mysqli_affected_rows == 1
} else {
	$m = $_GET['m'];
	$d = $_GET['d'];
	$y = $_GET['y'];
}
// Show the events for this day:
$getEvent_sql = "SELECT event_title, event_shortdesc, 
						date_format(event_start, '%l:%i %p') as fmt_start, 
						date_format(event_end, '%l:%i %p') as fmt_end
				FROM calendar_events 
				WHERE month(event_start) = '$m'
				AND dayofmonth(event_start) = '$d' 
				AND year(event_start)= '$y' 
				ORDER BY event_start";
$r = mysqli_query($dbc, $getEvent_sql);

if (mysqli_num_rows($r) > 0){
	$event_txt = "<ul>";
	while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$event_title = stripslashes($ev["event_title"]);
		$event_shortdesc = stripslashes($ev["event_shortdesc"]);
		$fmt_start = $ev["fmt_start"];
		$fmt_end = $ev["fmt_end"];
		$event_txt .= "<li><strong>".$fmt_start." to ".$fmt_end."</strong>:
			      ".$event_title."<br/>".$event_shortdesc."</li>";
	}
	$event_txt .="</ul>";
	mysqli_free_result($r);
} else {
	$event_txt = "";
}

mysqli_close($dbc);

if ($event_txt != ""){
	echo "<p><strong>Today's Events:</strong></p>
	$event_txt
	<hr/>";
}

// Show form for adding the event:

echo "
<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
<p><strong>Add Event:</strong><br/>
Complete the form below then press the submit button when you are done.</p>
<p><strong>Event Title:</strong><br/>
<input type=\"text\" name=\"event_title\" size=\"25\" maxlength=\"25\"/></p>
<p><strong>Event Description:</strong><br/>
<input type=\"text\" name=\"event_shortdesc\" size=\"25\" maxlength=\"100\"/></p>
<p><strong>Event Start (hh:mm):</strong><br/>
<select name=\"event_start_hh\">";
for ($x=1; $x<=24; $x++){
	echo "<option value=\"$x\">$x</option>";
}
echo "</select> :
<select name=\"event_start_mm\">
<option value=\"00\">00</option>
<option value=\"15\">15</option>
<option value=\"30\">30</option>
<option value=\"45\">45</option>
</select>
<p><strong>Event End (hh:mm):</strong><br/>
<select name=\"event_end_hh\">";
for ($x=1; $x<=24; $x++){
	echo "<option value=\"$x\">$x</option>";
}
echo "</select> :
<select name=\"event_end_mm\">
<option value=\"00\">00</option>
<option value=\"15\">15</option>
<option value=\"30\">30</option>
<option value=\"45\">45</option>
</select>
<input type=\"hidden\" name=\"m\" value=\"".$m."\">
<input type=\"hidden\" name=\"d\" value=\"".$d."\">
<input type=\"hidden\" name=\"y\" value=\"".$y."\">
<br/><br/>
<input type=\"submit\" name=\"submit\" value=\"Add Event!\">
</form>";
?>
</body>
</html>

