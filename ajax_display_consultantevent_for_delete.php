<?php
session_start();

	$repid = $_GET['repid'];
	$eventid = $_GET['event_id'];
	
			
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//Get the event info
	//MySQL date_format: %l is hour, %i is the minutes, %p is am/pm
	$getEvent_sql = "SELECT cal_id, rep_id, event_title, event_shortdesc, 
							event_start, event_end
					FROM calendar_events 
					WHERE rep_id = '$repid' 
					AND cal_id = $eventid LIMIT 1";
	$r = mysqli_query($dbc, $getEvent_sql);
	if (mysqli_num_rows($r) == 1){
		$displayform = '<h4>Edit Event</h4>';
		while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$displayform .= '<ul>
								<li>
									<label>Event title:</label>
									<label class="displayonly">'.$ev['event_title'].'</label>
								</li>
								<li>
									<label>Description:</label>
									<label class="displayonly">'.$ev['event_shortdesc'].'</label>
								</li>
								<li>
									<label>Event Start:</label>
									<label class="displayonly">'.$ev['event_start'].'</label>
								</li>
								<li>
									<label>Event End:</label>
									<label class="displayonly">'.$ev['event_end'].'</label>
								</li>	
								 <li>
									<input type="button" id="consultant_deleteevent_button" class="generalbutton" value="Delete Event"
												onClick="javascript: return deleteConsultantEvent();" />
									<input type="hidden" id="deleteevent_cal_id" value="'.$ev['cal_id'].'" />
								</li>
							</ul>
							<div id="ajax_delete_consultant_event" style="padding-left:1em;" ></div>';
		}
			
	} else {
		$displayform = '<h4>No Event Found.</h4>';
	}
	mysqli_close($dbc);
	echo $displayform;
?>