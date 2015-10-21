<?php
include('includes/gc_AuthSub_calendar_connect.php');
session_start();
	//selectlists holds the time arrays
	include ('includes/selectlists.php');
	
	$eventid = $_GET['id'];
	$repsid = $_SESSION['rep_id'];
	$tz = $_SESSION['rep_tz'];
	date_default_timezone_set($tz);
	
	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_Calendar');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	//Zend_Loader::loadClass('Zend_Http_Client');
	// Create an instance of the Calendar service, redirecting the user
	// to the AuthSub server if necessary.
	$gcal = new Zend_Gdata_Calendar(getAuthSubHttpClient());
    
	//The 'full' option is the full calendar project.  Other option is 'basic'
	//The 'private' option requires authentication
    if (isset($_GET['id'])) {
        try {          
          $event = $gcal->getCalendarEventEntry('http://www.google.com/calendar/feeds/default/private/full/' . $_GET['id']);
        } catch (Zend_Gdata_App_Exception $e) {
          echo "Error: " . $e->getResponse();
        }
	} else {
	die('ERROR: No event ID available!');  
	}
	// format data into human-readable form
	// populate a Web form with the record
	$title = $event->title;
	$when = $event->getWhen();
	$startTime = strtotime($when[0]->getStartTime());
	$sdate_dd = date('d', $startTime);
	$sdate_mm = date('m', $startTime);
	$sdate_yy = date('Y', $startTime);
	$sdate_hh = date('H', $startTime);
	$sdate_ii = date('i', $startTime);
	$endTime = strtotime($when[0]->getEndTime());
	$edate_dd = date('d', $endTime);
	$edate_mm = date('m', $endTime);
	$edate_yy = date('Y', $endTime);
	$edate_hh = date('H', $endTime);
	$edate_ii = date('i', $endTime);
	
	//concatenated start and end dates for event
	$start_dt = $sdate_mm.'-'.$sdate_dd.'-'.$sdate_yy;
	$end_dt = $edate_mm.'-'.$edate_dd.'-'.$edate_yy;
	
	//concatenated start and end times for event
	$start_time = $sdate_hh.':'.$sdate_ii;
	$end_time = $edate_hh.':'.$edate_ii;
	
	$formstr = '';
	$formstr = '<div class="calendarform">
		<form name="gc_editevent_form" id="gc_editevent_form" onSubmit="return updateGCEvent(\''.$_GET['id'].'\')">
		  <input type="hidden" name="gce_id" id="gce_id" value="'.$_GET['id'].'">
		  <ul>
			<li>
				<label>Event title:</label>
				 <input name="gce_title" id="gce_title" type="text" size="45" value="'.$title.'"/>
			</li>
			<li>
				<label>Start date<br /> (mm-dd-yyyy):</label>
				<input name="gce_startdate" id="datepicker_edit_startdate" type="text" value="'.$start_dt.'" />
			</li>
			<li>
				<label>End date<br /> (mm-dd-yyyy):</label>
				<input name="gce_enddate" id="datepicker_edit_enddate" type="text" value="'.$end_dt.'" />
			</li>
			<li>
				<label>Start Time:</label>';
					$selected_stime = "";
					$selected_stime = $start_time;
					
					$formstr .= '<select name="gce_starttime" id="gce_starttime">';
				
					foreach($calendartime as $id=>$name){
						if($selected_stime == $id){
							$sel = 'selected="selected"';
						}
						else{
							$sel = '';
						}
						$formstr .= "<option $sel value=\"$id\">$name</option>";
					}
						
					$formstr .= '</select>
				</li>
				<li>
					<label>End Time:</label>';
					$selected_etime = "";
					$selected_etime = $end_time;
					
					$formstr .= '<select name="gce_endtime" id="gce_endtime">';
				
					foreach($calendartime as $id=>$name){
						if($selected_etime == $id){
							$sel = 'selected="selected"';
						}
						else{
							$sel = '';
						}
						$formstr .= "<option $sel value=\"$id\">$name</option>";
					}
					$formstr .= '</select>
				</li>
				<li>
					<input name="submit" type="submit" class="button" value="Update Event" />
				</li>
				<li>
					<div id="ajax_gc_editevent_update"></div>
				</li>
			  </ul>	
			</form>
			</div>';
	echo $formstr;
?>
        