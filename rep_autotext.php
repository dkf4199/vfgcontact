<?php
session_start();
// Include the database sessions file:
// The file starts the session.
//require('includes/db_sessions.inc.php');
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
date_default_timezone_set($_SESSION['rep_tz']);
if (isset($_SESSION['unique_id'])){ 
	$dialer_unique_id = $_SESSION['unique_id'];
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
<title>VFG Contacts - Rep Dashboard</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfg_jquery_ui_menu.css" />
<script src="./js/jquery-1.9.1.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
<script>
$(document).ready(function() {
	$( "#menu" ).menu();
});
</script>

</head>

<body style="background-image:url(images/tcsfade.jpg);">
<div id="wrapper">
	<!-- Header -->
	<?php 
		//include('includes/html/vfg_logohead_and_menu_color.html');
		include('includes/html/newheader_log.html');
	?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
			
			<!-- DIALER LOOP TEST -->
			<?php
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				include ('includes/selectlists.php');
				//DB Connection
				require_once (MYSQL);
				
				$txt_msg = "This is test text number ";
				
				$contact_phone = $_SESSION['rep_phone'];	//Dial my own number
				
				//Loop the dialer section
				for ($x=1; $x<=10; $x++){
					// SET UP DIALER to send text - ONLY if rep's unique_id field is set
					//
					$rightnow = date("Y-m-d H:i:s");
					if ($dialer_unique_id != ''){
					
						//initialize status
						$status = "0";
						
						//Create key field for temp_contacts table field "id_temp_contact" 
						$idexists = true;
						do {
							$randnum = mt_rand(1,99999);
							$strnum = strval($randnum);

							switch (strlen($strnum)) {
							case 1:
								$finalnum = '0000'.$strnum;
								break;
							case 2:
								$finalnum = '000'.$strnum;
								break;
							case 3:
								$finalnum = '00'.$strnum;
								break;
							case 4:
								$finalnum = '0'.$strnum;
								break;
								case 5:
								$finalnum = $strnum;
								break;
							}
							$tempcontactid= $finalnum;
							
							// Is value already in id_temp_contact in temp_contacts table?
							$query = "SELECT id_temp_contact FROM temp_contacts WHERE id_temp_contact = '".$tempcontactid."'";
							$rs = @mysqli_query ($dbc, $query);
							if (mysqli_num_rows($rs) != 1) {
								  //id is unique
								  $idexists = false;
							}
							mysqli_free_result($rs);
							
						} while ($idexists);
					
						$text_msg = $txt_msg.$x.'.';
						//NOTE: Change this next routine....send the phone # into this program via ajax call.
						//      No need to go to db to retrieve this.
						if ($contact_phone != ''){
							$insquery = "INSERT INTO temp_contacts (id_temp_contact, unique_id, phone, comm_note, timestamp, status) 
										VALUES ('$tempcontactid', '$dialer_unique_id', '$contact_phone', '$text_msg', '$rightnow', '$status')";
							$result = mysqli_query ($dbc, $insquery);
							if (mysqli_affected_rows($dbc) == 1){
								echo 'Text '.$x.' sent via Dialer.<br />';
							} else {
								echo 'Dialer problem with text message.';
								//echo '<p>'.mysqli_error($dbc).'</p>';
							}
						}
						
					} //end if($dialer_unique_id)
				
				} //end for loop
				
				mysqli_close($dbc);
			?>
		
		
		<div class="cleardiv"></div>
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<div class="cleardiv"></div>
<?php include('includes/html/footer.html'); ?>
</body>
</html>