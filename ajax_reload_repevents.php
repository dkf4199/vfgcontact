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
					<a href="#" onClick="javascript: return displayRepAddEvent();" >New</a></p>';
			
	// Show the events for this day:
	$getEvent_sql = "SELECT id, rep_id, title, description, 
							date_format(start, '%l:%i %p') as fmt_start, 
							date_format(end, '%l:%i %p') as fmt_end
					FROM fc_events 
					WHERE rep_id = '$repid'
					AND month(start) = '$m'
					AND dayofmonth(start) = '$d' 
					AND year(start)= '$y' 
					ORDER BY start";
	$r = mysqli_query($dbc, $getEvent_sql);

	if (mysqli_num_rows($r) > 0){
		$displayform .= '<ul>';
		while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$event_title = stripslashes($ev["title"]);
			$event_desc = stripslashes($ev["description"]);
			$fmt_start = $ev["fmt_start"];
			$fmt_end = $ev["fmt_end"];
			$eid = $ev["id"];
			$displayform .= '<li><strong>'.$fmt_start.' to '.$fmt_end.'</strong>.  '.
					  $event_title.' - '.$event_desc.
					  '<br /><a href="#" onClick="javascript: return displayRepEventInfo(\''.$repid.'\',\''.$eid.'\');" >Edit</a>&nbsp;
					  <a href="#" onClick="javascript: return displayRepEventDeleteInfo(\''.$repid.'\',\''.$eid.'\');">Delete</a></li>';
		}
		$displayform .= '</ul>';
		mysqli_free_result($r);
	} 
	
	echo $displayform;
	
?>