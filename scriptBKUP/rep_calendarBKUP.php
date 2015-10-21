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
<title>EAPCRM Leads Management - It's Easy As Pie</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/google_calendar.css" />
<script type="text/javascript" src="./js/autotab.js"></script>
<script src="./js/jquery1-9-1.js"></script>
<script>
$(document).ready(function() {

	//Company dropdown list
	$('#gc_button').click(function() {
		//alert("GC_BUTTON clicked");
		var errors = '';
		
		var email = $("#email").val();
		var pass = $("#password").val();
		if (email == '') {errors = 'Email required.';}
		if (pass == '') {errors += ' Password required.';}
		
		if (errors != ''){
			$("#gc_fielderrors").text(errors);
		} else {
		
			//alert('Tier: '+current_toptier+"\n"+'Tier Step: '+current_toptierstep);
			$.ajax({
				url:"ajax_getcalendar.php",
				type: "GET",
				data: {email: email, 
					   password: pass},
				dataType: "html",		
				success:function(result){
					$("#gcContainer").html(result);
					//$("#ajax_updatecontact").html("");
			}}); //end success:function
		}
	});
	
	
});	//end ready
</script>

</head>

<body>
<div class="wrapper showgoldborder">
	<!-- Header include -->
	<?php include('includes/html/header_log.html'); ?>
	
	<div class="maincontent">
		<p class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></p>
		
		<?php
			//CHECK FOR AUTHENTICATION CREDENTIALS
			$repsid = $_SESSION['rep_id'];
			$tz = $_SESSION['rep_tz'];
			include ('includes/config.inc.php');
			include ('includes/phpfunctions.php');
			//DB Connection
			require_once (MYSQL);
			
			$em = '';
			$pass = '';
			
			// Make the query:
			$q = "SELECT gc_email, gc_pass
				  FROM gc_settings
				  WHERE rep_id = '$repsid' 
				  LIMIT 1";	
				
			$r = @mysqli_query ($dbc, $q); // Run the query.

			if ($r) { // If it ran OK, display the calendar.
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$em = $row['gc_email'];
					$pass = $row['gc_pass'];
					$_SESSION['rep_google_email'] = $em;
					$_SESSION['rep_google_pass'] = $pass;
					
				}
				mysqli_free_result($r);
			}
			
		?>
		<!-- Google Calendar Credentials 
		<div class="googlecalendardiv">
			<!--<form name="gc_form" id="gc_form" onSubmit="return getGCalendar()" >
				<label for="email">Google Email:</label>
				<input type="text" name="email" id="email" class="input200" value="" />
				<label for="first_name">Password:</label>
				<input type="password" id="password" name="password" class="input150" value="" />
				<input type="button" id="gc_button" class="googlebutton" value="Get Calendar" />
				<span id="gc_fielderrors" class="gc_errorspan"></span>
			<!--</form>
		</div>-->
		
		
		<div id="gcActions">
			<a class="googlelink" href="rep_calendar_viewevents.php">View Events</a>
			<a class="googlelink" href="rep_calendar_addevent.php">Add Event</a>
		</div>
		
		<div id="gcContainer">
			<?php
				$repsid = $_SESSION['rep_id'];
				$tz = $_SESSION['rep_tz'];
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				//DB Connection
				require_once (MYSQL);
				
				$em = '';
				$pass = '';
				
				// Make the query:
				$q = "SELECT gc_email, gc_pass
					  FROM gc_settings
					  WHERE rep_id = '$repsid' 
					  LIMIT 1";	
					
				$r = @mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the calendar.
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						$em = $row['gc_email'];
						$pass = $row['gc_pass'];
						$_SESSION['rep_google_email'] = $em;
						$_SESSION['rep_google_pass'] = $pass;
						
					}
					if ($em != '' && $pass != ''){
						//echo '<p>'.$em.' '.$pass.'</p>';
						echo '<iframe src="https://www.google.com/calendar/embed?src='.urlencode($em).'&ctz='.$tz.'" 
								style="border: 0" frameborder="0" width="100%" height="600px" scrolling="no"></iframe>';
					} elseif ($em == '' && $pass == ''){
						echo '<p>Could not retrieve Google Calendar.</p>
						  <p>If you haven\'t set your google account and password on the "Calendar Settings" page,<br />
						  please go to <a href="rep_gc_settings.php">Calendar Settings</a> and do that now.</p>';
					}
					mysqli_free_result($r);
				} else {
					
					//didn't retrieve data from gc_settings
					echo '<p>Could not retrieve Google Calendar settings.</p>';
				}
			?>
		</div> <!-- close ajax_googlecalendar -->
		
		<!-- div for update form -->
		<!--<div id="ajax_updatecontact"></div>-->
		
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
</body>
</html>