<?php
session_start();
	//ajax_addevent_modal.php
	include ('includes/selectlists.php');
	
	$view = $_GET['view'];
	$slot = $_GET['slot'];
	$tdate = $_GET['t_date'];

	$modalstr = '<div class="contact-content">
					<p>Calendar View: '.$view.'</p>
					<p>Slot Clicked: '.$slot.'</p>
					<p>Day Clicked: '.$tdate.'</p>
					<p>
						<label>Event Title:</label>
						<input name="fc_event_title" id="fc_event_title" type="text" size="40" maxlength="50" 
								value="" />
					</p>
					<p>
						<label>Start Time:</label>
						<select name="fc_eventstart_hh" id="fc_eventstart_hh" >
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
							<select name="fc_eventstart_mm" id="fc_eventstart_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
					</p>
					<p>
						<label>End Time:</label>
						<select name="fc_eventend_hh" id="fc_eventend_hh" >
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
							<select name="fc_eventend_mm" id="fc_eventend_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
					</p>
					<p>
						<button type="button" id="fullcalendar_addevent_button">Add Event</button>
						<input type="hidden" id="fc_todays_date" value="'.$tdate.'" />
					</p>
					<p>
						<div id="fc_addevent_ajax" style="padding-left:1em;" ></div>
					</p>
				</div>';
	echo $modalstr;
?>