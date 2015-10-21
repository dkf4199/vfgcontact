<?php
session_start();

	//include ('includes/config.inc.noerrorhandler.php');
	//include ('includes/phpfunctions.php');
	//include ('includes/selectlists.php');
	//DB Connection
	//require_once (MYSQL);
	
	// POST VARS COMING IN
	// rep vfgid, date
	$rep_vfgid = $_POST['repvfgid'];
	$event_date = $_POST['eventdate'];
	
	//$gmail = $pass = '';
	//Get the reps vfgid
	//$q = "SELECT gmail_acct, gmail_pass, rep_timezone FROM reps WHERE vfgrepid = '$rep_vfgid' LIMIT 1";
	//$r = mysqli_query($dbc, $q);

	/*if ($r) { 
		// Ran OK
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$gmail = $row['gmail_acct'];
			$pass = $row['gmail_pass'];
			$_SESSION['consultants_gmail_acct'] = $gmail;
			$_SESSION['consultants_gmail_pass'] = $pass;
			$_SESSION['consultants_tz'] = $row['rep_timezone'];
		}
		mysqli_free_result($r);
	}*/
	$gmail = $pass = $ctz = '';
	if (isset($_SESSION['consultants_gmail_acct'])){
		$gmail = $_SESSION['consultants_gmail_acct'];
	}
	if (isset($_SESSION['consultants_gmail_pass'])){
		$pass = $_SESSION['consultants_gmail_pass'];
	}

	//echo $gmail.'<br />'.$pass.'<br />';
	
	if ( ($gmail != '') && ($pass != '') ) {
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		Zend_Loader::loadClass('Zend_Http_Client');
		$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$gmailuser = $gmail;
		$gmailpass = $pass;
		$client = Zend_Gdata_ClientLogin::getHttpClient($gmailuser, $gmailpass, $gcal);
		$gcal = new Zend_Gdata_Calendar($client);
		$query = $gcal->newEventQuery();
		
		//Reconfigure the incomming date (mm-dd-yyyy) to (yyyy-mm-dd)
		list($dt_mm,$dt_dd,$dt_yy) = explode('-',$event_date);
		$calendardate = $dt_yy.'-'.$dt_mm.'-'.$dt_dd;
		
		//Add +1 day to date
		$date1 = new DateTime($calendardate);
		$date1->modify('+1 day');
		
		$nextday = $date1->format('Y-m-d');
		echo $calendardate.'<br />'.$nextday;
		//echo $date1->format('Y-m-d');
		
		//date range
		//$query->setStartMin($calendardate);
		//$query->setStartMax($calendardate);
		$query->setStartMin($calendardate);
		$query->setStartMax($nextday);
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
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
		
		echo '<table width="100%" border="0">
				 <tr>
				  <th>Event</th>
				  <th>Date</th>
				  <th>Start Time</th>
				  <th>End Time</th>
				 </tr>';
		foreach ($feed as $event) {
			//echo "<li>\n";
			//echo $event;
			foreach ($event->when as $when) {
				$startDateTime = new DateTime($when->startTime);
				$endDateTime = new DateTime($when->endTime);
					
				echo '<tr>
					<td>'.stripslashes($event->title->text).
					'</td>
					<td>'.$startDateTime->format('m-d-Y').
					'</td>
					 <td>'.$startDateTime->format('h:i a').
					 '</td>
					  <td>'.$endDateTime->format('h:i a').
					  '</td></tr>';
			}
				
		}
		echo '</table>';
	}
?>