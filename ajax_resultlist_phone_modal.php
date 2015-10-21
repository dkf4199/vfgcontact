<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repid = $_SESSION['rep_id'];
	$repfirst = $_SESSION['rep_firstname'];
	$replast = $_SESSION['rep_lastname'];
	$repphone = $_SESSION['rep_phone'];
	$fromemail = $_SESSION['rep_email'];
	
	$currcid = $_GET['currentcontactid'];
	$fname = $_GET['currentfirstname'];
	$lname = $_GET['currentlastname'];
	$contactphone = $_GET['contactphone'];
	
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$displayform = '<div style="display:none">
		<div class="contact-content">
		<h1 class="contact-title">Phone Call Scripts for '.$fname.' '.$lname.' ('.$contactphone.')</h1>
		<input type="hidden" id="contact_firstname" value="'.$fname.'" />
		<input type="hidden" id="contact_lastname" value="'.$lname.'" />
		<input type="hidden" id="contact_id" value="'.$currcid.'" />
		<input type="hidden" id="reps_firstname" value="'.$repfirst.'" />
		<input type="hidden" id="reps_lastname" value="'.$replast.'" />
		<input type="hidden" id="reps_phone" value="'.$repphone.'" />
		<input type="hidden" id="contacts_phone" value="'.$contactphone.'" />
		<br />
		<div class="scriptdivbottom">
			<ul>
				<li>
					<label for="script_list">Phone Script:</label>
					<select name="script_list" id="script_list" onChange="javascript: getPhoneScript(this.value);">
						<option value="4">No Script</option>
						<option value="1">Intro Script</option>
						<option value="2">Call After Video</option>
						<option value="3">2nd Call Attempt</option>
					</select>
					&nbsp;<a onclick="callAjax( \''.$contactphone.'\');" class="phonelist_modal" href="#">
										<img src="./images/smallicons/remotedial.png" class="phonelink"/></a>
				</li>
			</ul>
			<div id="script_section">
				<p align="center">
					<form name="noscript_form" id="noscript_form" onSubmit="return saveNoScriptPhoneNote(\''.$currcid.'\');" >
					<label>Phone Call Note:</label>
					<input type="text" name="phone_note" id="phone_note" class="input250" maxlength="50" value="" />
					<input type="hidden" name="phonenote_cid" id="phonenote_cid" value="'.$currcid.'" />
					<input type="submit" value="Save Note" />
					</form>
					<div id="ajax_save_note">Note msgs</div>
				  </p>
			</div>
		</div>
		<div class="cleardiv"></div>
	 </div>
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>