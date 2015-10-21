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
$_SESSION['from_page'] = 'rep_manage_recruits.php';
$_SESSION['back_btn_text'] = 'Back To Manage Prospects';
$_SESSION['srch_string'] = '';
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
<title>VFG Contact - Rep Team</title>
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
	<?php include('includes/html/newheader_log.html'); ?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		<div id="dialermessages"></div>		
		<div id="ajax_viewcontacts" class="opacity80">
			<?php
				$repsid = $_SESSION['rep_id'];
				
				$team_stats_id = '';
				if (isset($_SESSION['team_stats_id'])){
					$team_stats_id = $_SESSION['team_stats_id'];
				}
				
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				//DB Connection
				require_once (MYSQL);
				//*********************************************************
				//Pagination
				//*********************************************************
				//Max Display
				$display = 20;

				if ($team_stats_id != ''){
					
					if (isset($_GET['p']) && is_numeric($_GET['p'])) {
						//already determined
						$pages = $_GET['p'];

					} else {
						//need to determine based on count in db
						//
						 $q = "SELECT count(rep_id) FROM reps
							   WHERE team_stats_id ='$team_stats_id' ";
						 $r = @mysqli_query($dbc, $q);

						 $row = @mysqli_fetch_array($r, MYSQLI_NUM);
						 $records = $row[0];
						 mysqli_free_result($r);
						 
						 if ($records > $display) {
							 //more than one page
							 $pages = ceil($records/$display);
						 } else {
							 $pages = 1;
						 }
					} //end if get 'p'

					//determine where in database to return results from
					if (isset($_GET['s']) && is_numeric($_GET['s'])){
						$start = $_GET['s'];
					} else {
						$start = 0;
					}
					
					//Get total recs for refresh
					$q = "SELECT count(rep_id) FROM reps
							   WHERE team_stats_id ='$team_stats_id' ";
					$r = @mysqli_query($dbc, $q);
					$row = @mysqli_fetch_array($r, MYSQLI_NUM);
					$_SESSION['total_team_recs'] = $row[0];
					mysqli_free_result($r);
					
					
					// Make the query:
					
					$q = "SELECT firstname, lastname, gmail_acct, phone 
						  FROM reps
						  WHERE team_stats_id = '$team_stats_id'
						   ORDER BY lastname, firstname
						  LIMIT $start, $display";	
										
					$r = @mysqli_query ($dbc, $q); // Run the query.

					if ($r) { // If it ran OK, display the records.

						echo '<table id="csstable">
							<tr>
								<th colspan=8>YOUR TEAM ('.$_SESSION['total_team_recs'].' total)</th>
							</tr>
							<tr>
								<th scope="col">Lastname</th>
								<th scope="col">Firstname</th>
								<th scope="col">Email</th>
								<th scope="col">Phone</th>
							</tr>';
					
						//alternate row class css spec: spec, specalt
						$rowspec = 'specalt';	
						// Fetch and print all the records:
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							
													
							//switch the rowspec value
							//$rowspec = ($rowspec=='#ffffff' ? '#f5fafa' : '#ffffff');
							$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
												
							# 09/24/2013 Take the modal link off the edit.  Going back to a regular screen for the edits.
							echo '<tr>
									<td>'.$row['lastname'].'</td>
									<td>'.$row['firstname'].'</td>
									<td>'.$row['gmail_acct'].'</td>';
							if ($row['phone'] != ''){
								// REMOTE DIALER AJAX CALL
								echo '<td>'.$row['phone'].'<a onclick="callAjax( \''.$row['phone'].'\');" class="phonelist_modal" href="#">
											<img src="./images/smallicons/remotedial.png" class="phonelink"/></a></td>';
							} else {
								echo '<td>'.$row['phone'].'</td>';
							}
									
							echo '</tr>';
							
						}
						
						//CREATE PAGE LINKS TO OTHER RECS
						if ($pages > 1) {
							
							echo '<tr>
									<th colspan=4 align="center">
									<div class="paginator">';
							

							$current_page = ($start/$display) + 1;

							//if not first page - make previous button
							if ($current_page != 1) {
								echo '<a href="rep_team.php?s='.($start - $display).'&p='.$pages.'">Previous</a>&nbsp;';
								//echo '<a href="rep_view_contact.php?&s='.($start - $display).'&p='.
								//	$pages.'">Previous</a> ';
							}
							//make number links to pages
							for ($i = 1; $i <= $pages; $i++){
								if ($i != $current_page){
									echo '<a href="rep_team.php?s='.(($display * ($i - 1))).'&p='.$pages.'">'.$i.'</a>&nbsp;';
									//echo '<a href="rep_view_contact.php?&s=' .
									//	(($display * ($i - 1))) . '&p=' .
									//	$pages . '">' . $i . '</a> ';
								} else {
									//current page isn't a link
									echo $i . ' ';
								}
							} //end for loop
									
							//not last page - make a next link
							if ($current_page != $pages){
								echo '&nbsp;<a href="rep_team.php?s='.($start + $display).'&p='.$pages.'">Next</a>';
								//echo '<a href="rep_view_contact.php?&s='.($start + $display).'&p='.
								//	$pages.'">Next</a> ';
							}

							//close p
							//echo '</p>';

							//Close Paginator Div and Table
							echo '</div>
									</th></tr>
									</table>';
								
						} else {
							//no nav needed
							echo '</table>';
						}//end if ($pages > 1)
						
						//echo '</div> <!-- close viewleads -->';

						mysqli_free_result ($r); // Free up the resources.	

					} else { // If it did not run OK.

						// Public message:
						echo '<p>Your team could not be retrieved. We will fix the problem shortly.</p>';
						//echo '</div>';
						
						// Debugging message:
						//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
						
					} // End of if ($r)

					mysqli_close($dbc); // Close the database connection.
				
				} else {
					echo '<p>Your Team Stats Id field is not set on your profile.  No team data retrieved.</p>';
				
				}	//end if($team_stats_id != '')
				
			?>
		</div> <!-- close ajax_viewcontacts -->
		<div class="cleardiv"></div>
		
		
	</div> <!-- close main content -->

</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>