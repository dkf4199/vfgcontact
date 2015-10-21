<?php
include('includes/gc_AuthSub_calendar_connect.php');
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
include ('includes/selectlists.php');
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
<title>VFG Contact - Add Google Calendar Event</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/google_calendar.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {

	$("#datepicker_startdate").datepicker({ dateFormat: "mm-dd-yy" });
	//$("#datepicker_enddate").datepicker({ dateFormat: "mm-dd-yy" });
		
});	//end jquery ready
</script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
				
		<?php
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "addgcevent"){
				
				$errors = array();		//initialize errors array
				$iomsgs = array();
				
				//VALIDATE FORM FIELDS
				//*******************************************************
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
				date_default_timezone_set($_SESSION['rep_tz']);
				
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
					
					//convert start/end times to proper format
					$start = date(DATE_ATOM, mktime($st_hour, $st_min, 0, $sd_mm, $sd_dd, $sd_yy));
					//$end = date(DATE_ATOM, mktime($et_hour, $et_min, 0, $ed_mm, $ed_dd, $ed_yy));
					 
					/* dkf make the end date portion of the event the same as the start date */
					$end = date(DATE_ATOM, mktime($et_hour, $et_min, 0, $sd_mm, $sd_dd, $sd_yy));
					
					// construct event object
					// save to server      
					try {
						$event = $gcal->newEventEntry();        
						$event->title = $gcal->newTitle($title);        
						$when = $gcal->newWhen();
						$when->startTime = $start;
						$when->endTime = $end;
						$event->when = array($when);        
						$gcal->insertEvent($event);

						$iomsgs[] = 'Event successfully added.';
						
					} catch (Zend_Gdata_App_Exception $e) {
						//die("Error: " . $e->getResponse());
						$iomsgs[] = "Error: " . $e->getResponse();
					}
					
				} //end empty(errors)
				
			} //end form submit	
		?>
		<!-- Add Calendar Event form -->
		<div class="formbox roundcorners opacity70" >
			<div id="gcActions">
				<a class="googlelink" href="rep_calendar.php">Back To Calendar</a>
			</div>
			<h2>Add Calendar Event</h2>
			<div class="webform">
			<form name="gc_addnewevent_form" id="gc_addnewevent_form" action="rep_calendar_addevent.php" method="POST" >
				<ul>
					<li>
						<label>Event title:</label>
						<input name="gce_title" id="gce_title" type="text" size="40" 
								value="<?php if (isset($_POST['gce_title'])) echo $_POST['gce_title']; ?>" />
					</li>
					<li>
						<label>Event date<br /> (mm-dd-yyyy):</label>
						<input name="gce_startdate" id="datepicker_startdate" type="text" 
							value="<?php if (isset($_POST['gce_startdate'])) echo $_POST['gce_startdate']; ?>" />
					</li>
					<!--<li>
						<label>End date<br /> (mm-dd-yyyy):</label>
						<input name="gce_enddate" id="datepicker_enddate" type="text" 
							value="" />
					</li>-->
					<li>
						<label>Event Start Time:</label>
						<?php
							$selected_stime = "";
							if (isset($_POST['gce_starttime'])){
								$selected_stime = $_POST['gce_starttime'];
							}			
							echo '<select name="gce_starttime" id="gce_starttime">';

							foreach($calendartime as $id=>$name){
								if($selected_stime == $id){
									$sel = 'selected="selected"';
								}
								else{
									$sel = '';
								}
								echo "<option $sel value=\"$id\">$name</option>";
							}
						?>	
						</select>
					</li>
					<li>
						<label>Event End Time:</label>
						<?php
							$selected_etime = "";
							if (isset($_POST['gce_endtime'])){
								$selected_stime = $_POST['gce_endtime'];
							}			
							echo '<select name="gce_endtime" id="gce_endtime">';

							foreach($calendartime as $id=>$name){
								if($selected_etime == $id){
									$sel = 'selected="selected"';
								} else{
									$sel = '';
								}
								echo "<option $sel value=\"$id\">$name</option>";
							}
						?>
						</select>
					</li>
					<li>
						<input type="hidden" name="submitted" value="addgcevent" />
						<input name="submit" type="submit" class="button" value="Add Event" />
					</li>
				</ul>
				<div id="messages">
					<?php
						//Display error messages, if any.
						if (!empty($errors)) {
							echo 'ERROR:<br />';
							foreach ($errors as $msg) {
								echo " - $msg<br />\n";
							}
						}
						//Display script messages, if any.
						if (!empty($iomsgs)) {
							foreach ($iomsgs as $msg) {
								echo "$msg<br />\n";
							}
						}
					?>
				</div>
			</form>
			</div>
		</div>	
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>