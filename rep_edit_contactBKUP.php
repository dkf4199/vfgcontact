<?php
session_start();
// If no agent session value is present, redirect the user:
if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {
	require_once ('./includes/phpfunctions.php');
	$url = absolute_url('rep_login.php');
	//javascript redirect using window.location
	echo '<script language="Javascript">';
	echo 'window.location="' . $url . '"';
	echo '</script>';
	exit();	
}
$assigned_role = '';
if (isset($_GET['role'])) {
	$assigned_role = $_GET['role'];
}
if (isset($_GET['cid'])) {
	$_SESSION['get_contactid'] = $_GET['cid'];
	$currcid = $_GET['cid'];
}
if (isset($_GET['fl']) and $_GET['fl'] == 'first') {
	$_SESSION['get_firstload'] = 'first';
}
if (isset($_GET['s'])) {
	$_SESSION['get_s'] = $_GET['s'];
	$start = $_GET['s'];
} else {
	$start = 0;
}
if (isset($_GET['p'])) {
	$_SESSION['get_p'] = $_GET['p'];
}
// src flag - from calendar
if ( isset($_GET['fromsrc']) && $_GET['fromsrc'] == 'fccal' ) {
	$_SESSION['from_page'] = 'rep_fc.php';
	$_SESSION['back_btn_text'] = 'Back To Calendar';
	$_SESSION['srch_string'] = '';
}

$repsid = $_SESSION['rep_id'];
$repsvfgid = $_SESSION['vfgrep_id'];

