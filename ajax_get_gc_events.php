<?php
include('includes/gc_AuthSub_calendar_connect.php');
session_start();
date_default_timezone_set($_SESSION['rep_tz']);
	//"From Date" always set - check performed in jquery
	$fromdt = $_GET['fromdt'];
	
	//"To Date" can be blank, if it is - increment from date
	//by one to get "from date's" events
	if (!isset($_GET['todt']) || $_GET['todt'] == ''){
		
		//mktime routine
		$thisday = $fromdt;
		list($y,$m,$d)=explode('-',$thisday);
		$todt = Date("Y-m-d", mktime(0,0,0,$m,$d+1,$y));
		
		//strtotime routine
		#$today=$fromdt;
		#$nextday=strftime("%Y-%m-%d", strtotime("$today +1 day"));
		
	} else {
		$todt = $_GET['todt'];
	}
	
	$repsid = $_SESSION['rep_id'];
	$tz = $_SESSION['rep_tz'];
	
	
	
	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_Calendar');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	//Zend_Loader::loadClass('Zend_Http_Client');
	// Create an instance of the Calendar service, redirecting the user
	// to the AuthSub server if necessary.
	$gcal = new Zend_Gdata_Calendar(getAuthSubHttpClient());
    
    $query = $gcal->newEventQuery();
	//date range
	$query->setStartMin($fromdt);
	$query->setStartMax($todt);
    $query->setUser('default');
    $query->setVisibility('private');
    $query->setProjection('basic');
    $query->setOrderby('starttime');
	$query->setSortOrder('a');
    if(isset($_GET['q'])) {
      $query->setQuery($_GET['q']);      
    }
    
    try {
      $feed = $gcal->getCalendarEventFeed($query);
    } catch (Zend_Gdata_App_Exception $e) {
      echo "Error: " . $e->getResponse();
    }
    
	//Title Header
	#echo '<h1>'.$feed->title.'</h1>';
	//Title Header
	echo '<h3>'.$_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'</h3>';
	//echo '<h3>'.$_SESSION['rep_google_email'].' '.$_SESSION['rep_google_pass'].'</h3>';
    echo '<h3>'.$feed->totalResults.' event(s) found.</h3>
			<ol>';
        
    foreach ($feed as $event) {
      echo "<li>\n";
      echo "<h2>" . stripslashes($event->title) . "</h2>\n";
      echo stripslashes($event->summary) . " <br/>\n";
      $id = substr($event->id, strrpos($event->id, '/')+1);
      //echo "<a id=\"edit_event\" href=\"googlecalendar_editevent.php?id=$id\">edit</a> | ";
	  echo "<a id=\"gc_edit_event_link\" href=\"javascript: displayEventForEdit('".$id."');\">edit</a> | ";
      echo "<a id=\"gc_delete_event_link\" href=\"javascript: displayEventForDelete('".$id."');\">delete</a> <br/>\n";
      echo "</li>\n";
    }
	//echo "<li>\n";
	//echo $fromdt . "<br />\n";
	//echo $todt . "<br />\n";
	//echo "</li>\n"; 
    echo "</ul></ol><p/>";
	
	//unset($_GET);
?>
    