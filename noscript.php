<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
		
	$cid = $_GET['contact_id'];
	$fname = $_GET['contact_firstname'];
	$lname = $_GET['contact_lastname'];
	$repfirst = $_GET['reps_firstname'];
	$replast = $_GET['reps_lastname'];
	$repphone = $_GET['reps_phone'];
	
	$scriptstr = '<p align="center">
					<form name="noscript_form" id="noscript_form" onSubmit="return saveNoScriptPhoneNote(\''.$cid.'\');" >
					<label>Phone Call Note:</label>
					<input type="text" name="phone_note" id="phone_note" class="input250" maxlength="50" value="" />
					<input type="hidden" name="phonenote_cid" id="phonenote_cid" value="'.$cid.'" />
					<input type="submit" value="Save Note" />
					</form>
					<div id="ajax_save_note">Note msgs</div>
				  </p>';
	echo $scriptstr;
	
?>