include ('includes/selectlists.php');
include ('includes/phpfunctions.php');
include ('includes/config.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>VFG Contacts - Edit Contact</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/button.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/pure.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/tcs_modal_handlers.js'></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
<script>
$(window).bind('beforeunload', function(){

	var dataChanged = false;
		
	var firstName = $("#first_name").val();
	var ogFirstName = $("#original_firstname").val();
	var lastName = $("#last_name").val();
	var ogLastName = $("#original_lastname").val();
	var email = $("#email").val();
	var ogEmail = $("#original_email").val();
	var phone = $("#phone").val();
	var ogPhone = $("#original_phone").val();
	var city = $("#city").val();
	var ogCity = $("#original_city").val();
	var state = $("#state :selected").val();
	var ogState = $("#original_state").val();
	var timeZone = $("#timezone :selected").val();
	var ogTimeZone = $("#original_timezone").val();
	var tier = $("input:radio[name=tier]:checked").val();
	var ogTier = $("#original_tier").val();
	var tierStep = $("#tierstep :selected").val();
	var ogTierStep = $("#original_tierstep").val();
	var teamMember = $("#team_member :selected").val();
	var ogTeamMember = $("#original_teammember").val();
	var contactType = $("#contact_type :selected").val();
	var ogContactType = $("#original_contacttype").val();
	var porc = $("#porc :selected").val();
	var ogPorc = $("#original_porc").val();
	//var notes = $("#notes").val();
	//var ogNotes = $("#original_notes").val();
	var disposition = $("#disposition :selected").val();
	var ogDisposition = $("#og_disposition").val();
	var contactSource = $("#prospect_source :selected").val();
	var ogContactSource = $("#og_prospectsource").val();
	var nextActionDate = $("#datepicker").val();
	var ogNextActionDate = $("#original_nextactiondate").val();
	var assignedMgr = $("#assigned_manager").val();
	var ogAssignedMgr = $("#original_assignedmanager").val();
	var assignedConsult = $("#assigned_consultant").val();
	var ogAssignedConsult = $("#original_assignedconsultant").val();
	//var ogTier2Call = $("#original_tier2call").val();
	//var ogInviterOnCall = $("#original_inviteroncall").val();
	var additionalRep = $("#additional_rep").val();
	var ogAdditionalRep = $("#original_additionalrep").val();
	
	var ogPCphone = $("#og_pcphone").val();
	var ogPCtext = $("#og_pctext").val();
	var ogPCemail = $("#og_pcemail").val();
	
	// CHECK Preferred Contact CHECKBOXES
	var pc_phone = 'N';
	var pc_text = 'N';
	var pc_email = 'N';
	//Check box state
	if ( $('#pc_phone').prop("checked") ) {
		pc_phone = 'Y';
	}
	if ( $('#pc_text').prop("checked") ) {
		pc_text = 'Y';
	}
	if ( $('#pc_email').prop("checked") ) {
		pc_email = 'Y';
	}
	
	//var tier2call = ogTier2Call;
	//var inviterOnCall = ogInviterOnCall;
	//CHECK TO SEE IF CHECKBOXES ARE THERE for manager
	/*if ( $('#tier2_call').prop("checked") ) {
		tier2call = 'Y';
	}
	if ( $('#inviter_on_call').prop("checked") ) {
		inviterOnCall = 'Y';
	}*/
	
	//compare original vals from what's on screen
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	if (firstName.toLowerCase() != ogFirstName.toLowerCase()) {dataChanged = true;}
	if (lastName.toLowerCase() != ogLastName.toLowerCase()) {dataChanged = true;}
	if (email != ogEmail) {dataChanged = true;}
	if (phone != ogPhone) {dataChanged = true;}
	if (city != ogCity) {dataChanged = true;}
	if (state != ogState) {dataChanged = true;}
	if (timeZone != ogTimeZone) {dataChanged = true;}
	if (tier != ogTier) {dataChanged = true;}
	if (tierStep != ogTierStep) {dataChanged = true;}
	if (teamMember != ogTeamMember) {dataChanged = true;}
	if (contactType != ogContactType) {dataChanged = true;}
	if (porc != ogPorc) {dataChanged = true;}
	//if (notes != ogNotes) {dataChanged = true;}
	if (nextActionDate != ogNextActionDate) {dataChanged = true;}
	if (assignedMgr.toLowerCase() != ogAssignedMgr.toLowerCase()) {dataChanged = true;}
	if (assignedConsult.toLowerCase() != ogAssignedConsult.toLowerCase()) {dataChanged = true;}
	//if (tier2call != ogTier2Call) {dataChanged = true;}
	//if (inviterOnCall != ogInviterOnCall) {dataChanged = true;}
	if (additionalRep != ogAdditionalRep) {dataChanged = true;}
	if (disposition != ogDisposition) {dataChanged = true;}
	if (contactSource != ogContactSource) {dataChanged = true;}
	if (pc_phone != ogPCphone) {dataChanged = true;}
	if (pc_text != ogPCtext) {dataChanged = true;}
	if (pc_email != ogPCemail) {dataChanged = true;}
	
	//alert(dataChanged.toString());
	if (dataChanged) {
		$("#update_button").removeClass("generalbuttongreen");
		$("#update_button").addClass("generalbuttonred");
		return "You have unsaved changes on this page.";
		
	}
  
});
</script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<!-- Take Menu off the edit page, it goes here if needed -->
		<?php
			date_default_timezone_set($_SESSION['rep_tz']);
			//DB Connection
			require_once (MYSQL);
			
			//Pull the INVITER and the assigned by fields from contacts record
			// INVITER
			$invitername = '';
			$inviterphone = '';
			$inviteremail = '';
			$q = "SELECT b.firstname, b.lastname, b.gmail_acct, b.phone
				  FROM contacts a
				  INNER JOIN reps b
				  ON a.rep_id = b.rep_id
				  WHERE a.contact_id = '$currcid'";
			$r = mysqli_query($dbc, $q);
			if ($r){
				if (mysqli_num_rows($r) > 0){
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						$invitername = $row['firstname'].' '.$row['lastname'];
						$inviterphone = $row['phone'];
						$inviteremail = $row['gmail_acct'];
					}
				}
				mysqli_free_result($r);
			}
						
			//GET CONTACTS RECORD
			//*****************************************************************
			$hist = '';
			$type = '';
			$contacttime = '';
			// Make query to pull 3x3 COMMUNICATION HISTORY (need cid and repid)
			//
			// 02-07-2014 dkf Other reps can't see comm history that
			//			  weren't added by them.  Take off the repid=
			//            in the WHERE clause.
			//
			/*$cq = "SELECT comm_type, comm_note, comm_date
				  FROM communication_matrix
				  WHERE rep_id='$repsid'
				  AND contact_id='$currcid'
				  ORDER BY comm_date DESC ";
			*/
			$cq = "SELECT a.comm_type, a.comm_note, a.comm_date,
				          CONCAT(b.firstname,' ',b.lastname) as comm_name
				  FROM communication_matrix a
				  INNER JOIN reps b
				  ON a.rep_id = b.rep_id
				  WHERE contact_id='$currcid'
				  ORDER BY comm_date DESC ";
			$cr = mysqli_query($dbc, $cq);
			if ($cr){
				if (mysqli_num_rows($cr) > 0){
					while ($row = mysqli_fetch_array($cr, MYSQLI_ASSOC)) {
					
						switch ($row['comm_type']){
							case 'EM':
								$type = 'EMAIL';
								break;
							case 'PC':
								$type = 'PHONE CALL';
								break;
							case 'TM':
								$type = 'TEXT MSG';
								break;
						}
						//Format comm_date
						$commdt = strtotime( $row['comm_date'] );
						$formatted_commdt = date( 'm-d-Y h:i:s a', $commdt );
						$hist.= $formatted_commdt.' '.$type."\n".'BY:  '.$row['comm_name']."\n".$row['comm_note']."\n\n";
					}
				}
				mysqli_free_result($cr);
			}
			//************** END COMM HISTORY RETRIEVAL ******************************************************
			
			// CHRONOLOGICAL NOTES FOR CONTACT from notes table
			$note_arr = array();
			$note_str = '';
			$notes_sql = "SELECT rep_first, rep_last, note, note_date
				  FROM notes
				  WHERE contact_id='$currcid'
				  ORDER BY note_date DESC ";
			$nr = mysqli_query($dbc, $notes_sql);
			if ($nr){
				if (mysqli_num_rows($nr) > 0){
					while ($row = mysqli_fetch_array($nr, MYSQLI_ASSOC)) {
					
						//Format note_date
						$notedt = strtotime( $row['note_date'] );
						$formatted_notedt = date( 'm-d-Y h:i:s a', $notedt );
						$note_str .= $formatted_notedt."\n".'By: '.$row['rep_first'].' '.$row['rep_last']."\n".$row['note']."\n\n";
					}
				}
				mysqli_free_result($nr);
			}
			//************** END NOTES RETRIEVAL *************************************************************
			
			//COMM MATRIX AND NOTES - TEMP TABLE CHRONOLOGICAL EXTRACT
			$comm_note_str = '';
			$msg_type = '';
			//$commnotes_sql = "drop temporary table if exists $temp_table; ".
			$commnotes_sql = "SELECT rep_id, contact_id,' ' as 'repfirst', ' ' as 'replast', 
									 comm_type as 'type', comm_note as 'commnote', comm_date as 'entrydate'
								FROM communication_matrix
								WHERE contact_id = '$currcid'
								union
								SELECT rep_id, contact_id, rep_first as 'repfirst', rep_last as 'rep_last',
									  'NOTE' as 'type', note as 'commnote', note_date as 'entrydate'
								FROM notes
								WHERE contact_id = '$currcid'

								ORDER BY entrydate DESC ";
			$cn = mysqli_query($dbc, $commnotes_sql);
			if ($cn){
				if (mysqli_num_rows($cn) > 0){
					while ($row = mysqli_fetch_array($cn, MYSQLI_ASSOC)) {
						
						switch ($row['type']){
							case 'EM':
								$type = 'EMAIL';
								break;
							case 'PC':
								$type = 'PHONE CALL';
								break;
							case 'TM':
								$type = 'TEXT MSG';
								break;
							case 'NOTE':
								$type = 'NOTE ENTRY';
								break;
						}
						//Format note_date
						$commnotedt = strtotime( $row['entrydate'] );
						$formatted_commnotedt = date( 'm-d-Y h:i:s a', $commnotedt );
						$comm_note_str .= $formatted_commnotedt.' '.$type."\n";
						if ( $row['repfirst'] != ' ' ){
							$comm_note_str .= 'By: '.$row['repfirst'].' '.$row['replast']."\n";
						}
						$comm_note_str .= $row['commnote']."\n\n";
					}
				}
				mysqli_free_result($cn);
			}
			
			//************END MATRIX/NOTES TEMP TABLE CHRONO EXTRACT******************************************
			
			// BASE DATA RETRIEVAL - FROM CONTACTS
			//*****************************************************************************************
			$fn = $ln = $em = $ph = $city = $st = $tzone = $tier = $tierstep = $mab = $cab = $aab = '';
			$notes = $rid = $updt = $inviter = $tm = $nad = $ct = $poc = $am = $ac = $t2c = $ioc = $ar = '';
			$reptime = '';
			$disp = $pc_phone = $pc_text = $pc_email = $latest_scheduled = $psource = '';
			$start_date = $seven_day_date = $tier3date = $crmdate = $tier4date = '';
			$plan_name = $pers_ref = $plan_points = $plan_date = $plan_percent25 = $promo_to = '';
			$thirty_day_date = $recruit1 = $recruit1points = $recruit2 = $recruit2points = '';
			$recruits_date = $recruits_percent25 = $recruits_promo_to = '';
			$spouse_name = $anniversary_date = '';
			// Make the query to pick the contact data:
			$q = "SELECT firstname, lastname, email, phone,
						city, state, timezone, tier_status, 
						notes, update_date, rep_id, team_member, next_action_date,
						contact_type, prospect_or_customer, assigned_manager, assigned_consultant,
						had_tier2_call, inviter_on_call, additional_rep, 
						man_assigned_by, con_assigned_by, addl_assigned_by, disposition,
						preferred_contact_phone, preferred_contact_text, preferred_contact_email,
						last_scheduled_tiercall, contact_source, start_date, tier3_date, crm_date,
						tier4_date, plan_date, recruits_date, seven_day_date, thirty_day_date, 
						plan, pers_ref, plan_points, plan_percent25, promo_to, recruit1, 
						recruit1_points, recruit2, recruit2_points, recruits_percent25, 
						recruits_promo_to, spouse_name, anniversary_date 
				  FROM contacts
				  WHERE contact_id = '$currcid' LIMIT 1";
			$rs = mysqli_query ($dbc, $q); // Run the query.
			if ($rs){
				if (mysqli_num_rows($rs) == 1) {
					while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						$fn = $row['firstname'];
						$_SESSION['policy_account_firstname'] = $row['firstname'];
						$ln = $row['lastname'];
						$_SESSION['policy_account_lastname'] = $row['lastname'];
						$em = $row['email'];
						$ph = $row['phone'];
						$city = $row['city'];
						$st = $row['state'];
						$tzone = $row['timezone'];
						//display timezone data
						//rep time:
						date_default_timezone_set($_SESSION['rep_tz']);
						$reptime = date("h:i:s a");
						//contact time:
						if ($row['timezone'] != ''){
							date_default_timezone_set($row['timezone']);
							$contacttime = date("h:i:s a");
						} else {
							date_default_timezone_set($_SESSION['rep_tz']);
							$contacttime = date("h:i:s a");
						}
						//split the tier_status
						$tier = substr($row['tier_status'],0,1);
						$tierstep = substr($row['tier_status'],1,1);
						$notes = $row['notes'];
						$rid = $row['rep_id'];
						//Format the update_date
						if ($row['update_date'] != ''){
							$updatedt = strtotime( $row['update_date'] );
							$formatted_updatedt = date( 'm-d-Y h:i:s a', $updatedt );
						} else {
							$formatted_updatedt = '';
						}
						$inviter = $row['rep_id'];
						$tm = $row['team_member'];
						//reformat the next_action_date (yyyy-mm-dd to mm-dd-yyyy)
						$nxt = '';
						$nxt = $row['next_action_date'];
						if ($nxt != ''){
							//$next_action_dt = substr($nad,5,2).'-'.substr($nad,8,2).'-'.substr($nad,0,4);
							$nad = mysql_date_to_display($nxt);
						}
						//reformat the start date (yyyy-mm-dd to mm-dd-yyyy)
						$sdt = '';
						$sdt = $row['start_date'];
						if ($sdt != ''){
							$start_date = mysql_date_to_display($sdt);
						}
						//reformat the Tier3 date (yyyy-mm-dd to mm-dd-yyyy)
						$t3dt = '';
						$t3dt = $row['tier3_date'];
						if ($t3dt != ''){
							$tier3date = mysql_date_to_display($t3dt);
						}
						//reformat the CRM date (yyyy-mm-dd to mm-dd-yyyy)
						$crmdt = '';
						$crmdt = $row['crm_date'];
						if ($crmdt != ''){
							$crmdate = mysql_date_to_display($crmdt);
						}
						//reformat the Tier4 date (yyyy-mm-dd to mm-dd-yyyy)
						$t4dt = '';
						$t4dt = $row['tier4_date'];
						if ($t4dt != ''){
							$tier4date = mysql_date_to_display($t4dt);
						}
						//reformat the Plan date (yyyy-mm-dd to mm-dd-yyyy)
						$pldt = '';
						$pldt = $row['plan_date'];
						if ($pldt != ''){
							$plan_date = mysql_date_to_display($pldt);
						}
						//reformat the Recruits date (yyyy-mm-dd to mm-dd-yyyy)
						$rcdt = '';
						$rcdt = $row['recruits_date'];
						if ($rcdt != ''){
							$recruits_date = mysql_date_to_display($rcdt);
						}
						//reformat the Seven Day date (yyyy-mm-dd to mm-dd-yyyy)
						$svdt = '';
						$svdt = $row['seven_day_date'];
						if ($svdt != ''){
							$seven_day_date = mysql_date_to_display($svdt);
						}
						//reformat the Thirty Day date (yyyy-mm-dd to mm-dd-yyyy)
						$thdt = '';
						$thdt = $row['thirty_day_date'];
						if ($thdt != ''){
							$thirty_day_date = mysql_date_to_display($thdt);
						}
						//reformat the Anniversary date (yyyy-mm-dd to mm-dd-yyyy)
						$avdt = '';
						$avdt = $row['anniversary_date'];
						if ($avdt != ''){
							$anniversary_date = mysql_date_to_display($avdt);
						}
						$spouse_name = $row['spouse_name'];
						$plan_name = $row['plan'];
						$pers_ref = $row['pers_ref']; 
						$plan_points = $row['plan_points']; 
						$plan_percent25 = $row['plan_percent25']; 
						$promo_to = $row['promo_to']; 
						$recruit1 = $row['recruit1']; 
						$recruit1points = $row['recruit1_points']; 
						$recruit2 = $row['recruit2']; 
						$recruit2points = $row['recruit2_points']; 
						$recruits_percent25 = $row['recruits_percent25']; 
						$recruits_promo_to = $row['recruits_promo_to'];
						$ct = $row['contact_type'];
						$poc = $row['prospect_or_customer'];
						$am = $row['assigned_manager'];
						$ac = $row['assigned_consultant'];
						$t2c = $row['had_tier2_call'];
						$ioc = $row['inviter_on_call'];
						$ar = $row['additional_rep'];
						$mab = $row['man_assigned_by'];
						$cab = $row['con_assigned_by'];
						$aab = $row['addl_assigned_by'];
						$disp = $row['disposition'];
						$pc_phone = $row['preferred_contact_phone'];
						$pc_text = $row['preferred_contact_text'];
						$pc_email = $row['preferred_contact_email'];
						$latest_scheduled = $row['last_scheduled_tiercall'];
						$psource = $row['contact_source'];
					}
				}
				mysqli_free_result($rs);
			}
			
			// ASSIGNED MANAGER
			$man_name = '';
			$man_phone = '';
			$man_email = '';
			//Grab ASSIGNED MANAGER if field has data
			if ($am <> ''){
				//get the manager's name
				$q = "SELECT firstname, lastname, phone, gmail_acct
					  FROM reps
					  WHERE vfgrepid = '$am' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$man_name = $row['firstname'].' '.$row['lastname'];
							$man_phone = $row['phone'];
							$man_email = $row['gmail_acct'];
						}
					}
					mysqli_free_result($rs);
				}
				
			}
			
			// MANAGER ASSIGNED BY
			$man_assigned_by = '';
			//Grab manager assigned by if field has data
			if ($mab <> ''){
				//get the manager's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$mab' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$man_assigned_by = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}	
			}
			
			// ASSIGNED CONSULTANT
			$con_name = '';
			$con_phone = '';
			$con_email = '';
			//Grab assigned consultant if field has data
			if ($ac <> ''){
				//get the consultant's name
				$q = "SELECT firstname, lastname, phone, gmail_acct
					  FROM reps
					  WHERE vfgrepid = '$ac' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$con_name = $row['firstname'].' '.$row['lastname'];
							$con_phone = $row['phone'];
							$con_email = $row['gmail_acct'];
						}
					}
					mysqli_free_result($rs);
				}	
			}
			
			// CONSULTANT ASSIGNED BY
			$con_assigned_by = '';
			//Grab CONSULTANT ASSIGNED BY - if field has data
			if ($cab <> ''){
				//get the person's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$cab' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$con_assigned_by = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}	
			}
			
			// ADDITIONAL REP
			$addl_rep_name = '';
			//Grab additional rep if field has data
			if ($ar <> ''){
				//get the additional rep's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$ar' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$addl_rep_name = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}
				
			}
			
			//Grab Scheduled Call
			$thistier = '';
			$scheduled_call = '';
			//get data from 
			$q = "SELECT tier, date_format(scheduled_meeting, '%m-%d-%Y %l:%i %p') as meeting_date
				  FROM tiercall_meetings
				  WHERE contact_id = '$currcid' LIMIT 1";
			$rs = mysqli_query ($dbc, $q); // Run the query.
			if ($rs){
				if (mysqli_num_rows($rs) == 1) {
					while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						$thistier = $row['tier'];
						$scheduled_call = $row['meeting_date'];
					}
				}
				mysqli_free_result($rs);
			}
				
			
			
			mysqli_close($dbc); // Close the database connection.
		?>
		
		<!-- CONTACT INFO FROM RECORD AND LOCAL TIME -->
		<div id="contact_edit_infobox" class="opacity95">
			<p>
				<b>Inviter:</b><br /><?php echo $invitername; ?><br /><?php echo $inviterphone; ?><br /><?php echo $inviteremail; ?>
			</p>
			<p>
				<b>Manager:</b><br /><?php echo $man_name; ?><br /><?php echo $man_phone; ?><br /><?php echo $man_email; ?>
				
			</p>
			<p>
				<b>Consultant:</b><br /><?php echo $con_name; ?><br /><?php echo $con_phone; ?><br /><?php echo $con_email; ?>
				
			</p>
			<p>
				<b>Manager Assigned By:</b><br />  <?php echo $man_assigned_by; ?>
			</p>
			<p>
				<b>Consultant Assigned By:</b><br /> <?php echo $con_assigned_by; ?>
			</p>
			<p>
				<b>Your Local Time:</b><br />
				<?php echo $reptime; ?>
			</p>
			<p>
				<b>Prospect's Local Time:</b><br />
				<?php echo $contacttime; ?>
			</p>
		</div>
		
		<!-- EDIT DATA MIDDLE DIV -->
		<div class="editcontactbox roundcorners opacity95">
			<div class="editcontactform">
				<!-- EDIT HEADER -->
				<div class="editdivclientname">Edit <?php echo $fn.' '.$ln.'.'; ?></div>
				
				<!-- "BACK TO" BUTTON -->
				<div class="backtodiv">
					<form action="<?php echo $_SESSION['from_page']; ?>" method="GET" >
						<input type="hidden" name="s" value="<?php echo $start; ?>" />
						<input type="hidden" name="srch" value="<?php echo $_SESSION['srch_string']; ?>" />
						<input type="submit" id="back_to_list" class="backtobutton" value="<?php echo $_SESSION['back_btn_text']; ?>" />
					</form>
				</div>
				<div class="cleardiv"></div>
		
				<div style="text-align:center;">
					<form action="rep_edit_contact.php" method="GET">
						<input type="hidden" name="s" value="<?php echo $start; ?>" />
						<input type="hidden" name="cid" value="<?php echo $currcid; ?>" />
						<input type="submit" id="refresh_rec" class="backtobutton" value="Refresh Screen" />
					</form>
				</div>
				<form name="edit_contact_record" id="edit_contact_record" onSubmit="return updateContact();" class="pure-form pure-form-stacked" >
					<p align="center"><input type="submit" id="update_button" class="generalbuttongreen" value="Update Record" /></p>
					<fieldset>
						<legend>Personal Data</legend>
						
						<div class="pure-g">
							
							<div class="pure-u-1 pure-u-1-4">
								<!-- First name -->
								<label for="first_name">First Name:</label>
								<input type="text" id="first_name" name="first_name" class="input150 capitalwords" maxlength="30"
										value="<?php echo $fn; ?>" />
							</div>
							<div class="pure-u-1 pure-u-1-4">
								<!-- Last name -->
								<label for="last_name">Last Name:</label>
								<input type="text" id="last_name" name="last_name" class="input150 capitalwords" maxlength="45"  
									value="<?php echo $ln; ?>" />
							</div>
							<div class="pure-u-1 pure-u-1-4">
								<!-- Email-->
								<label for="email">Email:</label>
								<input type="text" id="email" name="email" class="input175" maxlength="80"
									value="<?php echo $em; ?>" />&nbsp;
								<a href="<?php echo $currcid; ?>" class="email_modal_main"><img src="./images/smallicons/mail-closed.png" /></a>
							</div>
						
							<div class="pure-u-1 pure-u-1-4">
								<!-- phone -->
								<label>Phone:</label>
								<input type="text" name="phone" id="phone" class="input100"
											value="<?php echo $ph; ?>" />&nbsp;
								<a href="<?php echo $currcid; ?>" class="phone_modal_main">
									<img src="./images/smallicons/cellphone1.png" />
								</a>
								<a href="<?php echo $currcid; ?>" class="text_modal_main">
									<img src="./images/smallicons/text.png" />
								</a>
							</div>
							<br /><br />
							<div class="pure-u-1 pure-u-1-3">
								<!-- City -->
								<label for="city">City:</label>
								<input type="text" id="city" name="city" class="input125" maxlength="40"  
									value="<?php echo $city; ?>" />
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- State -->
								<label for="state">State:</label>
								<?php
									$selected_state = "";
									$selected_state = $st;
									
									echo '<select name="state" id="state" class="pure-input-1-2">';
								
									foreach($states as $id=>$name){
										if($selected_state == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
								</select>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- Timezone -->
								<label for="timezone">Timezone:</label>
								<?php
								$selected_tz = "";
								$selected_tz = $tzone;

								echo '<select id="timezone" name="timezone" >';
									
								foreach($americaTimeZones as $id=>$name){
									if($selected_tz == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</div>
							
							<div class="pure-u-1 pure-u-1-4">
								<!-- Spouse name -->
								<label for="spouse_name">Spouse Name:</label>
								<input type="text" id="spouse_name" name="spouse_name" class="input150 capitalwords" maxlength="50"
										value="<?php echo $spouse_name; ?>" />
							</div>
							<div class="pure-u-1 pure-u-1-4">
								<label for="dp_anniversary_date">Anniversary Date:</label>
								<input type="text" name="dp_anniversary_date" id="dp_anniversary_date" value="<?php echo $anniversary_date; ?>" />
							</div>
							<div class="pure-u-1 pure-u-1-4">
							</div>
							<div class="pure-u-1 pure-u-1-4">
							</div>
							
						</div> <!-- Close div class="pure-g" -->	
					</fieldset>
					
					<fieldset>
						<legend>Business Data</legend>
						
						<div class="pure-g">	
							<div class="pure-u-1 pure-u-1-3">
								<!-- Tier -->
								<label class="pure-radio">Tier:</label>
								<input type="radio" name="tier" id="tier1" value="1" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '1' ? 'checked' : ''); ?> /><span>1</span>
								<input type="radio" name="tier" id="tier2" value="2" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '2' ? 'checked' : ''); ?> /><span>2</span>
								<input type="radio" name="tier" id="tier3" value="3" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '3' ? 'checked' : ''); ?> /><span>3</span>
								<input type="radio" name="tier" id="tier4" value="4" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '4' ? 'checked' : ''); ?> /><span>4</span>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- Prospect or Customer -->
								<label for="porc">Prospect/Customer:</label>
								<?php
								$selected_porc = "";
								$selected_porc = $poc;
								
								echo '<select name="porc" id="porc">';
							
								foreach($prospect_or_customer as $id=>$name){
									if($selected_porc == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- Contact Type -->
								<label for="contact_type">Prospect Type:</label>
								<?php
								$selected_contacttype = "";
								$selected_contacttype = $ct;
								
								echo '<select name="contact_type" id="contact_type">';
							
								foreach($direct_indirect as $id=>$name){
									if($selected_contacttype == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- STEP -->
								<?php
									//get the tierstep array based on the tier level
									$tierarray = '';
									switch ($tier){
										case '1':
											$tierarray = $tier1steps;
											break;
										case '2':
											$tierarray = $tier2steps;
											break;
										case '3':
											$tierarray = $tier3steps;
											break;
										case '4':
											$tierarray = $tier4steps;
											break;
									}
									echo '<td>
										<label for="tierstep">Status:</label>
											<select name="tierstep" id="tierstep" onChange="javascript: setStatus(this.value);">';
									foreach($tierarray as $id=>$name){
										if($tierstep == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
								</select>
								<?php
									//DROP MAIL ICON NEXT TO STATUS DROPDOWN IF THIS IS THE CONSULTANT
									if ($repsvfgid == $ac){
										echo '&nbsp;<a href="#" class="missed_meeting_email"><img src="./images/smallicons/mail-closed.png" /></a>';
									}
								?>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- Team Member -->
								<label for="team_member">Team Member:</label>
								<?php
								$selected_teammember = "";
								$selected_teammember = $tm;
								
								echo '<select name="team_member" id="team_member">';
							
								foreach($yes_or_no as $id=>$name){
									if($selected_teammember == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- DISPOSITION -->
								<label for="disposition">Disposition:</label>
								<?php
								$selected_disposition = "";
								$selected_disposition = $disp;
								
								echo '<select name="disposition" id="disposition">';
							
								foreach($disposition as $id=>$name){
									if($selected_disposition == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
								<!-- Disposition ICON to display MODAL of percentages -->
								<a href="<?php echo $repsid; ?>" class="disposition_percentages_modal">
									<img src="./images/smallicons/heirarchy.png" />
								</a>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- PROSPECT SOURCE -->
								<label for="prospect_source">Prospect Source:</label>
								<?php
								$selected_psource = "";
								$selected_psource = $psource;
								
								echo '<select name="prospect_source" id="prospect_source">';
							
								foreach($contact_source as $id=>$name){
									if($selected_psource == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- Next Action Date -->
								<label for="next_action_date">Next Action Date:</label>
								<input type="text" name="next_action_date" id="datepicker" value="<?php echo $nad; ?>" />
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<!-- Preferred Contact Method -->
								<?php
									//set the checked values for each event level...
									$phone_checked = $text_checked = $email_checked = '';
									if ($pc_phone == 'Y') {
										$phone_checked = ' checked="checked" ';
									}
									if ($pc_text == 'Y') {
										$text_checked = ' checked="checked" ';
									}
									if ($pc_email == 'Y') {
										$email_checked = ' checked="checked" ';
									}
								?>
								<label>Preferred Contact Method:</label>
								<input type="checkbox" name="pc_phone" id="pc_phone" value="Y" <?php echo $phone_checked; ?>>Phone&nbsp;&nbsp;
								<input type="checkbox" name="pc_text" id="pc_text" value="Y" <?php echo $text_checked; ?>>Text&nbsp;&nbsp;
								<input type="checkbox" name="pc_email" id="pc_email" value="Y" <?php echo $email_checked; ?>>Email
								
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								
							</div>
							
						</div> <!-- Close div class="pure-g" -->	
					</fieldset>
					
					<fieldset>
						<legend>Manager - Consultant - Additional Rep</legend>
						<div class="pure-g">
							<div class="pure-u-1 pure-u-1-4">
								<!-- ASSIGNED MANAGER -->
								<label for="assigned_manager">Manager VFG ID:</label>
								<input type="text" id="assigned_manager" name="assigned_manager" class="input100 capitalwords" maxlength="5"
										onChange="assignedLookup(this.value, 'mgr')" value="<?php echo $am; ?>" />
								<?php
									// MANAGER FIELD HAS TO BE POPULATED TO GET TO MANAGER CALENDAR MODAL
									if ( $am != '' ){
										echo '<a href="" class="lookup_manager_calendar_modal"><img src="./images/smallicons/find.png" /></a>';
									} else {
										//echo '<br />';
									}
								?>
								<span id="assigned_mgr" class="assigned_rep"><?php echo $man_name; ?></span>
							</div>
						
							<div class="pure-u-1 pure-u-1-4">
								<!-- MANAGER ONLY - tier 2 call and inviter on call -->
								
								<?php
									//determine where scheduled tiercall info is coming from
									$this_tiercall = '';
									if ($latest_scheduled != ''){
										$this_tiercall = $latest_scheduled;
									} elseif (($thistier != '' && $scheduled_call != '')){
										$this_tiercall = 'Tier '.$thistier.' on '.$scheduled_call;
									} else {
										$this_tiercall = '';
									}
								?>
								<label>Scheduled Call:</label>
								<input type="text" class="input175" value="<?php echo $this_tiercall; ?>" readonly />
							</div>
							
							<div class="pure-u-1 pure-u-1-4">
								<!-- Assigned Consultant -->
								<label for="assigned_consultant">Consultant VFG ID:</label>
								<input type="text" id="assigned_consultant" name="assigned_consultant" class="input100 capitalwords" maxlength="5"  
									onChange="assignedLookup(this.value, 'con')" value="<?php echo $ac; ?>" readonly />
								<?php
									// MANAGERS AND CONSULTANTS ONLY : get lookup consultant icon
									if ( ($repsvfgid == $am) || ($repsvfgid == $ac) ){
										echo '<a href="" class="lookup_consultant_modal"><img src="./images/smallicons/find.png" /></a>';
									} else {
										//echo '<br />';
									}
								?>
								<span id="assigned_con" class="assigned_rep"><?php echo $con_name; ?></span>
							</div>
							
							<div class="pure-u-1 pure-u-1-4">
								<!-- Additional Rep VFGID -->
								<label for="additional_rep">Additional Rep ID:</label>
								<input type="text" id="additional_rep" name="additional_rep" class="input100 capitalwords" maxlength="5"
										onChange="assignedLookup(this.value, 'adr')" value="<?php echo $ar; ?>" />
								<span id="addl_rep" class="assigned_rep"><?php echo $addl_rep_name; ?></span>
							</div>
						</div>
					</fieldset>
					
					<fieldset>
						<legend>History & Notes: (Click on the pen below to add note.)</legend>
						<div class="pure-g">
							<div class="pure-u-1 pure-u-1-2">
								<!-- Notes -->
								<label for="notes">Notes:</label>
								<textarea id="notes" name="notes" rows="5" cols="60" maxlength="300" ></textarea>
								<!-- Pen ICON next to notes field -->
								<img src="./images/smallicons/pen.png" title="Add Note" id="add_note" />
								<img src="./images/smallicons/eraser.png" title="Clear Note" id="erase_note" />
								<span id="add_note_msgs"></span>
							</div>
							<?php
									// Add old notes field from contact's record to the new notes string
									//$note_str .= "\n\n"."MISC NOTES:\n".$notes;
									$comm_note_str .= "\n\n"."MISC NOTES:\n".$notes;
								?>
							<div class="pure-u-1 pure-u-1-2">
								<!-- Notes -->
									<label for="notes">Communication History:</label>
									<textarea id="notes_history" name="notes_history" rows="12" cols="50" readonly><?php echo stripslashes($comm_note_str); ?></textarea>
							</div>
						</div>
					</fieldset>
					
					<fieldset>
						<legend>7 & 30 Day</legend>
						
						<div class="pure-g">
						
							<!-- Start Date -->
							<div class="pure-u-1">
								<label for="dp_startdate">Start Date:</label>
								<input type="text" name="dp_startdate" id="dp_startdate" value="<?php echo $start_date; ?>" />
							</div>
							
							<!-- 7 Day Date -->
							<div class="pure-u-1 pure-u-1-3"></div>
							<div class="pure-u-1 pure-u-1-3">
								<label for="seven_day_date">7 Day Date:</label>
								<input type="text" name="seven_day_date" id="seven_day_date" value="<?php echo $seven_day_date; ?>" readonly />
							</div>
							
							<div class="pure-u-1 pure-u-1-3"></div>
							
							<!-- Tier 3 Date -->
							<div class="pure-u-1 pure-u-1-2">
								<label for="dp_tier3_date">Tier 3 Date:</label>
								<input type="text" name="dp_tier3_date" id="dp_tier3_date" value="<?php echo $tier3date; ?>" />
							</div>
							<!-- CRM Date -->
							<div class="pure-u-1 pure-u-1-2">
								<label for="dp_crm_date">CRM Date:</label>
								<input type="text" name="dp_crm_date" id="dp_crm_date" value="<?php echo $crmdate; ?>" />
							</div>
							
							<!-- Tier 4 Date -->
							<div class="pure-u-1">
								<label for="dp_tier4_date">Tier 4 Date:</label>
								<input type="text" name="dp_tier4_date" id="dp_tier4_date" value="<?php echo $tier4date; ?>" />
							</div>
							
							<!-- Plan - PERS/REF - Plan Points -->
							<div class="pure-u-1 pure-u-1-3">
								<label for="plan_name">Plan:</label>
								<input type="text" name="plan_name" id="plan_name" value="<?php echo $plan_name; ?>"  maxlength="50" />
							</div>
							<div class="pure-u-1 pure-u-1-3">
								<label for="pers_ref">Pers/Ref:</label>
								<input type="text" name="pers_ref" id="pers_ref" value="<?php echo $pers_ref; ?>"  maxlength="20" />
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<label for="plan_points">Points:</label>
								<input type="text" name="plan_points" id="plan_points" value="<?php echo $plan_points; ?>" />
							</div>
							
							<!-- Plan Date - 25% - Promo To -->
							<div class="pure-u-1 pure-u-1-3">
								<label for="dp_plandate">Plan Date:</label>
								<input type="text" name="dp_plan_date" id="dp_plan_date" value="<?php echo $plan_date; ?>" />
							</div>
							<div class="pure-u-1 pure-u-1-3">
								<label for="plan_percent25">25%:</label>
								<input type="text" name="plan_percent25" id="plan_percent25" value="<?php echo $plan_percent25; ?>"  maxlength="5" />
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<label for="promo_to">Promo To:</label>
								<input type="text" name="promo_to" id="promo_to" value="<?php echo $promo_to; ?>"  maxlength="45" />
							</div>
							
							<!-- 30 Day Date -->
							<div class="pure-u-1 pure-u-1-3"></div>
							<div class="pure-u-1 pure-u-1-3">
								<label for="thirty_day_date">30 Day Date:</label>
								<input type="text" name="thirty_day_date" id="thirty_day_date" value="<?php echo $thirty_day_date; ?>" readonly />
							</div>					
							<div class="pure-u-1 pure-u-1-3"></div>
							
							<!-- Recruit 1 -->
							<div class="pure-u-1 pure-u-1-2">
								<label for="recruit1">Recruit 1:</label>
								<input type="text" name="recruit1" id="recruit1" value="<?php echo $recruit1; ?>" maxlength="45"/>
							</div>
							<!-- Recruit 1 Points -->
							<div class="pure-u-1 pure-u-1-2">
								<label for="recruit1_points">Recruit 1 Points:</label>
								<input type="text" name="recruit1_points" id="recruit1_points" value="<?php echo $recruit1points; ?>"  maxlength="5" />
							</div>
							
							<!-- Recruit 2 -->
							<div class="pure-u-1 pure-u-1-2">
								<label for="recruit2">Recruit 2:</label>
								<input type="text" name="recruit2" id="recruit2" value="<?php echo $recruit2; ?>"  maxlength="45"/>
							</div>
							<!-- Recruit 1 Points -->
							<div class="pure-u-1 pure-u-1-2">
								<label for="recruit2_points">Recruit 2 Points:</label>
								<input type="text" name="recruit2_points" id="recruit2_points" value="<?php echo $recruit2points; ?>"  maxlength="5" />
							</div>
							
							<!-- Recruit Date - Recruit 25% - Recruit Promo To -->
							<div class="pure-u-1 pure-u-1-3">
								<label for="dp_recruitsdate">Recruit Date:</label>
								<input type="text" name="dp_recruits_date" id="dp_recruits_date" value="<?php echo $recruits_date; ?>" />
							</div>
							<div class="pure-u-1 pure-u-1-3">
								<label for="recruits_percent25">25%:</label>
								<input type="text" name="recruits_percent25" id="recruits_percent25" value="<?php echo $recruits_percent25; ?>"  maxlength="5" />
							</div>
							
							<div class="pure-u-1 pure-u-1-3">
								<label for="recruits_promo_to">Promo To:</label>
								<input type="text" name="recruits_promo_to" id="recruits_promo_to" value="<?php echo $recruits_promo_to; ?>"  maxlength="45" />
							</div>
							
							
						</div>
					</fieldset>
					
					<fieldset>
						<legend>Policies</legend>
						
						<div class="pure-g">
						
							<div class="pure-u-1 pure-u-1-2">
								<?php
								//View or View/Edit Policy button under notes.  One is modal, other is separate page.
								if ($repsvfgid == $ac){
									echo '<td><a href="rep_edit_policies.php?s='.$start.'&cid='.$currcid.'&fn='.$fn.'&ln='.$ln.'&iv='.$inviter.'&am='.$am.'&ac='.$ac.'" class="myButton policy_edit">View/Edit Policies</a></td>';
								} else {
									echo '<td><a href="#" class="myButton policy_modal">View Policies</a></td>';
								}
								?>
							</div>
							
							
							<div class="pure-u-1 pure-u-1-2"></div>
							
							<div class="pure-u-1 pure-u-1-2"></div>
							
							<div class="pure-u-1 pure-u-1-2"></div>
						
						</div> <!-- Close div class="pure-g" -->
					</fieldset>
					
					
					<input type="hidden" name="submitted" value="updatecontact" />
					<input type="hidden" id="original_firstname" name="original_firstname" value="<?php echo $fn; ?>" />
					<input type="hidden" id="original_lastname" name="original_lastname" value="<?php echo $ln; ?>" />
					<input type="hidden" id="original_email" name="original_email" value="<?php echo $em; ?>" />
					<input type="hidden" id="original_phone" name="original_phone" value="<?php echo $ph; ?>" />
					<input type="hidden" id="original_city" name="original_city" value="<?php echo $city; ?>" />
					<input type="hidden" id="original_state" name="original_state" value="<?php echo $st; ?>" />
					<input type="hidden" id="original_timezone" name="original_timezone" value="<?php echo $tzone; ?>" />
					<input type="hidden" id="og_spouse_name" name="og_spouse_name" value="<?php echo $spouse_name; ?>" />
					<input type="hidden" id="og_anniversary_date" name="og_anniversary_date" value="<?php echo $anniversary_date; ?>" />
					<input type="hidden" id="original_tier" name="original_tier" value="<?php echo $tier; ?>" />
					<input type="hidden" id="original_tierstep" name="original_tierstep" value="<?php echo $tierstep; ?>" />
					<input type="hidden" id="original_teammember" name="original_teammember" value="<?php echo $tm; ?>" />
					<input type="hidden" id="original_notes" name="original_notes" value="<?php echo $notes; ?>" />
					<input type="hidden" id="original_nextactiondate" name="original_nextactiondate" value="<?php echo $nad; ?>" />
					<input type="hidden" id="original_porc" name="original_porc" value="<?php echo $poc; ?>" />
					<input type="hidden" id="original_contacttype" name="original_contacttype" value="<?php echo $ct; ?>" />
					<input type="hidden" id="original_assignedmanager" name="original_assignedmanager" value="<?php echo $am; ?>" />
					<input type="hidden" id="original_assignedconsultant" name="original_assignedconsultant" value="<?php echo $ac; ?>" />
					<input type="hidden" id="original_tier2call" name="original_tier2call" value="<?php echo $t2c; ?>" />
					<input type="hidden" id="original_inviteroncall" name="original_inviteroncall" value="<?php echo $ioc; ?>" />
					<input type="hidden" id="original_additionalrep" name="original_additionalrep" value="<?php echo $ar; ?>" />
					<input type="hidden" id="og_disposition" name="og_disposition" value="<?php echo $disp; ?>" />
					<input type="hidden" id="og_prospectsource" name="og_prospectsource" value="<?php echo $psource; ?>" />
					<input type="hidden" id="og_pcphone" name="og_pcphone" value="<?php echo $pc_phone; ?>" />
					<input type="hidden" id="og_pctext" name="og_pctext" value="<?php echo $pc_text; ?>" />
					<input type="hidden" id="og_pcemail" name="og_pcemail" value="<?php echo $pc_email; ?>" />
					<input type="hidden" id="og_startdate" name="og_startdate" value="<?php echo $start_date; ?>" />
					<input type="hidden" id="og_seven_day_date" name="og_seven_day_date" value="<?php echo $seven_day_date; ?>" />
					<input type="hidden" id="og_tier3_date" name="og_tier3_date" value="<?php echo $tier3date; ?>" />
					<input type="hidden" id="og_crm_date" name="og_crm_date" value="<?php echo $crmdate; ?>" />
					<input type="hidden" id="og_tier4_date" name="og_tier4_date" value="<?php echo $tier4date; ?>" />
					<input type="hidden" id="og_plan_name" name="og_plan_name" value="<?php echo $plan_name; ?>" />
					<input type="hidden" id="og_pers_ref" name="og_pers_ref" value="<?php echo $pers_ref; ?>" />
					<input type="hidden" id="og_plan_points" name="og_plan_points" value="<?php echo $plan_points; ?>" />
					<input type="hidden" id="og_plan_date" name="og_plan_date" value="<?php echo $plan_date; ?>" />
					<input type="hidden" id="og_plan_percent25" name="og_plan_percent25" value="<?php echo $plan_percent25; ?>" />
					<input type="hidden" id="og_promo_to" name="og_promo_to" value="<?php echo $promo_to; ?>" />
					<input type="hidden" id="og_thirty_day_date" name="og_thirty_day_date" value="<?php echo $thirty_day_date; ?>" />
					<input type="hidden" id="og_recruit1" name="og_recruit1" value="<?php echo $recruit1; ?>" />
					<input type="hidden" id="og_recruit1_points" name="og_recruit1_points" value="<?php echo $recruit1points; ?>" />
					<input type="hidden" id="og_recruit2" name="og_recruit2" value="<?php echo $recruit2; ?>" />
					<input type="hidden" id="og_recruit2_points" name="og_recruit2_points" value="<?php echo $recruit2points; ?>" />
					<input type="hidden" id="og_recruits_date" name="og_recruits_date" value="<?php echo $recruits_date; ?>" />
					<input type="hidden" id="og_recruits_percent25" name="og_recruits_percent25" value="<?php echo $recruits_percent25; ?>" />
					<input type="hidden" id="og_recruits_promo_to" name="og_recruits_promo_to" value="<?php echo $recruits_promo_to; ?>" />
					<input type="hidden" id="hidden_seven_day" name="hidden_seven_day" value="" />
					<input type="hidden" id="hidden_thirty_day" name="hidden_thirty_day" value="" />
					<input type="hidden" id="data_changed" name="data_changed" value="false" />
					<input type="hidden" id="cid" name="cid" value="<?php echo $currcid; ?>" />
					<input type="hidden" id="inviter" name="inviter" value="<?php echo $inviter; ?>" />
					<input type="hidden" id="set_tier" name="set_tier" value="<?php echo $tier; ?>" />
					<input type="hidden" id="set_status" name="set_status" value="<?php echo $tierstep; ?>" />
					<input type="hidden" id="role" name="role" value="<?php echo $assigned_role; ?>" />
					<input type="hidden" id="hidden_seven_day" name="hidden_seven_day" value="" />
					<input type="hidden" id="hidden_thirty_day" name="hidden_thirty_day" value="" />
					<!--<input type="submit" class="generalbutton" value="Update" /><br />-->
					<!--<div id="ajax_verify_update"></div>-->
				</form>
					
			</div> <!-- close editcontactform -->
		</div>	<!-- close editcontactbox -->
		<div id="update_messages" class="opacity95">Edit Messages:</div>
		<div class="cleardiv"></div>
		
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html><?php
session_start();
// If no agent session value is present, redirect the user:
if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {
	require_once ('./includes/phpfunctions.php');
	$url = absolute_url('rep_login.php');
	//javascript redirect using window.location
	echo '<script language="Javascript">';
	echo 'window.location="' . $url . '"';
	echo '</script>';
	exit();	
}
$assigned_role = '';
if (isset($_GET['role'])) {
	$assigned_role = $_GET['role'];
}
if (isset($_GET['cid'])) {
	$_SESSION['get_contactid'] = $_GET['cid'];
	$currcid = $_GET['cid'];
}
if (isset($_GET['fl']) and $_GET['fl'] == 'first') {
	$_SESSION['get_firstload'] = 'first';
}
if (isset($_GET['s'])) {
	$_SESSION['get_s'] = $_GET['s'];
	$start = $_GET['s'];
} else {
	$start = 0;
}
if (isset($_GET['p'])) {
	$_SESSION['get_p'] = $_GET['p'];
}

$repsid = $_SESSION['rep_id'];
$repsvfgid = $_SESSION['vfgrep_id'];

include ('includes/selectlists.php');
include ('includes/phpfunctions.php');
include ('includes/config.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>VFG Contacts - Edit Contact</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/button.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.3.0/pure-min.css">
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/tcs_modal_handlers.js'></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
<script>
$(window).bind('beforeunload', function(){

	var dataChanged = false;
		
	var firstName = $("#first_name").val();
	var ogFirstName = $("#original_firstname").val();
	var lastName = $("#last_name").val();
	var ogLastName = $("#original_lastname").val();
	var email = $("#email").val();
	var ogEmail = $("#original_email").val();
	var phone = $("#phone").val();
	var ogPhone = $("#original_phone").val();
	var city = $("#city").val();
	var ogCity = $("#original_city").val();
	var state = $("#state :selected").val();
	var ogState = $("#original_state").val();
	var timeZone = $("#timezone :selected").val();
	var ogTimeZone = $("#original_timezone").val();
	var tier = $("input:radio[name=tier]:checked").val();
	var ogTier = $("#original_tier").val();
	var tierStep = $("#tierstep :selected").val();
	var ogTierStep = $("#original_tierstep").val();
	var teamMember = $("#team_member :selected").val();
	var ogTeamMember = $("#original_teammember").val();
	var contactType = $("#contact_type :selected").val();
	var ogContactType = $("#original_contacttype").val();
	var porc = $("#porc :selected").val();
	var ogPorc = $("#original_porc").val();
	var notes = $("#notes").val();
	var ogNotes = $("#original_notes").val();
	var disposition = $("#disposition :selected").val();
	var ogDisposition = $("#og_disposition").val();
	var nextActionDate = $("#datepicker").val();
	var ogNextActionDate = $("#original_nextactiondate").val();
	var assignedMgr = $("#assigned_manager").val();
	var ogAssignedMgr = $("#original_assignedmanager").val();
	var assignedConsult = $("#assigned_consultant").val();
	var ogAssignedConsult = $("#original_assignedconsultant").val();
	var ogTier2Call = $("#original_tier2call").val();
	var ogInviterOnCall = $("#original_inviteroncall").val();
	var additionalRep = $("#additional_rep").val();
	var ogAdditionalRep = $("#original_additionalrep").val();
	
	var tier2call = ogTier2Call;
	var inviterOnCall = ogInviterOnCall;
	//CHECK TO SEE IF CHECKBOXES ARE THERE for manager
	if ( $('#tier2_call').prop("checked") ) {
		tier2call = 'Y';
	}
	if ( $('#inviter_on_call').prop("checked") ) {
		inviterOnCall = 'Y';
	}
	
	//compare original vals from what's on screen
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	if (firstName.toLowerCase() != ogFirstName.toLowerCase()) {dataChanged = true;}
	if (lastName.toLowerCase() != ogLastName.toLowerCase()) {dataChanged = true;}
	if (email != ogEmail) {dataChanged = true;}
	if (phone != ogPhone) {dataChanged = true;}
	if (city != ogCity) {dataChanged = true;}
	if (state != ogState) {dataChanged = true;}
	if (timeZone != ogTimeZone) {dataChanged = true;}
	if (tier != ogTier) {dataChanged = true;}
	if (tierStep != ogTierStep) {dataChanged = true;}
	if (teamMember != ogTeamMember) {dataChanged = true;}
	if (contactType != ogContactType) {dataChanged = true;}
	if (porc != ogPorc) {dataChanged = true;}
	if (notes != ogNotes) {dataChanged = true;}
	if (nextActionDate != ogNextActionDate) {dataChanged = true;}
	if (assignedMgr.toLowerCase() != ogAssignedMgr.toLowerCase()) {dataChanged = true;}
	if (assignedConsult.toLowerCase() != ogAssignedConsult.toLowerCase()) {dataChanged = true;}
	if (tier2call != ogTier2Call) {dataChanged = true;}
	if (inviterOnCall != ogInviterOnCall) {dataChanged = true;}
	if (additionalRep != ogAdditionalRep) {dataChanged = true;}
	if (disposition != ogDisposition) {dataChanged = true;}
	
	//alert(dataChanged.toString());
	if (dataChanged) {
		$("#update_button").removeClass("generalbuttongreen");
		$("#update_button").addClass("generalbuttonred");
		return "You have unsaved changes on this page.";
		
	}
  
});
</script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<!-- Take Menu off the edit page, it goes here if needed -->
		<?php
			date_default_timezone_set($_SESSION['rep_tz']);
			//DB Connection
			require_once (MYSQL);
			
			//Pull the inviter and the assigned by fields from contacts record
			// Inviter
			$invitername = '';
			$q = "SELECT b.firstname, b.lastname
				  FROM contacts a
				  INNER JOIN reps b
				  ON a.rep_id = b.rep_id
				  WHERE a.contact_id = '$currcid'";
			$r = mysqli_query($dbc, $q);
			if ($r){
				if (mysqli_num_rows($r) > 0){
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						$invitername = $row['firstname'].' '.$row['lastname'];
					}
				}
				mysqli_free_result($r);
			}
						
			//GET CONTACTS RECORD
			//*****************************************************************
			$hist = '';
			$type = '';
			// Make query to pull 3x3 history (need cid and repid)
			$cq = "SELECT comm_type, comm_note, comm_date
				  FROM communication_matrix
				  WHERE rep_id='$repsid'
				  AND contact_id='$currcid'
				  ORDER BY comm_date DESC ";
			$cr = mysqli_query($dbc, $cq);
			if ($cr){
				if (mysqli_num_rows($cr) > 0){
					while ($row = mysqli_fetch_array($cr, MYSQLI_ASSOC)) {
					
						switch ($row['comm_type']){
							case 'EM':
								$type = 'EMAIL';
								break;
							case 'PC':
								$type = 'PHONE CALL';
								break;
							case 'TM':
								$type = 'TEXT MSG';
								break;
						}
						//Format comm_date
						$commdt = strtotime( $row['comm_date'] );
						$formatted_commdt = date( 'm-d-Y h:i:s a', $commdt );
						$hist.= $formatted_commdt.' '.$type."\n".$row['comm_note']."\n\n";
					}
				}
				mysqli_free_result($cr);
			}
			
			$fn = $ln = $em = $ph = $city = $st = $tzone = $tier = $tierstep = $mab = $cab = $aab = '';
			$notes = $rid = $updt = $inviter = $tm = $nad = $ct = $poc = $am = $ac = $t2c = $ioc = $ar = '';
			$reptime = '';
			$disp = '';
			// Make the query to pick the contact data:
			$q = "SELECT firstname, lastname, email, phone,
						city, state, timezone, tier_status, 
						notes, update_date, rep_id, team_member, next_action_date,
						contact_type, prospect_or_customer, assigned_manager, assigned_consultant,
						had_tier2_call, inviter_on_call, additional_rep, 
						man_assigned_by, con_assigned_by, addl_assigned_by, disposition
				  FROM contacts
				  WHERE contact_id = '$currcid' LIMIT 1";
			$rs = mysqli_query ($dbc, $q); // Run the query.
			if ($rs){
				if (mysqli_num_rows($rs) == 1) {
					while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						$fn = $row['firstname'];
						$_SESSION['policy_account_firstname'] = $row['firstname'];
						$ln = $row['lastname'];
						$_SESSION['policy_account_lastname'] = $row['lastname'];
						$em = $row['email'];
						$ph = $row['phone'];
						$city = $row['city'];
						$st = $row['state'];
						$tzone = $row['timezone'];
						//display timezone data
						//rep time:
						date_default_timezone_set($_SESSION['rep_tz']);
						$reptime = date("h:i:s a");
						//contact time:
						if ($row['timezone'] != ''){
							date_default_timezone_set($row['timezone']);
							$contacttime = date("h:i:s a");
						} else {
							date_default_timezone_set($_SESSION['rep_tz']);
							$contacttime = date("h:i:s a");
						}
						//split the tier_status
						$tier = substr($row['tier_status'],0,1);
						$tierstep = substr($row['tier_status'],1,1);
						$notes = $row['notes'];
						$rid = $row['rep_id'];
						//Format the update_date
						if ($row['update_date'] != ''){
							$updatedt = strtotime( $row['update_date'] );
							$formatted_updatedt = date( 'm-d-Y h:i:s a', $updatedt );
						} else {
							$formatted_updatedt = '';
						}
						$inviter = $row['rep_id'];
						$tm = $row['team_member'];
						//reformat the next_action_date (yyyy-mm-dd to mm-dd-yyyy)
						$nad = '';
						$nad = $row['next_action_date'];
						if ($nad != ''){
							$next_action_dt = substr($nad,5,2).'-'.substr($nad,8,2).'-'.substr($nad,0,4);
							$nad = $next_action_dt;
						}
						$ct = $row['contact_type'];
						$poc = $row['prospect_or_customer'];
						$am = $row['assigned_manager'];
						$ac = $row['assigned_consultant'];
						$t2c = $row['had_tier2_call'];
						$ioc = $row['inviter_on_call'];
						$ar = $row['additional_rep'];
						$mab = $row['man_assigned_by'];
						$cab = $row['con_assigned_by'];
						$aab = $row['addl_assigned_by'];
						$disp = $row['disposition'];
						
					}
				}
				mysqli_free_result($rs);
			}
			
			$man_name = '';
			//Grab assigned manager if field has data
			if ($am <> ''){
				//get the manager's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$am' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$man_name = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}
				
			}
			$man_assigned_by = '';
			//Grab manager assigned by if field has data
			if ($mab <> ''){
				//get the manager's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$mab' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$man_assigned_by = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}	
			}
			
			$con_name = '';
			//Grab assigned consultant if field has data
			if ($ac <> ''){
				//get the consultant's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$ac' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$con_name = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}	
			}
			$con_assigned_by = '';
			//Grab manager assigned by if field has data
			if ($cab <> ''){
				//get the manager's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$cab' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$con_assigned_by = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}	
			}
			
			$addl_rep_name = '';
			//Grab additional rep if field has data
			if ($ar <> ''){
				//get the manager's name
				$q = "SELECT firstname, lastname
					  FROM reps
					  WHERE vfgrepid = '$ar' LIMIT 1";
				$rs = mysqli_query ($dbc, $q); // Run the query.
				if ($rs){
					if (mysqli_num_rows($rs) == 1) {
						while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
							$addl_rep_name = $row['firstname'].' '.$row['lastname'];
						}
					}
					mysqli_free_result($rs);
				}
				
			}
			
			//Grab Scheduled Call
			$thistier = '';
			$scheduled_call = '';
			//get data from 
			$q = "SELECT tier, date_format(scheduled_meeting, '%m-%d-%Y %l:%i %p') as meeting_date
				  FROM tiercall_meetings
				  WHERE contact_id = '$currcid' LIMIT 1";
			$rs = mysqli_query ($dbc, $q); // Run the query.
			if ($rs){
				if (mysqli_num_rows($rs) == 1) {
					while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						$thistier = $row['tier'];
						$scheduled_call = $row['meeting_date'];
					}
				}
				mysqli_free_result($rs);
			}
				
			
			
			mysqli_close($dbc); // Close the database connection.
		?>
			
		<div class="editcontactbox roundcorners opacity95">
			<div class="editcontactform">
					<!--<div class="refresheditdiv">
						
					</div>-->
					<div class="backtodiv">
						<form action="<?php echo $_SESSION['from_page']; ?>" method="GET" >
							<input type="hidden" name="s" value="<?php echo $start; ?>" />
							<input type="hidden" name="srch" value="<?php echo $_SESSION['srch_string']; ?>" />
							<input type="submit" id="back_to_list" class="backtobutton" value="<?php echo $_SESSION['back_btn_text']; ?>" />
						</form>
					</div>
					<div class="cleardiv"></div>
					<div class="editdivclientname">Edit for: <?php echo $fn.' '.$ln.'.'; ?></div>
					<p align="center"><b>Inviter:</b> <?php echo $invitername; ?>&nbsp;&nbsp;&nbsp;&nbsp;
									<b>Manager Assigned By:</b>  <?php echo $man_assigned_by; ?>&nbsp;&nbsp;&nbsp;&nbsp;
									<b>Consultant Assigned By:</b> <?php echo $con_assigned_by; ?></p>
					<div class="reptime"><?php echo 'Your Local Time:  '.$reptime; ?></div>
					<div class="contacttime"><?php echo $fn.' '.$ln.'\'s Local Time:  '.$contacttime; ?></div>
					<div class="cleardiv"></div>
					<div style="text-align:center;">
						<form action="rep_edit_contact.php" method="GET" >
							<input type="hidden" name="s" value="<?php echo $start; ?>" />
							<input type="hidden" name="cid" value="<?php echo $currcid; ?>" />
							<input type="submit" id="refresh_rec" class="backtobutton" value="Refresh Screen" />
						</form>
					</div>
					<form name="edit_contact_record" id="edit_contact_record" onSubmit="return updateContact();" >
						<p align="center"><input type="submit" id="update_button" class="generalbuttongreen" value="Update Record" /></p>
					<table align="center" border="0" width="70%">
						<tr>	<!-- First name, Last name -->
							<td><label for="first_name">First Name:</label>
								<input type="text" id="first_name" name="first_name" class="input150 capitalwords" maxlength="30"
										value="<?php echo $fn; ?>" />
							</td>
							<td>
								<label for="last_name">Last Name:</label>
								<input type="text" id="last_name" name="last_name" class="input200 capitalwords" maxlength="45"  
									value="<?php echo $ln; ?>" />
							</td>
						</tr>
						<tr>	<!-- Email, Phone -->
							<td>
								<label for="email">Email:</label>
								<input type="text" id="email" name="email" class="input200" maxlength="80"
									value="<?php echo $em; ?>" />&nbsp;
								<a href="<?php echo $currcid; ?>" class="email_modal_main"><img src="./images/smallicons/mail-closed.png" /></a>
								<!--<div id="email_respons"></div>-->
							</td>
							<td>
								<label>Phone:</label>
								<input type="text" name="phone" id="phone" class="input125"
											value="<?php echo $ph; ?>" />&nbsp;
								<!--<a href="'.$currcid.'" class="matrix-mail">
									<img src="./images/mail_icon.png" height="25px" width="30px" />
								</a>-->
								<a href="<?php echo $currcid; ?>" class="phone_modal_main">
									<img src="./images/smallicons/cellphone1.png" />
								</a>
								<a href="<?php echo $currcid; ?>" class="text_modal_main">
									<img src="./images/smallicons/text.png" />
								</a>
							</td>
						</tr>
						<tr>	<!-- City, State, Timezone -->
							<td>
								<label for="city">City:</label>
								<input type="text" id="city" name="city" class="input125" maxlength="40"  
									value="<?php echo $city; ?>" />
							</td>
							<td>
								<label for="state">State:</label>
								<?php
									$selected_state = "";
									$selected_state = $st;
									
									echo '<select name="state" id="state">';
								
									foreach($states as $id=>$name){
										if($selected_state == $id){
											$sel = 'selected="selected"';
										}
										else{
											$sel = '';
										}
										echo "<option $sel value=\"$id\">$name</option>";
									}
								?>
								</select>
							</td>
						</tr>
						<tr>	<!-- Timezone -->
							<td>
								<label for="timezone">Timezone:</label>
								<?php
								$selected_tz = "";
								$selected_tz = $tzone;

								echo '<select id="timezone" name="timezone">';
									
								foreach($americaTimeZones as $id=>$name){
									if($selected_tz == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
							<td>	<!-- Tier -->
								<label>Tier:</label>
								<input type="radio" name="tier" id="tier1" value="1" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '1' ? 'checked' : ''); ?> /><span>1</span>
								<input type="radio" name="tier" id="tier2" value="2" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '2' ? 'checked' : ''); ?> /><span>2</span>
								<input type="radio" name="tier" id="tier3" value="3" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '3' ? 'checked' : ''); ?> /><span>3</span>
								<input type="radio" name="tier" id="tier4" value="4" onClick="javascript: setList(this.value);"
								<?php echo ($tier == '4' ? 'checked' : ''); ?> /><span>4</span>
							</td>
						</tr>
						<tr>
							<td><!-- Team Member -->
								<label for="team_member">Team Member:</label>
								<?php
								$selected_teammember = "";
								$selected_teammember = $tm;
								
								echo '<select name="team_member" id="team_member">';
							
								foreach($yes_or_no as $id=>$name){
									if($selected_teammember == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
							<?php
							//get the tierstep array based on the tier level
							$tierarray = '';
							switch ($tier){
								case '1':
									$tierarray = $tier1steps;
									break;
								case '2':
									$tierarray = $tier2steps;
									break;
								case '3':
									$tierarray = $tier3steps;
									break;
								case '4':
									$tierarray = $tier4steps;
									break;
							}
							echo '<td>
								<label for="tierstep">Status:</label>
									<select name="tierstep" id="tierstep" onChange="javascript: setStatus(this.value);">';
							foreach($tierarray as $id=>$name){
								if($tierstep == $id){
									$sel = 'selected="selected"';
								}
								else{
									$sel = '';
								}
								echo "<option $sel value=\"$id\">$name</option>";
							}
							?>
							</select>
							<?php
								//DROP MAIL ICON NEXT TO STATUS DROPDOWN IF THIS IS THE CONSULTANT
								if ($repsvfgid == $ac){
									echo '&nbsp;<a href="#" class="missed_meeting_email"><img src="./images/smallicons/mail-closed.png" /></a>';
								}
							?>
							</td>
						</tr>
						<tr>	
							<td> <!-- Prospect or Customer -->
								<label for="porc">Prospect/Customer:</label>
								<?php
								$selected_porc = "";
								$selected_porc = $poc;
								
								echo '<select name="porc" id="porc">';
							
								foreach($prospect_or_customer as $id=>$name){
									if($selected_porc == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
							<td>
								<label for="contact_type">Contact Type:</label>
								<?php
								$selected_contacttype = "";
								$selected_contacttype = $ct;
								
								echo '<select name="contact_type" id="contact_type">';
							
								foreach($direct_indirect as $id=>$name){
									if($selected_contacttype == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
						</tr>
						<tr>	
							<td> <!-- ASSIGNED MANAGER -->
								<label for="assigned_manager">Manager VFG ID:</label>
								<input type="text" id="assigned_manager" name="assigned_manager" class="input100 capitalwords" maxlength="5"
										onChange="assignedLookup(this.value, 'mgr')" value="<?php echo $am; ?>" /><br />
								<span id="assigned_mgr" class="assigned_rep"><?php echo $man_name; ?></span>
							<td>
								<label for="next_action_date">Next Action Date:</label>
								<input type="text" name="next_action_date" id="datepicker" value="<?php echo $nad; ?>" />
							</td>
						</tr>
						<tr>	<!-- ASSIGNED CONSULTANT -->
							<td>
								<label for="assigned_consultant">Consultant VFG ID:</label>
								<input type="text" id="assigned_consultant" name="assigned_consultant" class="input100 capitalwords" maxlength="5"  
									onChange="assignedLookup(this.value, 'con')" value="<?php echo $ac; ?>" readonly />&nbsp;
								<?php
									// MANAGER ONLY gets find icon
									if ($repsvfgid == $am){
										echo '<a href="" class="lookup_consultant_modal"><img src="./images/smallicons/find.png" /></a><br />';
									} else {
										echo '<br />';
									}
								?>
								<span id="assigned_con" class="assigned_rep"><?php echo $con_name; ?></span>
							</td>
							<td> 
								<!-- MANAGER ONLY - tier 2 call and inviter on call -->
								<?php
									if ($repsvfgid == $am){
										//set the checked values for each event level...
										$tier2call_checked = $inviteroncall_checked = '';
										if ($t2c == 'Y') {
											$tier2call_checked = ' checked="checked" ';
										}
										if ($ioc == 'Y') {
											$inviteroncall_checked = ' checked="checked" ';
										}
										echo '<input type="checkbox" name="tier2_call" id="tier2_call" value="Y" '.$tier2call_checked. '>Tier 2 Call Made?<br />
											<input type="checkbox" name="inviter_on_call" id="inviter_on_call" value="Y" '.$inviteroncall_checked.' >Inviter On Call?';
									} else {
										echo '<label>Manager Tier 2 Call?</label><span> '.$t2c.'</span><br />
											  <label>Inviter on the Call?</label><span> '.$ioc.'</span>';
									}
								?>
								<!-- Scheduled Call Meetings -->
								<!--<div id="tiercall_meeting"></div>-->
								<br /><br />
								<label>Scheduled Call:</label>
								<?php
									if ($thistier <> ''){
										echo '<label>Tier '.$thistier.' on '.$scheduled_call.'</label>';
									}
								?>
							</td>
						</tr>
						<tr>
							<td>
								<label for="additional_rep">Additional Rep ID:</label>
								<input type="text" id="additional_rep" name="additional_rep" class="input100 capitalwords" maxlength="5"
										onChange="assignedLookup(this.value, 'adr')" value="<?php echo $ar; ?>" /><br />
								<span id="addl_rep" class="assigned_rep"><?php echo $addl_rep_name; ?></span>
							</td>
							<td>
								<!-- DISPOSITION -->
								<label for="disposition">Disposition:</label>
								<?php
								$selected_disposition = "";
								$selected_disposition = $disp;
								
								echo '<select name="disposition" id="disposition">';
							
								foreach($disposition as $id=>$name){
									if($selected_disposition == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
								<!-- Disposition ICON to display MODAL of percentages -->
								<a href="<?php echo $repsid; ?>" class="disposition_percentages_modal">
									<img src="./images/smallicons/heirarchy.png" />
								</a>
							</td>
						</tr>
					</table>
					<p />
					<table align="center" border="0" width="90%">
						<tr>	<!-- 3x3 History, Notes -->
							<td>
								<label for="matrix_history">Communication History:</label>
								<textarea id="matrix_history" name="matrix_history" rows="12" cols="50" readonly ><?php echo $hist; ?></textarea> 
							</td>
							<td valign="top">
								<label for="notes">Notes:</label>
								<textarea id="notes" name="notes" rows="12" cols="50"><?php echo stripslashes($notes); ?></textarea> 
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<?php
								//View or View/Edit Policy button under notes.  One is modal, other is separate page.
								if ($repsvfgid == $ac){
									echo '<td><a href="rep_edit_policies.php?s='.$start.'&cid='.$currcid.'&fn='.$fn.'&ln='.$ln.'&iv='.$inviter.'&am='.$am.'&ac='.$ac.'" class="myButton policy_edit">View/Edit Policies</a></td>';
								} else {
									echo '<td><a href="#" class="myButton policy_modal">View Policies</a></td>';
								}
							?>
						</tr>
						<tr>
							<td colspan="2" align="center" >
								<input type="hidden" name="submitted" value="updatecontact" />
								<input type="hidden" id="original_firstname" name="original_firstname" value="<?php echo $fn; ?>" />
								<input type="hidden" id="original_lastname" name="original_lastname" value="<?php echo $ln; ?>" />
								<input type="hidden" id="original_email" name="original_email" value="<?php echo $em; ?>" />
								<input type="hidden" id="original_phone" name="original_phone" value="<?php echo $ph; ?>" />
								<input type="hidden" id="original_city" name="original_city" value="<?php echo $city; ?>" />
								<input type="hidden" id="original_state" name="original_state" value="<?php echo $st; ?>" />
								<input type="hidden" id="original_timezone" name="original_timezone" value="<?php echo $tzone; ?>" />
								<input type="hidden" id="original_tier" name="original_tier" value="<?php echo $tier; ?>" />
								<input type="hidden" id="original_tierstep" name="original_tierstep" value="<?php echo $tierstep; ?>" />
								<input type="hidden" id="original_teammember" name="original_teammember" value="<?php echo $tm; ?>" />
								<input type="hidden" id="original_notes" name="original_notes" value="<?php echo $notes; ?>" />
								<input type="hidden" id="original_nextactiondate" name="original_nextactiondate" value="<?php echo $nad; ?>" />
								<input type="hidden" id="original_porc" name="original_porc" value="<?php echo $poc; ?>" />
								<input type="hidden" id="original_contacttype" name="original_contacttype" value="<?php echo $ct; ?>" />
								<input type="hidden" id="original_assignedmanager" name="original_assignedmanager" value="<?php echo $am; ?>" />
								<input type="hidden" id="original_assignedconsultant" name="original_assignedconsultant" value="<?php echo $ac; ?>" />
								<input type="hidden" id="original_tier2call" name="original_tier2call" value="<?php echo $t2c; ?>" />
								<input type="hidden" id="original_inviteroncall" name="original_inviteroncall" value="<?php echo $ioc; ?>" />
								<input type="hidden" id="original_additionalrep" name="original_additionalrep" value="<?php echo $ar; ?>" />
								<input type="hidden" id="og_disposition" name="og_disposition" value="<?php echo $disp; ?>" />
								<input type="hidden" id="data_changed" name="data_changed" value="false" />
								<input type="hidden" id="cid" name="cid" value="<?php echo $currcid; ?>" />
								<input type="hidden" id="inviter" name="inviter" value="<?php echo $inviter; ?>" />
								<input type="hidden" id="set_tier" name="set_tier" value="<?php echo $tier; ?>" />
								<input type="hidden" id="set_status" name="set_status" value="<?php echo $tierstep; ?>" />
								<input type="hidden" id="role" name="role" value="<?php echo $assigned_role; ?>" />
								<!--<input type="submit" class="generalbutton" value="Update" /><br />-->
								<!--<div id="ajax_verify_update"></div>-->
							</td>
						</tr>
					</form>
						<!-- <tr>
							<td>
								<form action="rep_edit_contact.php" method="GET" >
								<input type="hidden" name="s" value="<?php //echo $start; ?>" />
								<input type="hidden" name="cid" value="<?php //echo $currcid; ?>" />
								<input type="submit" id="refresh_rec" class="backtobutton" value="Refresh Record" />
								</form>
							</td>
							<td>
								&nbsp;
							</td>
						</tr> -->	
					</table>
			</div> <!-- close editcontactform -->
		</div>	<!-- close editcontactbox -->
		<div id="update_messages" class="opacity95">Edit Contact</div>
		<div class="cleardiv"></div>
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>