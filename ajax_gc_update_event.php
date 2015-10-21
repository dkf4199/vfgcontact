<?php
include('includes/gc_AuthSub_calendar_connect.php');
session_start();
date_default_timezone_set($_SESSION['rep_tz']);

# ajax_gc_update_event.php

	//called from function updateEvent() on rep_calendar_viewevents.php
	//
	//$_POST[] DATA SENT IN FROM $ajax. function
	/* 	gce_id, gce_title, 
		gce_startdate, gce_enddate,
		gce_starttime, gce_endtime,
		
		
	*/
	
	$errors = array();		//initialize errors array
	$iomsgs = array();
	
	//VALIDATE FORM FIELDS
	//*******************************************************
	//gce_id
	if ( empty($_POST['gce_id']) ){
		$errors[] = 'Id not supplied.';
	}
	
	//gce_title
	if (empty($_POST['gce_title']) || $_POST['gce_title'] == '' ){
		$errors[] = 'Please enter event\'s title.';
	}
	//gce_startdate
	if (empty($_POST['gce_startdate']) || $_POST['gce_startdate'] == '' ){
		$errors[] = 'Please enter event\'s start date.';
	} elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['gce_startdate']))) {
		$errors[] = 'Invalid start date format. Format is MM-DD-YYYY.';
	}
	//gce_enddate
	// 08/27/2013 dkf
				// Comment out the end date on the events
				//
				/*
	if (empty($_POST['gce_enddate']) || $_POST['gce_enddate'] == '' ){
		$errors[] = 'Please enter event\'s end date.';
	} elseif (!preg_match("/^\d{2}[-]\d{2}[-]\d{4}$/", trim($_POST['gce_enddate']))) {
		$errors[] = 'Invalid end date format. Format is MM-DD-YYYY.';
	}
	*/
	
	//gce_starttime
	if (empty($_POST['gce_starttime']) || $_POST['gce_starttime'] == '' ){
		$errors[] = 'Please enter event\'s start time.';
	}
	//gce_endtime
	if (empty($_POST['gce_endtime']) || $_POST['gce_endtime'] == '' ){
		$errors[] = 'Please enter event\'s end time.';
	}
	//*************** END FIELD VALIDATION ***********************
	
	//Data Changed flag
	//$hasDataChanged = $_POST['data_changed'];
	
	//*************** DB UPDATE **********************************
	if (empty($errors)){
	
		//load ZEND classes
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		//Zend_Loader::loadClass('Zend_Http_Client');
		// Create an instance of the Calendar service, redirecting the user
		// to the AuthSub server if necessary.
		$gcal = new Zend_Gdata_Calendar(getAuthSubHttpClient());
		
		//UPDATE GC EVENT
		
		//$_POST start and end times in dropdowns in form hh:ii
		//$_POST start and end dates in form mm-dd-yyyy from datepickers
		
		$title = htmlentities($_POST['gce_title']);
		
		
		//Put $_POST vars into LISTS
		//****************************
		//Times
		list($st_hour,$st_min) = explode(':',$_POST['gce_starttime']);
		list($et_hour,$et_min) = explode(':',$_POST['gce_endtime']);
		
		//Dates
		list($sd_mm,$sd_dd,$sd_yy) = explode('-',$_POST['gce_startdate']);
		//list($ed_mm,$ed_dd,$ed_yy) = explode('-',$_POST['gce_enddate']);
		
		//convert dates to proper format
		$start = date(DATE_ATOM, mktime($st_hour, $st_min, 0, $sd_mm, $sd_dd, $sd_yy));
		//$end = date(DATE_ATOM, mktime($et_hour, $et_min, 0, $ed_mm, $ed_dd, $ed_yy));
					 
		/* 08/27/2013 dkf 
		   make the end date portion of the 
		   event the same as the start date */
		$end = date(DATE_ATOM, mktime($et_hour, $et_min, 0, $sd_mm, $sd_dd, $sd_yy));
		  
		// get existing event record
		// update event attributes
		// save changes to server
		try {
			$event = $gcal->getCalendarEventEntry('http://www.google.com/calendar/feeds/default/private/full/' . $_POST['gce_id']);
			$event->title = $gcal->newTitle($title); 
			$when = $gcal->newWhen();
			$when->startTime = $start;
			$when->endTime = $end;
			$event->when = array($when);        
			$event->save();
				
			$iomsgs[] = 'Event successfully updated.';
			
		} catch (Zend_Gdata_App_Exception $e) {
			//die("Error: " . $e->getResponse());
			$iomsgs[] = "Error: " . $e->getResponse();
		}
		
		//DISPLAY iomsgs[]
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p>';
		foreach ($iomsgs as $msg){
			echo "$msg<br />\n";
		}
		echo '</p>';
		
	} else {	//Have errors
	
		//DISPLAY ERRORS[] ARRAY MESSAGES
		echo '<p><u><font color="red"><b>ERRORS:</b></font></u><br />';
		foreach ($errors as $msg){
			echo " - $msg<br />\n";
		}
		echo '</p>';
	}
?>