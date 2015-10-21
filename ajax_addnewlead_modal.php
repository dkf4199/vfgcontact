<?php
session_start();
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	require_once (MYSQL);
				
	$leadcat = $_GET['lead_cat'];
	
	$displayform = '<div style="display:none">
		<div class="contact-content">
			<h1 class="contact-title">Add New Lead</h1>
			<div class="contact-loading" style="display:none"></div>
			<br />
			<div class="leadcontentleft">
				<ul>
					<li>
						<label for="lead_category">Category:</label>
						<select name="lead_category" id="lead_category">';
					foreach($lead_category as $id=>$name){
						if($leadcat == $id){
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
						<label for="first_name">First Name:</label>
						<input type="text" id="first_name" name="first_name" class="input150 capitalwords" maxlength="30" value="" />
					</li>
					<li>
						<label for="last_name">Last Name:</label>
						<input type="text" id="last_name" name="last_name" class="input150 capitalwords" maxlength="30" value="" />
					</li>
					<li>
						<label for="phone">Phone:</label>
						<input type="text" id="phone" name="phone" class="input150 capitalwords" maxlength="15" value="" />
					</li>
					<li>
						<label for="email">Email:</label>
						<input type="text" id="email" name="email" class="input200 capitalwords" maxlength="80" value="" />
					</li>
					<li>
						<label for="lead_priority">Priority:</label>
						<select name="lead_priority" id="lead_priority">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</li>
					<li>
						<label for="lead_notes">Notes:</label>
						<textarea id="lead_notes" name="lead_notes" rows="6" cols="40"></textarea> 		
					</li>
				</ul>
				<p align="center"><input type="button" id="add_lead" class="generalbutton" value="Add Lead" /></p>
			</div> <!-- close contentleft -->
			<div class="leadcontentright">
				
			</div>
			<div class="cleardiv"></div>
		</div></div>';
	echo $displayform;
?>