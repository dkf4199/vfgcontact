<?php
session_start();
	//ajax_addevent_modal.php
	include ('includes/selectlists.php');
	
	$event_title = $_GET['title'];
	$event_id = $_GET['event_id'];
	$start_hh = $_GET['start_hh'];
	$start_mm = $_GET['start_mm'];
	$end_hh = $_GET['end_hh'];
	$end_mm = $_GET['end_mm'];
	$event_start_date = $_GET['event_start'];
	$event_end_date = $_GET['event_end'];
	
	$modalstr = '<div class="contact-content">
					<p>EDIT - DELETE EVENT MODAL</p>
					<p>Event ID: '.$event_id.'</p>
					<p>Title: '.$event_title.'</p>
					<p>Start: '.$event_start_date.'</p>
					<p>End: '.$event_end_date.'</p>
					<p>
						<label>Event Title:</label>
						<input name="fc_ed_event_title" id="fc_ed_event_title" type="text" size="40" maxlength="50" 
								value="'.$event_title.'" />
					</p>
					<p>
						<label>Start Time:</label>
						<select name="fc_ed_eventstart_hh" id="fc_ed_eventstart_hh" >';
						foreach($event_hh as $id=>$name){
							if($start_hh == $id){
								$sel = 'selected="selected"';
							}
							else{
								$sel = '';
							}
							$modalstr .= "<option $sel value=\"$id\">$name</option>";
						}	
					$modalstr .= '</select> :
							<select name="fc_ed_eventstart_mm" id="fc_ed_eventstart_mm" >';
							foreach($event_mm as $id=>$name){
									if($start_mm == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									$modalstr .= "<option $sel value=\"$id\">$name</option>";
								}	
					$modalstr .= '</select>
					</p>
					<p>
						<label>End Time:</label>
						<select name="fc_ed_eventend_hh" id="fc_ed_eventend_hh" >';
							foreach($event_hh as $id=>$name){
							if($end_hh == $id){
								$sel = 'selected="selected"';
							}
							else{
								$sel = '';
							}
							$modalstr .= "<option $sel value=\"$id\">$name</option>";
						}	
					$modalstr .= '</select> :
							<select name="fc_ed_eventend_mm" id="fc_ed_eventend_mm" >';
							foreach($event_mm as $id=>$name){
									if($end_mm == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									$modalstr .= "<option $sel value=\"$id\">$name</option>";
								}	
					$modalstr .= '</select>
					</p>
					<p>
						<button type="button" id="fullcalendar_editevent_button">Edit Event</button>&nbsp;&nbsp;
						<button type="button" id="fullcalendar_deleteevent_button">Delete Event</button>
						<input type="hidden" id="fc_ed_eventid" value="'.$event_id.'" />
						<input type="hidden" id="fc_ed_start_dt" value="'.$event_start_date.'" />
						<input type="hidden" id="fc_ed_end_dt" value="'.$event_end_date.'" />
					</p>
					<p>
						<div id="fc_editdelete_ajax" style="padding-left:1em;" ></div>
					</p>
				</div>';
	echo $modalstr;
?>