<?php
session_start();
	$m = $_GET['month'];
	$d = $_GET['day'];
	$y = $_GET['year'];
	
	$displayform = '<h4>Add Event To My Calendar</h4>
				<ul>
					<li>
						<label class="blocklabel">Event title:</label>
						<input name="new_event_title" id="new_event_title" type="text" size="40" maxlength="50" 
								value="" />
					</li>
					<li>
						<label class="blocklabel">Description:</label>
						<input name="new_event_shortdesc" id="new_event_shortdesc" type="text" size="50" maxlength="100" value="" />
					</li>
					<li>
						<label class="blocklabel">Event Start (hh:mm):</label>
						<select name="new_event_start_hh" id="new_event_start_hh" >
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
							<select name="new_event_start_mm" id="new_event_start_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
					</li>
					<li>
						<label class="blocklabel">Event End (hh:mm):</label>
						<select name="new_event_end_hh" id="new_event_end_hh" >
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
							<select name="new_event_end_mm" id="new_event_end_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
							
					</li>
					<li>
						<input type="button" id="consultant_newevent_button" class="generalbutton" value="Add Event"
								onClick="javascript: return addNewConsultantEvent();" />
					</li>
				</ul>
				<div id="ajax_new_consultant_event" style="padding-left:1em;" ></div>';
				
	echo $displayform;

?>