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
//Zend_Loader::loadClass('Zend_Http_Client');
// Create an instance of the Calendar service, redirecting the user
// to the AuthSub server if necessary.
$service = new Zend_Gdata_Calendar(getAuthSubHttpClient());
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
<title>VFG Contact - Google Calendar</title>
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
<div id="wrapper" style="width: 1500px; min-width: 1500px;">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
				
		<?php
			/*
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
			$q = "SELECT gmail_acct, gmail_pass
				  FROM reps
				  WHERE rep_id = '$repsid'";	
				
			$r = @mysqli_query ($dbc, $q); // Run the query.

			if ($r) { // If it ran OK, set credentials.
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$em = $row['gmail_acct'];
					$pass = $row['gmail_pass'];
				}
				mysqli_free_result($r);
				//echo $em.' '.$pass.'.';
			}*/
		?>
		
		<div id="gcContainer" class="opacity80">
			<?php
				$em = $pass = $tz = '';
				if (isset($_SESSION['rep_gmail'])){
					$em = $_SESSION['rep_gmail'];
				}
				if (isset($_SESSION['rep_gpass'])){
					$pass = $_SESSION['rep_gpass'];
				}
				$tz = $_SESSION['rep_tz'];
					
				//REMEMBER: The calendar in this iframe ONLY NEEDS the google email....not password
				if ($em != ''){
					//echo '<p>'.$em.'</p>';
					//Display the view and add links
					echo '<div id="gcActions">
							<a class="googlelink" href="rep_calendar_viewevents.php">View Events</a>
							<a class="googlelink" href="rep_calendar_addevent.php">Add Event</a>
						  </div>';	
					echo '<iframe src="https://www.google.com/calendar/embed?src='.urlencode($em).'&ctz='.$tz.'" 
							style="border: 0" frameborder="0" width="100%" height="600px" scrolling="no"></iframe>';
				} elseif ($em == '' && $pass == ''){
					echo '<p>Could not retrieve Google Calendar.</p>
					  <p>If you haven\'t set your google account and password 
							go to <a href="rep_edit_profile.php">Rep Edit Profile</a> and do that now.</p>';
				}		
			?>
		</div> <!-- close gcContainer -->
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>