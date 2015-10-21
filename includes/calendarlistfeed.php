<?php #calendarlistfeed.php
	//session already started....
	
	// Gets list of all calendars owned by this user
	//**********************************************
	
	/* The Calendar Data API provides several ways to access 
	   the list of calendars that appear in the Google Calendar 
	   web application. There are 3 types of calendars in 
	   this list: primary, secondary, and imported calendars. 
	   1.) A primary calendar is created for a user when they sign 
		   up for a Google Calendar account. 
	   2.) All other calendars created by that user are called 
	       secondary calendars. 
	   3.) Imported calendars are calendars that a user 
	       subscribes to that someone else has created.
	   
	   To Get All Calendar Feeds:
	   /calendar/feeds/default/allcalendars/full
	   
	   To Get Calendar That User Owns:
	   calendar/feeds/default/owncalendars/full
	   
	   GetCalendarListFeed:
	   *********************
	   Calling getCalendarListFeed() creates a new instance 
	   of Zend_Gdata_Calendar_ListFeed containing each available 
	   calendar as an instance of Zend_Gdata_Calendar_ListEntry. 
	   After retrieving the feed, you can use the iterator and 
	   accessors contained within the feed to inspect the enclosed calendars.
	*/
	
	$tz = $_SESSION['rep_tz'];
	date_default_timezone_set($tz);
	
	require_once 'Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');
    Zend_Loader::loadClass('Zend_Http_Client');
    
    $gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
    //$user = "dkf4199@gmail.com";
    //$pass = "deronfrederickson";
	$user = $_SESSION['rep_google_email'];
	$pass = $_SESSION['rep_google_pass'];
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
    $gcal = new Zend_Gdata_Calendar($client);
	
	
	//Get list of existing calendars
	$calFeed = $gcal->getCalendarListFeed();
	
	//Loop through calendars and check name which is ->title->text
	foreach ($calFeed as $calendar) {
		//if($calendar -> title -> text == "App Calendar") {
		//	$noAppCal = false;
		//}
		echo 'Calendar Name: '.$calendar->title->text.' Calendar ID: '.$calendar->id.'<br />';
	}
	
?>