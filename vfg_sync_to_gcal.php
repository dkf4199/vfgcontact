<?php
session_start();
	// If no agent session value is present, redirect the user:
	if ( !isset($_SESSION['staff_agent']) OR ($_SESSION['staff_agent'] != md5($_SERVER['HTTP_USER_AGENT'])) ) {
		require_once ('./includes/phpfunctions.php');
		$url = absolute_url('rep_login.php');
		//javascript redirect using window.location
		echo '<script language="Javascript">';
		echo 'window.location="' . $url . '"';
		echo '</script>';
		exit();	
	}
	include('includes/gc_AuthSub_calendar_connect.php');
	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_Calendar');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	// Create an instance of the Calendar service, redirecting the user
	// to the AuthSub server if necessary.
	try {
		$gcal = new Zend_Gdata_Calendar(getAuthSubHttpClient());
	} catch (Zend_Gdata_App_Exception $e) {
		echo "Error: " . $e->getResponse();
	}
	//$gcal = new Zend_Gdata_Calendar(getAuthSubHttpClient());

?>
<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
<title>Calendar Sync - VFG to Google</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">

<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {
	
});
</script>

</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<?php
			$repid = $_SESSION['rep_id'];
			
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

			// use config file WITHOUT error handler......it breaks the Zend stuff
			include ('includes/config.noerrhandler.inc.php');
			//DB Connection
			require_once (MYSQL);
			
			$start_date = '';
			$end_date = '';
			//First thing.....find MIN start and MAX end dates from fc_events
			$minmax_sql = "SELECT min(date_format(start, '%Y-%m-%d')) as min_start,
								  max(date_format(end, '%Y-%m-%d')) as max_end
							FROM fc_events
							WHERE rep_id = '$repid' ";
			$rs = mysqli_query($dbc, $minmax_sql);
			if (mysqli_num_rows($rs) > 0){
				while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
					$start_date = $row['min_start'];
					$end_date = $row['max_end'];
				}
				mysqli_free_result($rs);
			}
			
			// Reconfigure the end date.  GC needs +1 day to include the last day in date range.  It's exclusive!
			$ed_exclusive = new DateTime($end_date);
			$ed_exclusive->add(new DateInterval('P1D'));
			
			$end_date = $ed_exclusive->format('Y-m-d');
			
			// GC Event array (gonna hold start date, end_date, title)
			$gc_events = array();
			$vfg_events = array();
			
			// GET Google Calendar FEED for date range
			$query = $gcal->newEventQuery();
			//date range
			$query->setStartMin($start_date);
			$query->setStartMax($end_date);
			$query->setUser('default');
			$query->setVisibility('private');
			$query->setProjection('full');		//This HAS TO BE 'full' or you do NOT have access to the when[] data!!!!!!!
			$query->setOrderby('starttime');
			$query->setSortOrder('a');
			//$query->setFutureevents('true');
			try {
			  $feed = $gcal->getCalendarEventFeed($query);
			} catch (Zend_Gdata_App_Exception $e) {
			  echo "Error: " . $e->getResponse();
			}
			
			echo '<p>'.$feed->totalResults.' GC Events found between '.$start_date.' and '.$end_date.'</p>';
			
			//echo '<p><ul>';
			// Each Google Calendar Event's data
			foreach ($feed as $event) {
							
				// DEBUG display
				//echo "<li>Title: " . $event->title. "</li>";
				//echo "<li>Location: ". $event->where[0]."</li>";
				$id = substr($event->id, strrpos($event->id, '/')+1);
				
				foreach ($event->when as $when) {
					$startDateTime = new DateTime($when->startTime);
					$endDateTime = new DateTime($when->endTime);
					
					$event_day = $startDateTime->format('Y-m-d');
					// DEBUG display
					//echo "<li>Event Day: ".$event_day."</li>";
					//echo "<li>Start Time: ".$startDateTime->format('H:i')."</li>";
					//echo "<li>End Time: ".$endDateTime->format('H:i')."</li>";
					
					// Load gc_events array
					$gc_events[] = $event->title.'*'.$event_day.'*'.$startDateTime->format('H:i').'*'.$endDateTime->format('H:i');
				}
				
			}
			//echo "</ul></p>";
			
			// DEBUG - display $gc_events[] contents
			if (!empty($gc_events)) {
				echo '<p>gc_events[]<br />';
				foreach ($gc_events as $evt) {
					echo "$evt<br />\n";
				}
				echo '</p>';
			}
			
			
			$evt_counter = 0;
			//Get the event info
			//MySQL date_format: %l is hour, %i is the minutes, %p is am/pm
			$getEvents_sql = "SELECT title, description,
									date_format(start, '%Y-%m-%d') as fmt_start_dt, 
									date_format(end, '%Y-%m-%d') as fmt_end_dt,
									date_format(start, '%H') as fmt_start_hh,
									date_format(start, '%i') as fmt_start_mm, 							
									date_format(end, '%H') as fmt_end_hh,
									date_format(end, '%i') as fmt_end_mm
							FROM fc_events 
							WHERE rep_id = '$repid' ";
			$r = mysqli_query($dbc, $getEvents_sql);
			if (mysqli_num_rows($r) > 0){
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					
					$title = $row['title'];
					$desc = $row['description'];
					$startDate = $row['fmt_start_dt'];
					$startTime = $row['fmt_start_hh'].':'.$row['fmt_start_mm'];
					$endDate = $row['fmt_end_dt'];
					$endTime = $row['fmt_end_hh'].':'.$row['fmt_end_mm'];
					
					$vfg_events[] = $title.'*'.$startDate.'*'.$startTime.'*'.$endTime;
					
					// loop thru $gc_events[] array
					// if title from $gc_events[] matches title in record, DO NOT ADD IT
					$event_match = false;
					foreach ($gc_events as $evt){
						
						// explode each entry
						list($evttitle, $evtday, $evtstart, $evtend) = explode("*", $evt);
						if ( ($title == $evttitle) && ($startDate == $evtday) && ($startTime == $evtstart) && ($endTime == $evtend) ){
							$event_match = true;
						}
					}
					
					if (!$event_match){
						$evt_counter ++;
						// CREATE EVENTS IN THE GOOGLE CALENDAR
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
							//echo "Error: " . $e->getResponse();
						}
					}	//end !$event_match
					
				}
				mysqli_free_result($r);
				
				// DEBUG - display $gc_events[] contents
				if (!empty($vfg_events)) {
					echo '<p>vfg_events[]<br />';
					foreach ($vfg_events as $evt) {
						echo "$evt<br />\n";
					}
					echo '</p>';
				}
			
			} 
			
			echo 'Events Added: '.$evt_counter;
			
			mysqli_close($dbc);
		
		?>
		
		
		<div class="cleardiv"></div>
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>