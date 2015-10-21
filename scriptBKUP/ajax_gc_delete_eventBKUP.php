<?php
session_start();	
    // load classes
    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');
    Zend_Loader::loadClass('Zend_Http_Client');
    
    // connect to service
    $gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
    //$user = "dkf4199@gmail.com";
	//$pass = "deronfrederickson";
	$user = $_SESSION['rep_google_email'];
	$pass = $_SESSION['rep_google_pass'];
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
    $gcal = new Zend_Gdata_Calendar($client);
      
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