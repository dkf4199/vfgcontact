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
	$getEvent_sql = "SELECT id, rep_id, title, description, 
							date_format(start, '%H') as fmt_start_hh,
							date_format(start, '%i') as fmt_start_mm, 							
							date_format(end, '%H') as fmt_end_hh,
							date_format(end, '%i') as fmt_end_mm
					FROM fc_events 
					WHERE rep_id = '$repid' 
					AND id = $eventid LIMIT 1";
	$r = mysqli_query($dbc, $getEvent_sql);
	if (mysqli_num_rows($r) == 1){
		$displayform = '<h4>Edit Event</h4>';
		while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$displayform .= '<ul>
								<li>
									<label class="blocklabel">Event title:</label>
									<input name="edit_event_title" id="edit_event_title" type="text" size="40" maxlength="100" 
											value="'.$ev['title'].'" />
								</li>
								<li>
									<label class="blocklabel">Description:</label>
									<input name="edit_event_desc" id="edit_event_desc" type="text" size="50" maxlength="100" 
											value="'.$ev['description'].'" />
								</li>
								<li>
									<label class="blocklabel">Event Start (hh:mm):</label>
									<select name="edit_event_start_hh" id="edit_event_start_hh" >';
								foreach($event_hh as $id=>$name){
									if($ev['fmt_start_hh'] == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									$displayform .= "<option $sel value=\"$id\">$name</option>";
								}	
									
			$displayform .= '</select> :
							<select name="edit_event_start_mm" id="edit_event_start_mm" >';
							foreach($event_mm as $id=>$name){
								if($ev['fmt_start_mm'] == $id){
									$sel = 'selected="selected"';
								}
								else{
									$sel = '';
								}
								$displayform .= "<option $sel value=\"$id\">$name</option>";
							}
			$displayform .= '</select>
							 </li>
							 <li>
									<label class="blocklabel">Event End (hh:mm):</label>
									<select name="edit_event_end_hh" id="edit_event_end_hh" >';
								foreach($event_hh as $id=>$name){
									if($ev['fmt_end_hh'] == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									$displayform .= "<option $sel value=\"$id\">$name</option>";
								}	
									
			$displayform .= '</select> :
							<select name="edit_event_end_mm" id="edit_event_end_mm" >';
							foreach($event_mm as $id=>$name){
								if($ev['fmt_end_mm'] == $id){
									$sel = 'selected="selected"';
								}
								else{
									$sel = '';
								}
								$displayform .= "<option $sel value=\"$id\">$name</option>";
							}
			$displayform .= '</select>
							 </li>
							 <li>
								<input type="button" id="rep_editevent_button" class="generalbutton" value="Edit Event"
											onClick="javascript: return updateRepEvent();" />
								<input type="hidden" id="editevent_fc_id" value="'.$ev['id'].'" />
							</li>
							</ul>
							<div id="ajax_edit_consultant_event" style="padding-left:1em;" ></div>';
		}
			
	} else {
		$displayform = '<h4>No Event Found.</h4>';
	}
	mysqli_close($dbc);
	echo $displayform;
?>