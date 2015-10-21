<?php
session_start();
	# ajax_insertupdate_tiercall_meeting.php
	include ('includes/config.inc.php');
	include ('includes/selectlists.php');
	require_once (MYSQL);
	
	$tier = $_GET['tier'];
	$cid = $_GET['cid'];
	
	$cfirst = $_GET['cfirst'];
	$clast = $_GET['clast'];
	$ctzone = $_GET['ctzone'];
	
	$tzdesc = '';
	//Get timezone description
	switch($ctzone){
		case 'America/New_York':
			$tzdesc = 'Eastern';
			break;
		case 'America/Chicago':
			$tzdesc = 'Central';
			break;
		case 'America/Boise':
			$tzdesc = 'Mountain';
			break;
		case 'America/Phoenix':
			$tzdesc = 'Arizona Mountain';
			break;
		case 'America/Los_Angeles':
			$tzdesc = 'Pacific';
			break;
		case 'America/Juneau':
			$tzdesc = 'Alaska';
			break;
		case 'Pacific/Honolulu':
			$tzdesc = 'Hawaii';
			break;
	}
	//Flag indicating this call is from the phone script modal
	$from_phonescript = 'N';
	if (isset($_GET['fromphone'])){
		$from_phonescript = $_GET['fromphone'];
	}
	
	//Get meeting entry if one exists
	//MySQL date_format: %l is hour, %i is the minutes, %p is am/pm
	$rec_exists = false;
	$md = $m_hour = $m_min = $ni = $nt = $s_em = $s_txt ='';
	$get_sql = "SELECT date_format(scheduled_meeting, '%m-%d-%Y') as meeting_date,
						date_format(scheduled_meeting, '%H') as meeting_hh,
						date_format(scheduled_meeting, '%i') as meeting_mm, 							
						notification_interval, 
						date_format(notification_time, '%m-%d-%Y %l:%i %p') as notify_time,
						send_email,
						send_text
				FROM tiercall_meetings
				WHERE contact_id = '$cid' LIMIT 1";
	$r = mysqli_query($dbc, $get_sql);
	if (mysqli_num_rows($r) == 1){
		$rec_exists = true;
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$md = $row['meeting_date'];
			$m_hour = $row['meeting_hh'];
			$m_min = $row['meeting_mm'];
			$ni = $row['notification_interval'];
			$nt = $row['notify_time'];
			$s_em = $row['send_email'];
			$s_txt = $row['send_text'];
		}
		mysqli_free_result($r);
	}
	//Get contact's local time
	date_default_timezone_set($ctzone);
	$contacttime = date("g:i a");	//g is 12-hour format....no leading zeros
	
	echo '<div style="display:none">
			<div class="modal-content">
		<h3>'.$cfirst.' '.$clast.' :  Tier '.$tier.' Meeting</h3>
		<p>'.$cfirst.' '.$clast.'\'s Timezone:  '.$tzdesc.' ('.$contacttime.')</p> 
		<div style="text-align: center;">
		<label for="meeting_date">Tier Meeting Date:</label>
		<input type="text" name="meeting_date" id="datepickermeeting" value="'.$md.'" /><br>
		<label>Meeting Time (hh:mm) :</label>
		<select name="meeting_time_hh" id="meeting_time_hh" >';
		foreach($meeting_hh as $id=>$name){
			if($m_hour == $id){
				$sel = 'selected="selected"';
			}
			else{
				$sel = '';
			}
			echo "<option $sel value=\"$id\">$name</option>";
		}
	echo '</select> : <select name="meeting_time_mm" id="meeting_time_mm" >';
	foreach($meeting_mm as $id=>$name){
		if($m_min == $id){
			$sel = 'selected="selected"';
		}
		else{
			$sel = '';
		}
		echo "<option $sel value=\"$id\">$name</option>";
	}
	echo '</select></br />
		<label for="notification_interval">Notification Interval:</label>
		<select name="notification_interval" id="notification_interval">';
	foreach($notification_int as $id=>$name){
		if($ni == $id){
			$sel = 'selected="selected"';
		}
		else{
			$sel = '';
		}
		echo "<option $sel value=\"$id\">$name</option>";
	}	
	echo '</select><br />
		  <label for="meeting_send_email">Send Email?</label>
		  <select name="meeting_send_email" id="meeting_send_email">';
	foreach($yes_or_no as $id=>$name){
		if($s_em == $id){
			$sel = 'selected="selected"';
		}
		else{
			$sel = '';
		}
		echo "<option $sel value=\"$id\">$name</option>";
	}		  
		  
	echo '</select><br />
		  <label for="meeting_send_text">Send Text?</label>
		  <select name="meeting_send_text" id="meeting_send_text">';
	foreach($yes_or_no as $id=>$name){
		if($s_txt == $id){
			$sel = 'selected="selected"';
		}
		else{
			$sel = '';
		}
		echo "<option $sel value=\"$id\">$name</option>";
	}		  
	
	echo '</select><br />
		<label>Notification time:</label>'.$nt.'<br />
		<input type="button" name="add_meeting_button" id="add_meeting_button" 
				onClick="javascript: return addTierMeeting(); " value="Add/Update" />';
	if ($rec_exists){
		echo '<input type="button" name="delete_meeting_button" id="delete_meeting_button" 
				onClick="javascript: return deleteTierMeeting(\''.$cid.'\'); " value="Delete" />';
	}
	echo '<input type="hidden" id="from_phonescript" name="from_phonescript" value="'.$from_phonescript.'"><br /><br />
		<div id="tier_messages"></div>
		</div> <!-- close generic div -->
		</div> <!-- close modal-container -->
		</div>';

?>