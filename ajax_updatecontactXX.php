<?php
session_start();
# ajax_updatecontact.php

	$repsid = $_SESSION['rep_id'];
	$repsvfgid = $_SESSION['vfgrep_id'];
	//called from function updateContact() on rep_manage_contacts.php
	//
	
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	include ('includes/phpfunctions.php');
	
	//DB Connection
	require_once (MYSQL);
	$errors = array();		//initialize errors array
	
	//VALIDATE FORM FIELDS
	//*******************************************************
	$contactid = $_POST['cid'];
	$inviter_id = $_POST['inviter'];
	
	//FIRST NAME
	if ( empty($_POST['first_name']) ){
		$errors[] = 'Please enter recruit\'s first name.';
	} elseif (!preg_match("/^[A-Za-z]+$/", trim($_POST['first_name']))) {
		$errors[] = 'First name contains at least 1 invalid character.';
	} else {
		$fname = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
		$fn = ucwords(strtolower($fname));
	}
	
	//LAST NAME
	if (empty($_POST['last_name']) || $_POST['last_name'] == '' ){
		//$errors[] = 'Please enter contact\'s last name.';
		$ln = '';
	} elseif (!preg_match("/^[A-Za-z -]+$/", trim($_POST['last_name']))) {
		$errors[] = 'Last name contains at least 1 invalid character.';
	} else {
		$lname = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
		$ln = ucwords(strtolower($lname));
	}
	
	//EMAIL
	//check email
	if (empty($_POST['email']) || $_POST['email'] == ''){
		//$errors[] = 'Enter contact\'s email.';
		$email = '';
	} elseif (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($_POST['email']))) {
		$errors[] = 'Enter a valid email address.';
	} else {
		$email = mysqli_real_escape_string($dbc, trim(strtolower($_POST['email'])));
		
	}
	
	//phone
	if ( empty($_POST['phone']) || $_POST['phone'] == ''){
		//$errors[] = 'Please enter contact\'s phone.';
		$phone = '';
	} elseif ( preg_match("/^\d{3}[-]\d{3}[-]\d{4}$/", trim($_POST['phone'])) || preg_match("/^\d{10}$/", trim($_POST['phone']))) {
		if (strlen(trim($_POST['phone'])) == 10) {	//no dashes ##########
			$formattedphone = trim($_POST['phone']);
			$phone = substr($formattedphone,0,3).'-'.substr($formattedphone,3,3).'-'.substr($formattedphone,6,4);
		}
		if (strlen(trim($_POST['phone'])) == 12) {	//dashes ###-###-####
			$phone = trim($_POST['phone']);
		}
	} else {
		$errors[] = 'Invalid phone number format. ###-###-#### or ##########.';
	}
	
	//City
	if (empty($_POST['city'])){
		//$errors[] = 'Please enter contact\'s city.';
		$city = '';
	} elseif (!preg_match("/^[A-Za-z ]+$/", trim($_POST['city']))) {
		$errors[] = 'City contains at least 1 invalid character.';
	} else {
		$city = mysqli_real_escape_string($dbc, ucwords(trim(strtolower($_POST['city']))));
	}
	
	//State
	if ($_POST['state'] == ""){
		//$errors[] = 'Select contact\'s state.';
		$state = '';
	} else {
		$state = strip_tags(trim($_POST['state']));
	}
	
	//Timezone
	if (empty($_POST['timezone']) || $_POST['timezone'] == ''){
		//$errors[] = 'Please select contact\'s time zone.';
		$tz = $_SESSION['rep_tz'];
	} else {
		$tz = mysqli_real_escape_string($dbc, trim($_POST['timezone']));
	}
	//Tier 
	$tier = $_POST['tier'];
	//Tier Step
	$tierstep = '';
	if ($_POST['tierstep'] == ""){
		$errors[] = 'Select recruit\'s tier step.';
	} else {
		$tierstep = strip_tags(trim($_POST['tierstep']));
	}
	$curr_tierstep = $tier.$tierstep;
	//Original Tier+Step
	$og_tierstep = $_POST['original_tier'].$_POST['original_tierstep'];
	
	//Compare current tierstep to original - set flag to reset action taken var...
	$tierstep_changed = false;
	if ($curr_tierstep != $og_tierstep){
		$tierstep_changed = true;
	}
	
	//Team Member
	$teammember = $_POST['team_member'];
	
	//Contact Type
	$contacttype = $_POST['contact_type'];
	
	//Prospect Or Customer
	$porc = $_POST['porc'];
	
	//Check: If Team Member = Y, rorc cannot be C
	if ($teammember == 'Y' && $porc == 'C'){
		$errors[] = 'Team members must be prospects. Can\'t be a customer.';
	}
	
	//next_action_date
	if (empty($_POST['next_action_date'])){
		$nd = 'empty';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['next_action_date']))) {
				$errors[] = 'Invalid action date format. Format is MM-DD-YYYY.';
	} else {
		$nad = mysqli_real_escape_string($dbc, trim($_POST['next_action_date']));
		//reformat the next_action_date (mm-dd-yyyy to yyyy-mm-dd)
		$nd = '';
		$nd = $nad;
		if ($nd != ''){
			$next_action_dt = substr($nd,6,4).'-'.substr($nd,0,2).'-'.substr($nd,3,2);
			$nd = $next_action_dt;
		}
	}
	
	//Preferred Contact Method
	$pc_phone = '';
	if (empty($_POST['pc_phone'])){
		$pc_phone = 'N';
	} else {
		$pc_phone = $_POST['pc_phone'];
	}
	$pc_text = '';
	if (empty($_POST['pc_text'])){
		$pc_text = 'N';
	} else {
		$pc_text = $_POST['pc_text'];
	}
	$pc_email = '';
	if (empty($_POST['pc_email'])){
		$pc_email = 'N';
	} else {
		$pc_email = $_POST['pc_email'];
	}
	
	
	$m_assigned_by = $_POST['original_assignedmanager'];
	$c_assigned_by = $_POST['original_assignedconsultant'];
	$a_assigned_by = $_POST['original_additionalrep'];
	$con_changed = false;
	$man_changed = false;
	$addl_changed = false;
	
	$assigned_manager = '';
	//ASSIGNED MANAGER vfgid
	if (empty($_POST['assigned_manager']) || $_POST['assigned_manager'] == ''){
		//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
	} else {
		$assigned_manager = strip_tags(trim(strtoupper($_POST['assigned_manager'])));

		if ($_POST['assigned_manager'] != $_POST['original_assignedmanager']){
			$man_changed = true;
		}
		
		//MAKE SURE ASSIGNED MANAGER VFGREPID EXISTS!
		$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$assigned_manager' LIMIT 1";
		//RUN QUERY
		$rs = @mysqli_query ($dbc, $query);
		if (mysqli_num_rows($rs) != 1) {
			//email already exists!
			$errors[] = 'Assigned Manager\'s VFG ID doesn\'t exist in our records.';
		}
		mysqli_free_result($rs);
	}
	
	$assigned_consultant = '';
	//ASSIGNED Consultant vfgid
	if (empty($_POST['assigned_consultant']) || $_POST['assigned_consultant'] == ''){
		//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
	} else {
		$assigned_consultant = strip_tags(trim(strtoupper($_POST['assigned_consultant'])));

		if ($_POST['assigned_consultant'] != $_POST['original_assignedconsultant']){
			$con_changed = true;
		}
		//MAKE SURE ASSIGNED CONSULTANT VFGREPID EXISTS!
		$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$assigned_consultant' LIMIT 1";
		//RUN QUERY
		$rs = @mysqli_query ($dbc, $query);
		if (mysqli_num_rows($rs) != 1) {
			//email already exists!
			$errors[] = 'Assigned Consultant\'s VFG ID doesn\'t exist in our records.';
		}
		mysqli_free_result($rs);
	}
	
	$additional_rep = '';
	//ASSIGNED Consultant vfgid
	if (empty($_POST['additional_rep']) || $_POST['additional_rep'] == ''){
		//$errors[] = 'Please enter your recruiter\'s VFG Rep ID.';
	} else {
		$additional_rep = strip_tags(trim(strtoupper($_POST['additional_rep'])));

		if ($_POST['additional_rep'] != $_POST['original_additionalrep']){
			$addl_changed = true;
		}
		//MAKE SURE ASSIGNED CONSULTANT VFGREPID EXISTS!
		$query = "SELECT vfgrepid FROM reps WHERE vfgrepid = '$additional_rep' LIMIT 1";
		//RUN QUERY
		$rs = @mysqli_query ($dbc, $query);
		if (mysqli_num_rows($rs) != 1) {
			//email already exists!
			$errors[] = 'Additional Rep\'s VFG ID doesn\'t exist in our records.';
		}
		mysqli_free_result($rs);
	}
	
	//Tier2 call & Inviter On Call
	//$tier2call = $_POST['original_tier2call'];
	//$inviteroncall = $_POST['original_inviteroncall'];
	$tier2call = 'N';
	$inviteroncall = 'N';
	//Change tier if checkbox is checked....form element will be passed in
	if ( isset($_POST['tier2_call']) ){
		$tier2call = $_POST['tier2_call'];
	}
	//Change inviteroncall if checkbox is checked....form element will be passed in
	if ( isset($_POST['inviter_on_call']) ){
		$inviteroncall = $_POST['inviter_on_call'];
	}
	
	//DISPOSITION
	$disposition = $_POST['disposition'];
	
	// PROSPECT SOURCE
	$psource = $_POST['prospect_source'];
	
	//ROLE - if from rep_manage_assigned_recruits.php
	//       it's part of the query string over there.
	$role = '';
	if (isset($_POST['role'])){
		$role = $_POST['role'];
	}
	
	// 7 & 30 DAY DATA
	//*****************
	/*
	//START DATE
	if (empty($_POST['dp_startdate'])){
		$dpstartdate = '';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['dp_startdate']))) {
				$errors[] = 'Invalid Start Date format. Format is MM-DD-YYYY.';
	} else {
		$startdate = mysqli_real_escape_string($dbc, trim($_POST['dp_startdate']));
		//reformat the next_action_date (mm-dd-yyyy to yyyy-mm-dd)
		$dpstartdate = '';
		$dpstartdate = substr($startdate,6,4).'-'.substr($startdate,0,2).'-'.substr($startdate,3,2);
	}
	//SEVEN DAY DATE
	if (empty($_POST['seven_day_date'])){
		$sevendaydate = '';
	} else {
		$sevendaydate = mysqli_real_escape_string($dbc, trim($_POST['seven_day_date']));
	}
	//TIER 3 DATE
	if (empty($_POST['dp_tier3_date'])){
		$tier3date = '';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['dp_tier3_date']))) {
		$errors[] = 'Invalid Tier 3 Date format. Format is MM-DD-YYYY.';
	} else {
		$t3date = mysqli_real_escape_string($dbc, trim($_POST['dp_tier3_date']));
		//reformat (mm-dd-yyyy to yyyy-mm-dd)
		$tier3date = '';
		$tier3date = substr($t3date,6,4).'-'.substr($t3date,0,2).'-'.substr($t3date,3,2);
	}
	//CRM DATE
	if (empty($_POST['dp_crm_date'])){
		$crmdate = '';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['dp_crm_date']))) {
		$errors[] = 'Invalid CRM Date format. Format is MM-DD-YYYY.';
	} else {
		$crm = mysqli_real_escape_string($dbc, trim($_POST['dp_crm_date']));
		//reformat(mm-dd-yyyy to yyyy-mm-dd)
		$crmdate = '';
		$crmdate = substr($crm,6,4).'-'.substr($crm,0,2).'-'.substr($crm,3,2);
	}
	//TIER4 DATE
	if (empty($_POST['dp_tier4_date'])){
		$tier4date = '';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['dp_tier4_date']))) {
		$errors[] = 'Invalid Tier 4 Date format. Format is MM-DD-YYYY.';
	} else {
		$t4date = mysqli_real_escape_string($dbc, trim($_POST['dp_tier4_date']));
		//reformat (mm-dd-yyyy to yyyy-mm-dd)
		$tier4date = '';
		$tier4date = substr($t4date,6,4).'-'.substr($t4date,0,2).'-'.substr($t4date,3,2);
	}
	//PLAN
	if (empty($_POST['plan'])){
		$plan = '';
	} else {
		$plan = mysqli_real_escape_string($dbc, trim($_POST['plan']));
	}
	//PERS/REF
	if (empty($_POST['pers_ref'])){
		$persref = '';
	} else {
		$persref = mysqli_real_escape_string($dbc, trim($_POST['pers_ref']));
	}
	//PLAN POINTS
	if (empty($_POST['plan_points'])){
		$planpoints = '';
	} else {
		$planpoints = mysqli_real_escape_string($dbc, trim($_POST['plan_points']));
	}
	//Plan Date
	if (empty($_POST['dp_plandate'])){
		$plandate = '';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['dp_plandate']))) {
		$errors[] = 'Invalid Plan Date format. Format is MM-DD-YYYY.';
	} else {
		$pdate = mysqli_real_escape_string($dbc, trim($_POST['dp_tier4_date']));
		//reformat (mm-dd-yyyy to yyyy-mm-dd)
		$plandate = '';
		$plandate = substr($pdate,6,4).'-'.substr($pdate,0,2).'-'.substr($pdate,3,2);
	}
	//Percent 25
	if (empty($_POST['plan_percent25'])){
		$planpercent25 = '';
	} else {
		$planpercent25 = mysqli_real_escape_string($dbc, trim($_POST['plan_percent25']));
	}
	//Promo To
	if (empty($_POST['plan_points'])){
		$planpoints = '';
	} else {
		$planpoints = mysqli_real_escape_string($dbc, trim($_POST['plan_points']));
	}
	//30 Day Date
	if (empty($_POST['thirty_day_date'])){
		$thirtydaydate = '';
	} else {
		$thirtydaydate = mysqli_real_escape_string($dbc, trim($_POST['thirty_day_date']));
	}
	//Recruit 1
	if (empty($_POST['plan_points'])){
		$planpoints = '';
	} else {
		$planpoints = mysqli_real_escape_string($dbc, trim($_POST['plan_points']));
	}
	//Recruit 1 Points
	if (empty($_POST['recruit1_points'])){
		$recruit1points = '';
	} else {
		$recruit1points = mysqli_real_escape_string($dbc, trim($_POST['recruit1_points']));
	}
	//Recruit 2
	if (empty($_POST['recruit2'])){
		$recruit2 = '';
	} else {
		$recruit2 = mysqli_real_escape_string($dbc, trim($_POST['recruit2']));
	}
	//Recruit 2 Points
	if (empty($_POST['recruit2_points'])){
		$recruit2points = '';
	} else {
		$recruit2points = mysqli_real_escape_string($dbc, trim($_POST['recruit2_points']));
	}
	//Recruits Date
	if (empty($_POST['dp_recruitsdate'])){
		$recruitsdate = '';
	}  elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['dp_recruitsdate']))) {
		$errors[] = 'Invalid Recruits Date format. Format is MM-DD-YYYY.';
	} else {
		$rdate = mysqli_real_escape_string($dbc, trim($_POST['dp_recruitsdate']));
		//reformat (mm-dd-yyyy to yyyy-mm-dd)
		$recruitsdate = '';
		$recruitsdate = substr($rdate,6,4).'-'.substr($rdate,0,2).'-'.substr($rdate,3,2);
	}
	//Recruits Percent 25
	if (empty($_POST['recruits_percent25'])){
		$recruitspercent25 = '';
	} else {
		$recruitspercent25 = mysqli_real_escape_string($dbc, trim($_POST['recruits_percent25']));
	}
	//Recruits Promo To
	if (empty($_POST['recruits_promo_to'])){
		$recruitspromoto = '';
	} else {
		$recruitspromoto = mysqli_real_escape_string($dbc, trim($_POST['recruits_promo_to']));
	}
	*/
	//*********************** END FIELD VALIDATION **************************************
	
	//Data Changed flag
	$hasDataChanged = $_POST['data_changed'];
	
	//*************** DB UPDATE **********************************
	if (empty($errors)){
		if ($hasDataChanged == 'true'){
			//rep time:
			if (isset($_SESSION['rep_tz'])){
				date_default_timezone_set($_SESSION['rep_tz']);
			}
			
			$update_dt = date("Y-m-d H:i:s");
			$tierstatus = $tier.$tierstep;
			
			// DKF 03/17/2014 take notes out of update
			//
			$updatesql = "UPDATE contacts 
				  SET firstname = '$fn', 
					  lastname = '$ln', 
					  email = '$email',
					  phone = '$phone',
					  city = '$city',
					  state = '$state',
					  timezone = '$tz',
					  tier_status = '$tierstatus',
					  update_date = '$update_dt',
					  updated_by = '$repsid',
					  team_member = '$teammember', ";
			if ($nd == 'empty'){
				$updatesql .= "next_action_date = NULL, ";
			} else {
				$updatesql .= "next_action_date = '$nd', nad_set_by = '$repsid', ";
			}
			if ($man_changed){
				$updatesql .= "man_assigned_by = '$repsvfgid', ";
			}
			if ($con_changed){
				$updatesql .= "con_assigned_by = '$repsvfgid', ";
			}
			if ($addl_changed){
				$updatesql .= "addl_assigned_by = '$repsvfgid', ";
			}
			if ($tierstep_changed){		//doing update, tierstep changed - RESET action_taken flag
				if ($role != ''){
					switch($role){
						case 'rep':
							$updatesql .= "rep_action_taken = 'N', ";
							break;
						case 'manager':
							$updatesql .= "manager_action_taken = 'N', ";
							break;
						case 'consult':
							$updatesql .= "consultant_action_taken = 'N', ";
							break;
						case 'addlrep':
							$updatesql .= "addlrep_action_taken = 'N', ";
							break;
					}
				}
			}
			if (!$tierstep_changed){	//doing update, tierstep didn't change - SET action_taken flag
				if ($role != ''){
					switch($role){
						case 'rep':
							$updatesql .= "rep_action_taken = 'Y', ";
							break;
						case 'manager':
							$updatesql .= "manager_action_taken = 'Y', ";
							break;
						case 'consult':
							$updatesql .= "consultant_action_taken = 'Y', ";
							break;
						case 'addlrep':
							$updatesql .= "addlrep_action_taken = 'Y', ";
							break;
					}
				}
			}
			$updatesql .= "contact_type = '$contacttype',
						   prospect_or_customer = '$porc',
						   assigned_manager = '$assigned_manager',
						   assigned_consultant = '$assigned_consultant',
						   had_tier2_call = '$tier2call',
						   inviter_on_call = '$inviteroncall',
						   additional_rep = '$additional_rep',
						   disposition = '$disposition', 
						   contact_source = '$psource', 
						   preferred_contact_phone = '$pc_phone',
						   preferred_contact_text = '$pc_text',
						   preferred_contact_email = '$pc_email', ";
			/*if ($dpstartdate == ''){
				$updatesql .= "start_date = NULL, ";
			} else {
				$updatesql .= "start_date = '$dpstartdate', ";
			}
			if ($sevendaydate == ''){
				$updatesql .= "seven_day_date = NULL, ";
			} else {
				$updatesql .= "seven_day_date = '$sevendaydate', ";
			}
			if ($tier3date == ''){
				$updatesql .= "tier3_date = NULL, ";
			} else {
				$updatesql .= "tier3_date = '$tier3date', ";
			}
			if ($crmdate == ''){
				$updatesql .= "crm_date = NULL, ";
			} else {
				$updatesql .= "crm_date = '$crmdate', ";
			}			
			if ($tier4date == ''){
				$updatesql .= "tier4_date = NULL, ";
			} else {
				$updatesql .= "tier4_date = '$tier4date', ";
			}			   
			if ($plandate == ''){
				$updatesql .= "plan_date = NULL, ";
			} else {
				$updatesql .= "plan_date = '$plandate', ";
			}
			if ($thirtydaydate == ''){
				$updatesql .= "thirty_day_date = NULL, ";
			} else {
				$updatesql .= "thirty_day_date = '$thirtydaydate', ";
			}
			if ($recruitsdate == ''){
				$updatesql .= "recruits_date = NULL, ";
			} else {
				$updatesql .= "recruits_date = '$recruitsdate', ";
			}
			$updatesql .= "plan = '$plan',
						   pers_ref = '$persref',
						   plan_points = '$planpoints', 
						   plan_percent25 = '$planpercent25',
						   promo_to = '$promoto', 
						   recruit1 = '$recruit1',
						   recruit1_points = '$recruit1points',
						   recruit2 = '$recruit2',
						   recruit2_points = '$recruit2points',
						   recruits_percent25 = '$recruitspercent25',
						   recruits_promo_to = '$recruitspromoto'
						WHERE contact_id = '$contactid' LIMIT 1"; 
			*/
			$updatesql .= "WHERE contact_id = '$contactid' LIMIT 1";
			//RUN UPDATE QUERY
			$rs= mysqli_query($dbc, $updatesql);
			
			if (mysqli_affected_rows($dbc) == 1){
				echo '<p>Update successful.</p>';
								
			} else {
				echo '<p>Update failed.</p>';
				echo '<p>'.mysqli_error($dbc).'</p>';
			
			}	// close mysqli_affected_rows($dbc) == 1
		} else {
			//no changes were made
			echo '<p>You made no changes to the data.</p>';
		}
		
		// After updating the record: if $tierstatus is 3F or 4F, 
		// 							  it's a missed meeting. Email the manager 
		//                            and inviter IF this rep is the consultant.
	
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p><u><font color="red"><b>ERRORS:</b></font></u><br />';
		foreach ($errors as $msg){
			echo "<br />$msg\n";
		}
		echo '</p>';
	}
	
	mysqli_close($dbc); // Close the database connection.
?>