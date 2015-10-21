<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Adding GC events</title> 
  </head>
  <body>
    
    <?php
		
		// load classes
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		Zend_Loader::loadClass('Zend_Http_Client');

		
		
		// connect to service
		$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$user = "dkf4199@gmail.com";
		$pass = "deronfrederickson";
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
		$gcal = new Zend_Gdata_Calendar($client);
		  
		// Set the date using RFC 3339 format.
		$startDate = "2014-05-13";
		$startTime = "17:00";
		$endDate = "2014-05-13";
		$endTime = "18:00";
		$tzOffset = "-07";
		
		$title = "New Event 1";
		$desc = "This is my synched event.";
		//$start = date(DATE_ATOM, mktime($_POST['sdate_hh'], $_POST['sdate_ii'], 0, $_POST['sdate_mm'], $_POST['sdate_dd'], $_POST['sdate_yy']));
		//$end = date(DATE_ATOM, mktime($_POST['edate_hh'], $_POST['edate_ii'], 0, $_POST['edate_mm'], $_POST['edate_dd'], $_POST['edate_yy']));

		// construct event object
		// save to server      
		try {
			$event = $gcal->newEventEntry();        
			$event->title = $gcal->newTitle($title);
			$event->content = $gcal->newContent($desc);
			$when = $gcal->newWhen();
			$when->startTime = "{$startDate}T{$startTime}:00.000";
			$when->endTime = "{$endDate}T{$endTime}:00.000";
			//$when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
			//$when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
			$event->when = array($when);        
			$gcal->insertEvent($event);   
		} catch (Zend_Gdata_App_Exception $e) {
			echo "Error: " . $e->getResponse();
		}      
    
    ?>
  </body>
</html>     

