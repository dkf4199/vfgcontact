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
	
	$displayform .= '</div> <!-- close .consultantevents div -->
			
			<div class="addevent">
				<h4>Add Event To My Calendar</h4>
				<ul>
					<li>
						<label class="blocklabel">Event title:</label>
						<input name="event_title" id="event_title" type="text" size="40" maxlength="50" 
								value="" />
					</li>
					<li>
						<label class="blocklabel">Description:</label>
						<input name="event_desc" id="event_desc" type="text" size="50" maxlength="100" value="" />
					</li>
					<li>
						<label class="blocklabel">Event Start (hh:mm):</label>
						<select name="event_start_hh" id="event_start_hh" >
							<option value="01">1 am</option>
							<option value="02">2 am</option>
							<option value="03">3 am</option>
							<option value="04">4 am</option>
							<option value="05">5 am</option>
							<option value="06">6 am</option>
							<option value="07">7 am</option>
							<option value="08">8 am</option>
							<option value="09">9 am</option>
							<option value="10">10 am</option>
							<option value="11">11 am</option>
							<option value="12">12 pm</option>
							<option value="13">1 pm</option>
							<option value="14">2 pm</option>
							<option value="15">3 pm</option>
							<option value="16">4 pm</option>
							<option value="17">5 pm</option>
							<option value="18">6 pm</option>
							<option value="19">7 pm</option>
							<option value="20">8 pm</option>
							<option value="21">9 pm</option>
							<option value="22">10 pm</option>
							<option value="23">11 pm</option>
							<option value="24">12 am</option>
							</select> :
							<select name="event_start_mm" id="event_start_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
					</li>
					<li>
						<label class="blocklabel">Event End (hh:mm):</label>
						<select name="event_end_hh" id="event_end_hh" >
							<option value="01">1 am</option>
							<option value="02">2 am</option>
							<option value="03">3 am</option>
							<option value="04">4 am</option>
							<option value="05">5 am</option>
							<option value="06">6 am</option>
							<option value="07">7 am</option>
							<option value="08">8 am</option>
							<option value="09">9 am</option>
							<option value="10">10 am</option>
							<option value="11">11 am</option>
							<option value="12">12 pm</option>
							<option value="13">1 pm</option>
							<option value="14">2 pm</option>
							<option value="15">3 pm</option>
							<option value="16">4 pm</option>
							<option value="17">5 pm</option>
							<option value="18">6 pm</option>
							<option value="19">7 pm</option>
							<option value="20">8 pm</option>
							<option value="21">9 pm</option>
							<option value="22">10 pm</option>
							<option value="23">11 pm</option>
							<option value="24">12 am</option>
							</select> :
							<select name="event_end_mm" id="event_end_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
							
					</li>
					<li>
						&nbsp;
					</li>
					<li>
						<label>Recurring Event:</label>
						<input type="checkbox" name="repeats_checkbox" id="repeats_checkbox" value="1" onClick="return toggleDiv();"/>
					</li>
					<div id="recurring_eventdiv" style="display:none;">
					<li>
						<label class="blocklabel">Repeat Until:</label>
						<input type="text" name="recurring_event_end_dp" id="recurring_event_end_dp" value="" />
					</li>
					<li>
						<label class="blocklabel">Repeat Every:</label>
						day <input type="radio" value="1" name="repeat_freq" align="bottom" checked>
						week <input type="radio" value="7" name="repeat_freq" align="bottom">
						two weeks <input type="radio" value="14" name="repeat_freq" align="bottom">
					</li>
					</div>
					<li>
						<input type="button" id="rep_addevent_button" class="generalbutton" value="Add Event" />
					</li>
				</ul>
				<div id="ajax_add_consultant_event" style="padding-left:1em;" ></div>
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