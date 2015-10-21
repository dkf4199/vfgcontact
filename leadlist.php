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
include ('includes/config.inc.php');
include ('includes/phpfunctions.php');
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
<title>VFG Contact - All Leads</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/memory_jogger_handlers.js'></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
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
		
			<!-- refresh button -->
			<div style="text-align: center;">
				<form action="leadlist.php">
					<input type="submit" id="refresh_listing" class="generalbutton" value="Refresh List" />
				</form>
			</div>
			<?php
				$repsid = $_SESSION['rep_id'];
				//DB Connection
				require_once (MYSQL);
				
				$allleads = 0;
				//count(*) of leads
				$t = "SELECT count(rep_id) FROM lead_list
				   WHERE rep_id='$repsid'
				   AND is_prospect = 'N'";
				$r = @mysqli_query($dbc, $t);

				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$allleads = $row[0];
				mysqli_free_result($r);
				
				// Make the query:			
				$q = "SELECT l_id, firstname, lastname, phone, email,
							 priority_number, category, is_prospect, entry_date
					  FROM lead_list 
					  WHERE rep_id = '$repsid'
					  AND is_prospect = 'N'
					  ORDER BY priority_number, entry_date ";	
				
				$r = @mysqli_query ($dbc, $q); // Run the query.
				if ($r) { // If it ran OK, display the records.

					echo '<div id="ajax_leadlist">
						<table id="csstable">
						<tr>
							<th colspan=8>YOUR LEADS ('.$allleads.' total)</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Category</th>
							<th scope="col">Priority</th>
							<th scope="col">Entry Date</th>
						</tr>';
				
					//alternate row class css spec: spec, specalt
					$rowspec = 'specalt';	
					// Fetch and print all the records:
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						echo '<tr>
								<td><a href="#" onClick="editLead(\''.$row['l_id'].'\')">Edit</a></td>
								<td>'.$row['lastname'].'</td>
								<td>'.$row['firstname'].'</td>
								<td>'.$row['email'].'</td>
								<td>'.$row['phone'].'</td>
								<td>'.$row['category'].'</td>
								<td>'.$row['priority_number'].'</td>
								<td>'.$row['entry_date'].'</td>
							  </tr>';
						
					}
					echo '</table>';
				
				} else { // If it did not run OK.

					// Public message:
					echo '<p>Your leads could not be retrieved. We will fix the problem shortly.</p>';
							
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
				} // End of if ($r)
				echo '</div>';
				mysqli_close($dbc); // Close the database connection.		
			?>
			
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>