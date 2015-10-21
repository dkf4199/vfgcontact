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
//Link back fields for edit screen
$_SESSION['from_page'] = 'rep_search_last.php';
$_SESSION['back_btn_text'] = 'Back To Last Name Search';
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
<title>VFG Contact - Last Name Search</title>
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
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/tcs_modal_handlers.js'></script>
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
		<div style="text-align:center;">
			<form method="get" action="rep_search_last.php">
				<label>Search By Last Name:</label>
				<input type="text" name="srch" id="srch" class="input100" value="<?php if (isset($_GET['srch'])) echo $_GET['srch']; ?>" />
				<input type="submit" value="Search" />
			</form>
		</div>
		
		<div id="ajax_viewcontacts" class="opacity80">	
		<?php
			$repsid = $_SESSION['rep_id'];			//rep_id
			$repsvfgid = $_SESSION['vfgrep_id'];	//vfg id
			
			if (isset($_GET['srch']) && $_GET['srch'] != ''){
				$searchstr = $_GET['srch'];
			} else {
				$searchstr = '';
			}
			
			//Link back fields for edit screen
			$_SESSION['from_page'] = 'rep_search_last.php';
			$_SESSION['back_btn_text'] = 'Back To Last Name Search';
			$_SESSION['srch_string'] = $searchstr;
			
			include ('includes/config.inc.php');
			include ('includes/phpfunctions.php');
			//DB Connection
			require_once (MYSQL);
			
			//Get Results if $searchstr isn't empty
			if ($searchstr != ''){
				//UNION query
				$contactsquery = "(SELECT 'contacts' AS from_table,
								  contact_id,
								  firstname,
								  lastname,
								  email,
								  phone,
								  tier_status,
								  entry_date 
							FROM contacts 
							WHERE lastname LIKE '$searchstr%'
							AND (rep_id = '$repsid' 
								 OR assigned_manager = '$repsvfgid' 
								 OR assigned_consultant = '$repsvfgid' 
								 OR additional_rep = '$repsvfgid')
							ORDER BY entry_date desc)
						UNION
						  (SELECT 'dumpedcontacts' AS from_table,
								   contact_id,
								   firstname,
								   lastname,
								   email,
								   phone,
								   tier_status,
								   entry_date 
							 FROM dumped_contacts
							 WHERE lastname LIKE '$searchstr%'
							 AND rep_id = '$repsid'
							 ORDER BY entry_date desc)";
				//**********************************************
				$r = @mysqli_query ($dbc, $contactsquery); // Run the query.

				if ($r) { // If it ran OK, display the records.
					
					echo '<table id="csstable">
						<tr>
							<th colspan=7>SEARCH RESULTS</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Entry Date</th>
							<th scope="col">Tier</th>
						</tr>';
				
					//alternate row class css spec: spec, specalt
					$rowspec = 'specalt';	
					// Fetch and print all the records:
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						
						$rawdate = strtotime( $row['entry_date'] );
						$formatted_entrydt = date( 'm-d-Y h:i a', $rawdate );
						
						//switch the rowspec value
						//$rowspec = ($rowspec=='#ffffff' ? '#f5fafa' : '#ffffff');
						$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
											
						//create each row
						
						echo '<tr class="'.$rowspec.'">';
						if ($row['from_table'] == 'contacts'){
							echo '<td><div id="contact-form"><a href="rep_edit_contact.php?s=&cid='.$row['contact_id'].'" class="bluelink" >Edit</a></div>
									&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Dump</a></div></td>';
						} elseif ($row['from_table'] == 'dumpedcontacts'){
							echo '<td><div id="restorecontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Restore</a></div>
									&nbsp;<div id="flushcontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Flush</a></div></td>';
						}
								
						echo '<td>'.$row['lastname'].'</td>
							  <td>'.$row['firstname'].'</td>
							  <td>'.$row['email'].'</td>';
						
						if ($row['phone'] != ''){
							//echo '<td>'.$row['phone'].'<a href="'.$row['contact_id'].'" class="phonelist_modal" >
							//			<img src="./images/mobile_phone.png" class="phonelink"/></a></td>';
							// REMOTE DIALER AJAX CALL
							echo '<td>'.$row['phone'].'<a onclick="callAjax( \''.$row['phone'].'\');" class="phonelist_modal" href="#">
										<img src="./images/smallicons/remotedial.png" class="phonelink"/></a></td>';
						} else {
							echo '<td>'.$row['phone'].'</td>';
						}
						
						echo '<td>'.$formatted_entrydt.'</td>
							  <td>'.substr($row['tier_status'],0,1).'</td>	
							  </tr>';
						
					}
					
					echo '</table>';
					
					mysqli_free_result ($r); // Free up the resources.	

				} else { // If it did not run OK.

					// Public message:
					echo '<p>Your contacts could not be retrieved. We will fix the problem shortly.</p>';
					//echo '</div>';
					
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $contactquery . '</p>';
					
				} // End of if ($r)
				
			} else {
				echo '<table id="csstable">
						<tr>
							<th colspan=7>SEARCH RESULTS</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Entry Date</th>
							<th scope="col">Tier</th>
						</tr></table>';
			}	//end if empty $searchstr
			
			mysqli_close($dbc); // Close the database connection.

		?>
		</div> <!-- close ajax_viewcontacts -->
		<div class="cleardiv"></div>

	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>