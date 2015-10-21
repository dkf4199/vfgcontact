<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repid = $_SESSION['rep_id'];
	$fromemail = $_SESSION['rep_email'];
	
	$currcid = $_GET['currentid'];
	$fname = $_GET['currentfirstname'];
	$lname = $_GET['currentlastname'];
	$toemail = $_GET['currentemail'];
	//$fname = 'XXX';
	//$lname = 'YYY';
	
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$displayform = '<div style="display:none">
		<div class="contact-content">
		<h1 class="contact-title">3x3 Phone Call Note for '.$fname.' '.$lname.' at '.$toemail.'.</h1>
		<div class="contact-loading" style="display:none"></div>
		<div class="contact-message" style="display:none"></div>
		<br />
		<ul>
			<li>
				<label for="phone_note">Call Note:</label>
				<input type="text" name="phone_note" id="phone_note" class="input250" maxlength="30" value="" />
			</li>
			<li>
				<label>&nbsp;</label>
				<a href="'.$currcid.'" class="matrixphonelink">Save Phone Note</a>&nbsp;&nbsp;
				<a href="'.$currcid.'" class="matrixcloselink">Close</a>
				<input type="hidden" name="mailto_firstname" id="mailto_firstname" value="'.$fname.'" />
				<input type="hidden" name="mailto_lastname" id="mailto_lastname" value="'.$lname.'" />
				<input type="hidden" name="mailto_email" id="mailto_email" value="'.$toemail.'" />
				<input type="hidden" name="mailto_from" id="mailto_from" value="'.$fromemail.'" />
			</li>
		</ul>
	 </div>
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>