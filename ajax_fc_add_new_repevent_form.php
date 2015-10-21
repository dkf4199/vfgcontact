<?php
session_start();
	$m = $_GET['month'];
	$d = $_GET['day'];
	$y = $_GET['year'];
	
	$displayform = '<h4>Add Event To My Calendar</h4>
				<ul>
					<li>
						<label class="blocklabel">Event title:</label>
						<input name="newlink_event_title" id="newlink_event_title" type="text" size="40" maxlength="50" 
								value="" />
					</li>
					<li>
						<label class="blocklabel">Description:</label>
						<input name="newlink_event_desc" id="newlink_event_desc" type="text" size="50" maxlength="100" value="" />
					</li>
					<li>
						<label class="blocklabel">Event Start (hh:mm):</label>
						<select name="newlink_event_start_hh" id="newlink_event_start_hh" >
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
							<select name="newlink_event_start_mm" id="newlink_event_start_mm" >
							<option value="00">00</option>
							<option value="15">15</option>
							<option value="30">30</option>
							<option value="45">45</option>
							</select>
					</li>
					<li>
						<label class="blocklabel">Event End (hh:mm):</label>
						<select name="newlink_event_end_hh" id="newlink_event_end_hh" >
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
							<select name="newlink_event_end_mm" id="newlink_event_end_mm" >
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
						<input type="checkbox" name="newlink_repeats_checkbox" id="newlink_repeats_checkbox" value="1" onClick="return toggleDiv();"/>
					</li>
					<div id="recurring_eventdiv" style="display:none;">
					<li>
						<label class="blocklabel">Repeat Until:</label>
						<input type="text" name="newlink_recurring_event_end_dp" id="newlink_recurring_event_end_dp" value="" />
					</li>
					<li>
						<label class="blocklabel">Repeat Every:</label>
						day <input type="radio" value="1" name="newlink_repeat_freq" align="bottom" checked>
						week <input type="radio" value="7" name="newlink_repeat_freq" align="bottom">
						two weeks <input type="radio" value="14" name="newlink_repeat_freq" align="bottom">
					</li>
					</div>
					<li>
						<input type="button" id="rep_newevent_button" class="generalbutton" value="Add Event"
								onClick="javascript: return addNewRepEvent();" />
					</li>
				</ul>
				<div id="ajax_new_consultant_event" style="padding-left:1em;" ></div>';
				
	echo $displayform;

?>