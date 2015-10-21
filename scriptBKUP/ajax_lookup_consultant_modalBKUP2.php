<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	
	$curr_state = $_GET['state'];
	$state_text = $_GET['statetext'];
	$fname = $_GET['c_first'];
	$lname = $_GET['c_last'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$lic_consultants = array();
	//Lookup Consultant's in contact's state
	
	$lic_field = 'lic_'.strtolower($curr_state);
	
	$q = "SELECT a.rep_id, b.vfgrepid, b.firstname, b.lastname, b.replevel_consultant
		  FROM rep_license a INNER JOIN reps b ON a.rep_id = b.rep_id
		  WHERE $lic_field = 'Y' AND b.replevel_consultant = 'Y'";
	
	$r = mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) > 0){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$lic_consultants[] = $row['vfgrepid'].':'.$row['firstname'].':'.$row['lastname'];
		}
		mysqli_free_result($r);
	}
	
	$num_reps = sizeof($lic_consultants);
	
	$displayform = '<div style="display:none">
		<div class="modal-content">
			
			<h3 class="modal-title">Consultants for '.$fname.' '.$lname.' in '.$state_text.'.  Found '.$num_reps.' licensed consultants.</h3>
			<p align="center">
				<select name="selected_consultant" id="selected_consultant">
					<option value="">Select</option>';
			
	foreach ($lic_consultants as $agent){
		// 3 pieces: vfgid(0), firstname(1), lastname(2)
		$thisagent = explode(":", $agent);
		
		$displayform .= '<option value="'.$thisagent[0].'">'.$thisagent[1].' '.$thisagent[2].'</option>';
	}
	
	$displayform .= '</select>&nbsp;&nbsp;&nbsp;
			<a href="" class="linkbutton" id="set_consultant_button">Select Consultant</a><br /><br />
			<div id="set_consultant_response" style="text-align:center;">Choose A Rep From List</div>
			</p>
		  <!-- Consultant calendar and add event divs -->
		  <div class="consultantevents">
				<label for="consultant_eventday" id="day_label" class="nowidth">Pick A Day:</label>
				<input type="text" name="consultant_eventday" id="consultantdatepicker" value="" onChange="return getConsultantEvents(this.value)" />
				<div id="consultants_schedule"></div>
		  </div>
		  <div class="addevent">
			  <h4>Add Event To Consultant\'s Calendar</h4>
				<ul>
					<li>
						<label class="blocklabel">Event title:</label>
						<input name="repadd_event_title" id="repadd_event_title" type="text" size="40" maxlength="50" 
								value="" />
					</li>
					<li>
						<label class="blocklabel">Description:</label>
						<input name="repadd_event_shortdesc" id="repadd_event_shortdesc" type="text" size="50" maxlength="100" value="" />
					</li>
					<li>
						<label class="blocklabel">Event Start (hh:mm):</label>
						<select name="repadd_event_start_hh" id="repadd_event_start_hh" >
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
							<select name="repadd_event_start_mm" id="repadd_event_start_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
					</li>
					<li>
						<label class="blocklabel">Event End (hh:mm):</label>
						<select name="repadd_event_end_hh" id="repadd_event_end_hh" >
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
							<select name="repadd_event_end_mm" id="repadd_event_end_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
							
					</li>
					<li>
						<input type="button" id="rep_add_event_to_consultant_calendar" class="generalbutton" value="Add Event" />
					</li>
				</ul>
				<div id="ajax_rep_add_consultant_event"></div>
		  </div>
		  <div class="cleardiv"></div>
			<input type="hidden" name="repadd_m" id="repadd_m" value="" >
			<input type="hidden" name="repadd_d" id="repadd_d" value="" >
			<input type="hidden" name="repadd_y" id="repadd_y" value="" >
		 </div> <!-- close modal-content -->
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>