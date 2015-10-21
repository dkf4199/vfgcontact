<?php
session_start();
	
	$mgr_repid = $_SESSION['assigned_manager_repid'];	//consultants repid to get events from
	
	$m = $_GET['month'];
	$d = $_GET['day'];
	$y = $_GET['year'];
		
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	
	//Get the events from the consultant's calendar
	// Show the events for this day:
	$getEvent_sql = "SELECT id, rep_id, title, description, 
							date_format(start, '%l:%i %p') as fmt_start, 
							date_format(end, '%l:%i %p') as fmt_end
					FROM fc_events
					WHERE rep_id = '$mgr_repid'
					AND month(start) = '$m'
					AND dayofmonth(start) = '$d' 
					AND year(start)= '$y' 
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
					<td>'.stripslashes($ev["title"]).
					'</td>
					<td>'.$ev["fmt_start"].
					'</td>
					 <td>'.$ev["fmt_end"].
					 '</td></tr>';
		}
		$displayform .= '</table>';
		mysqli_free_result($r);
	} 
	
	echo $displayform;
	mysqli_close($dbc);
	
?>