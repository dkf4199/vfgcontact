<?php
include('includes/gc_AuthSub_calendar_connect.php');
session_start();	
    require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_Calendar');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	//Zend_Loader::loadClass('Zend_Http_Client');
	// Create an instance of the Calendar service, redirecting the user
	// to the AuthSub server if necessary.
	$gcal = new Zend_Gdata_Calendar(getAuthSubHttpClient());
      
    // if event ID is present
    // get event object from feed
    // delete event  
    if (isset($_POST['gcdelete_id'])) {
      try {          
          $event = $gcal->getCalendarEventEntry('http://www.google.com/calendar/feeds/default/private/full/' . $_POST['gcdelete_id']);
          $event->delete();
      } catch (Zend_Gdata_App_Exception $e) {
          echo "Error: " . $e->getResponse();
      }        
      echo 'Event successfully deleted.';  
    } else {
      echo 'No event ID available';  
    }
    ?>