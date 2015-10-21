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
	$formstr = '<div class="webform">
		<form name="gc_deleteevent_form" id="gc_deleteevent_form" onSubmit="return deleteGCEvent(\''.$_GET['id'].'\')">
		  <input type="hidden" name="gcdelete_id" id="gcdelete_id" value="'.$_GET['id'].'">
		  <ul>
			<li>
				<label>Event title:</label>
				 <span>'.$title.'</span
			</li>
			<li>
				<label>Event date<br /> (mm-dd-yyyy):</label>
				<span>'.$start_dt.'</span>
			</li>
			<li>
				<label>Event Start Time:</label>
				<span>'.$start_time.'</span>
			</li>
			<li>
				<label>Event End Time:</label>
				<span>'.$end_time.'</span>
			<li>
				<input name="submit" type="submit" class="button" value="Delete Event" />
			</li>
			<li>
				<div id="ajax_gc_editevent_update"></div>
			</li>
		  </ul>	
		</form>
		</div>';
	echo $formstr;
	unset($_GET);
?>
        