<?php
session_start();
	//************************************************************************
	// CALLED:  When the event header is clicked on the calendar.  EventClick
	//   DATA:  Need to pass id, title, description, start, end
	//************************************************************************
	
	$repid = $_SESSION['rep_id'];
	
	$m = $_GET['month'];
	$d = $_GET['day'];
	$y = $_GET['year'];
	
	$e_title = $_GET['title'];
	$e_desc = $_GET['description'];
	$e_id = $_GET['event_id'];
	$start_hh = $_GET['start_hh'];
	$start_mm = $_GET['start_mm'];
	$end_hh = $_GET['end_hh'];
	$end_mm = $_GET['end_mm'];
	$event_start_date = $_GET['event_start'];
	$event_end_date = $_GET['event_end'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	
	$displayform = '<div style="display:none">
		<div class="modal-content">
			
			<h3 class="modal-title">Events For '.$m.'-'.$d.'-'.$y.'</h3>
			
			<!-- Two Divs, Left displays events - Right is form to add -->
			<div class="consultantevents">
				<p><strong>Today\'s Events:</strong><br />
					<a href="#" onClick="javascript: return displayRepAddEvent();" >New</a></p>';
			
	// Show the REP's events for this day:
	//MySQL date_format: %l is hour, %i is the minutes, %p is am/pm
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
			$event_title = stripslashes($ev['title']);
			$event_desc = stripslashes($ev['description']);
			$fmt_start = $ev['fmt_start'];
			$fmt_end = $ev['fmt_end'];
			$eid = $ev['id'];
			$displayform .= '<li><strong>'.$fmt_start.' to '.$fmt_end.'</strong>.  '.
					  $event_title.' - '.$event_desc.
					  '<br /><a href="#" onClick="javascript: return displayRepEventInfo(\''.$repid.'\',\''.$eid.'\');" >Edit</a>&nbsp;
					  <a href="#" onClick="javascript: return displayRepEventDeleteInfo(\''.$repid.'\',\''.$eid.'\');">Delete</a></li>';
		}
		$displayform .= '</ul>';
		mysqli_free_result($r);
	} 
	
	$displayform .= '</div> <!-- close .consultantevents div -->
			
			<div class="addevent">
				<h4>Edit Event</h4>
				<ul>
					<li>
						<label class="blocklabel">Event title:</label>
						<input name="edit_event_title" id="edit_event_title" type="text" size="40" maxlength="100" 
								value="'.$e_title.'" />
					</li>
					<li>
						<label class="blocklabel">Description:</label>
						<input name="edit_event_desc" id="edit_event_desc" type="text" size="50" maxlength="100" value="'.$e_desc.'" />
					</li>
					<li>
							<label class="blocklabel">Event Start (hh:mm):</label>
							<select name="edit_event_start_hh" id="edit_event_start_hh" >';
						foreach($event_hh as $id=>$name){
							if($start_hh == $id){
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
						if($start_mm == $id){
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
							if($end_hh == $id){
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
						if($end_mm == $id){
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
						<input type="hidden" id="editevent_fc_id" value="'.$e_id.'" />
					</li>
					</ul>
					<div id="ajax_edit_consultant_event" style="padding-left:1em;" ></div>
			</div>
			<div class="cleardiv"></div>
			<input type="hidden" name="evt_m" id="evt_m" value="'.$m.'" >
			<input type="hidden" name="evt_d" id="evt_d" value="'.$d.'" >
			<input type="hidden" name="evt_y" id="evt_y" value="'.$y.'" >
			
		 </div> <!-- close modal-content -->
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>