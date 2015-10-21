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
					<a href="#" onClick="javascript: return displayConsultantAddEvent();" >New</a></p>';
			
	// Show the events for this day:
	//MySQL date_format: %l is hour, %i is the minutes, %p is am/pm
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
	
	$displayform .= '</div>
			
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
						<input name="event_shortdesc" id="event_shortdesc" type="text" size="50" maxlength="100" value="" />
					</li>
					<li>
						<label class="blocklabel">Event Start (hh:mm):</label>
						<select name="event_start_hh" id="event_start_hh" >
							<option value="1">1 am</option>
							<option value="2">2 am</option>
							<option value="3">3 am</option>
							<option value="4">4 am</option>
							<option value="5">5 am</option>
							<option value="6">6 am</option>
							<option value="7">7 am</option>
							<option value="8">8 am</option>
							<option value="9">9 am</option>
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
						<option value="1">1 am</option>
							<option value="2">2 am</option>
							<option value="3">3 am</option>
							<option value="4">4 am</option>
							<option value="5">5 am</option>
							<option value="6">6 am</option>
							<option value="7">7 am</option>
							<option value="8">8 am</option>
							<option value="9">9 am</option>
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
						<input type="button" id="consultant_addevent_button" class="generalbutton" value="Add Event" />
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