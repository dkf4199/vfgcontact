<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	
	$mgr_vfgid = $_GET['mgr_vfgid'];			//manager's vfgid
	$cid = $_GET['c_id'];						//contacts id
	$c_fname = $_GET['c_first'];			//contacts firstname
	$c_lname = $_GET['c_last'];			//contacts lastname
	
	$mgr_first = $mgr_last = $mgr_tz = '';
	
	include ('includes/selectlists.php');
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	
	// GET MANAGER'S DATA
	$q = "SELECT rep_id, vfgrepid, firstname, lastname, rep_timezone
		  FROM reps
		  WHERE vfgrepid = '$mgr_vfgid' LIMIT 1";
	
	$r = mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) > 0){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$_SESSION['assigned_manager_repid'] = $row['rep_id'];
			$mgr_first = $row['firstname'];
			$mgr_last = $row['lastname'];
			$mgr_tz = getStandardTZname($row['rep_timezone']);
		}
		mysqli_free_result($r);
	}
	
	
	
	$displayform = '<div style="display:none">
		<div class="modal-content">
			<h3 class="modal-title">Assigned Manager for '.$c_fname.' '.$c_lname.'</h3>
			<div id="set_consultant_response" style="text-align:center;">Assigned Manager:  '.$mgr_first.' '.$mgr_last.'</div>
			<div id="set_consultant_timezone" style="text-align:center;">Manager\'s Timezone:  '.$mgr_tz.'</div>
			<!-- Consultant calendar and add event divs -->
		  <div class="consultantevents">
				<label for="manager_eventday" id="day_label" class="nowidth">Pick A Day:</label>
				<input type="text" name="manager_eventday" id="managerdatepicker" value="" onChange="return getManagersEvents(this.value)" />
				<div id="consultants_schedule"></div>
		  </div>
		  <div class="addevent">
			  <h4>Add Event To Manager\'s Calendar</h4>
				<ul>
					<li>
						<label class="blocklabel">Event title:</label>
						<input name="repadd_event_title" id="repadd_event_title" type="text" size="40" maxlength="50" 
								value="Follow-Up w/'.$c_fname.' '.$c_lname.'" />
					</li>
					<li>
						<label class="blocklabel">Description:</label>
						<input name="repadd_event_shortdesc" id="repadd_event_shortdesc" type="text" size="50" maxlength="100" 
							value="Set by '.$_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].' '.$_SESSION['vfgrep_id'].' '.$_SESSION['rep_phone'].'" />
					</li>
					<li>
						<label class="blocklabel">Event Start (hh:mm):</label>
						<select name="repadd_event_start_hh" id="repadd_event_start_hh" >
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
							<select name="repadd_event_end_mm" id="repadd_event_end_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
							
					</li>
					<li>
						<input type="button" id="rep_add_event_to_managers_calendar" class="generalbutton" value="Add Event" />
					</li>
				</ul>
				<div id="ajax_rep_add_manager_event"></div>
		  </div>
		  <div class="cleardiv"></div>
			<input type="hidden" name="repadd_m" id="repadd_m" value="" >
			<input type="hidden" name="repadd_d" id="repadd_d" value="" >
			<input type="hidden" name="repadd_y" id="repadd_y" value="" >
			<input type="hidden" name="repadd_cid" id="repadd_cid" value="'.$cid.'" >
		 </div> <!-- close modal-content -->
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>