<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	// POST VARS COMING IN
	// rep vfgid, date
	
	$event_date = $_POST['eventdate'];
	
	$crepid = $ctz = '';
	if (isset($_SESSION['consultants_repid'])){
		$crepid = $_SESSION['consultants_repid'];
	}
	if (isset($_SESSION['consultants_tz'])){
		$ctz = $_SESSION['consultants_tz'];
	}

	//echo 'Consultant Rep ID: '.$crepid.'<br />';
	
	//Reconfigure the incomming date (mm-dd-yyyy) to (yyyy-mm-dd) into list vars
	list($dt_mm,$dt_dd,$dt_yy) = explode('-',$event_date);
	
	//echo 'M D Y: '.$dt_mm.' '.$dt_dd.' '.$dt_yy.'<br />';
	//Get the events from the consultant's calendar
	// Show the events for this day:
	/*$getEvent_sql = "SELECT cal_id, rep_id, event_title, event_shortdesc, 
							date_format(event_start, '%l:%i %p') as fmt_start, 
							date_format(event_end, '%l:%i %p') as fmt_end
					FROM calendar_events 
					WHERE rep_id = '$crepid'
					AND month(event_start) = '$dt_mm'
					AND dayofmonth(event_start) = '$dt_dd' 
					AND year(event_start)= '$dt_yy' 
					ORDER BY event_start";
	*/
	$getEvent_sql = "SELECT id, rep_id, title, description, 
							date_format(start, '%l:%i %p') as fmt_start, 
							date_format(end, '%l:%i %p') as fmt_end
					FROM fc_events 
					WHERE rep_id = '$crepid'
					AND month(start) = '$dt_mm'
					AND dayofmonth(start) = '$dt_dd' 
					AND year(start)= '$dt_yy' 
					ORDER BY start";
	$r = mysqli_query($dbc, $getEvent_sql);
	
	$displayform = '<table width="100%" border="0">
		 <tr>
		  <th>Event</th>
		  <th>Start Time</th>
		  <th>End Time</th>
		 </tr>';
	
	
	if (mysqli_num_rows($r) > 0){
		while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$displayform .= '<tr>
					<td>'.stripslashes($ev['title']).
					'</td>
					<td>'.$ev['fmt_start'].
					'</td>
					 <td>'.$ev['fmt_end'].
					 '</td></tr>';
		}
		$displayform .= '</table>';
		mysqli_free_result($r);
	} else {
		$displayform .= 'No Events today.';
	}
	
	echo $displayform;
	mysqli_close($dbc);
	
?>