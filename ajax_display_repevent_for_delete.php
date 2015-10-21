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
	$getEvent_sql = "SELECT a.id, a.rep_id, a.title, a.description, 
							a.start, a.end, b.parent_id, b.repeats
					FROM fc_events a LEFT JOIN fc_events_parent b
					ON a.parent_id = b.parent_id
					WHERE a.rep_id = '$repid' 
					AND a.id = $eventid LIMIT 1";
	$r = mysqli_query($dbc, $getEvent_sql);
	if (mysqli_num_rows($r) == 1){
		$displayform = '<h4>Delete Event</h4>';
		while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			// some events may not have a parent_id....records in db from before
			// the recurring events enhancement
			$parent_id = 0;
			if ( isset($ev['parent_id']) ){
				$parent_id = $ev['parent_id'];
			}
			
			$displayform .= '<ul>
								<li>
									<label>Event title:</label>
									<label class="displayonly">'.$ev['title'].'</label>
								</li>
								<li>
									<label>Description:</label>
									<label class="displayonly">'.$ev['description'].'</label>
								</li>
								<li>
									<label>Event Start:</label>
									<label class="displayonly">'.$ev['start'].'</label>
								</li>
								<li>
									<label>Event End:</label>
									<label class="displayonly">'.$ev['end'].'</label>
								</li>
								<li>
									<label>Recurring Event:</label>';
								if ( $ev['repeats'] == 1 ){
									$displayform .= '<label class="displayonly">YES</label>';
								} else {
									$displayform .= '<label class="displayonly">NO</label>';
								}
				$displayform .= '</li>
								<li>
									<input type="button" id="rep_deleteevent_button" class="generalbutton" value="Delete Event"
												onClick="javascript: return deleteRepEvent();" />
									<input type="hidden" id="deleteevent_fc_id" value="'.$ev['id'].'" />
									<input type="hidden" id="deleteevent_fc_parent_id" value="'.$parent_id.'" />
									<input type="hidden" id="deleteevent_fc_repeats" value="'.$ev['repeats'].'" />
								</li>';
								if ( $ev['repeats'] == 1 ){
									$displayform .= '<li>
												<input type="button" id="rep_delete_recurring_events_button" class="generalbutton" 
														value="Delete the Recurring Events" onClick="javascript: return deleteRepRecurringEvents();" />
												</li>';
								}
				$displayform .= '</ul>
							<div id="ajax_delete_consultant_event" style="padding-left:1em;" ></div>';
		}
			
	} else {
		$displayform = '<h4>No Event Found.</h4>';
	}
	mysqli_close($dbc);
	echo $displayform;
?>