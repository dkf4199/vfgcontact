<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	
	$m = $_GET['month'];
	$d = $_GET['day'];
	$y = $_GET['year'];
		
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	
	$displayform = '<p><strong>Today\'s Events:</strong><br />
					<a href="#" onClick="javascript: return displayConsultantAddEvent();" >New</a></p>';
			
	// Show the events for this day:
	$getEvent_sql = "SELECT cal_id, rep_id, event_title, event_shortdesc, 
							date_format(event_start, '%l:%i %p') as fmt_start, 
							date_format(event_end, '%l:%i %p') as fmt_end
					FROM calendar_events 
					WHERE rep_id = '$repid'
					AND month(event_start) = '$m'
					AND dayofmonth(event_start) = '$d' 
					AND year(event_start)= '$y' 
					ORDER BY event_start";
	$r = mysqli_query($dbc, $getEvent_sql);

	if (mysqli_num_rows($r) > 0){
		$displayform .= '<ul>';
		while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$event_title = stripslashes($ev["event_title"]);
			$event_shortdesc = stripslashes($ev["event_shortdesc"]);
			$fmt_start = $ev["fmt_start"];
			$fmt_end = $ev["fmt_end"];
			$eid = $ev["cal_id"];
			$displayform .= '<li><strong>'.$fmt_start.' to '.$fmt_end.'</strong>.  '.
					  $event_title.' - '.$event_shortdesc.
					  '<br /><a href="#" onClick="javascript: return displayConsultantEventInfo(\''.$repid.'\',\''.$eid.'\');" >Edit</a>&nbsp;
					  <a href="#" onClick="javascript: return displayConsultantEventDeleteInfo(\''.$repid.'\',\''.$eid.'\');">Delete</a></li>';
		}
		$displayform .= '</ul>';
		mysqli_free_result($r);
	} 
	
	echo $displayform;
	
?>