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
$_SESSION['from_page'] = 'rep_todays_tasks_and_events.php';
$_SESSION['back_btn_text'] = 'Back To Tasks and Events';
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
<title>VFG Contact - Today's Tasks</title>
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
				
		<div id="ajax_viewcontacts" class="opacity80">
			<form action="rep_todays_tasks_and_events.php" method="GET">
				<label>Select Tasks for:</label>
				<input type="text" name="nextactiondate" id="datepickerstatic" 
						value="<?php if (isset($_GET['nextactiondate'])) echo $_GET['nextactiondate']; ?>" />
				<input type="submit" id="taskbutton" value="Get Tasks" />
			</form>
			<br />
			<?php
				// Get Contact's Tasks from VFGContacts For Date Given
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				//DB Connection
				require_once (MYSQL);
				
				$repsid = $_SESSION['rep_id'];
				$repsvfgid = $_SESSION['vfgrep_id'];	//vfg id
				
				//mysql date is yyyy-mm-dd
				$mysqldate = $displaydate = '';
				
				// nextactiondate is from the datepicker, in mm-dd-yyyy form
				// MySQL uses yyyy-mm-dd for date
				// we are displaying this thing as mm-dd-yyyy
				if (isset($_GET['nextactiondate']) && $_GET['nextactiondate'] != ''){
					$actiondate = $_GET['nextactiondate'];
					list($ad_mm,$ad_dd,$ad_yy) = explode('-',$actiondate);
					$displaydate = $ad_mm.'-'.$ad_dd.'-'.$ad_yy;
					$mysqldate = $ad_yy.'-'.$ad_mm.'-'.$ad_dd;
				} else {
					$mysqldate = date('Y-m-d');
					$displaydate = date('m-d-Y');
				}
				
				if (isset($_GET['actiondate'])){
					$mysqldate = $_GET['actiondate'];
					list($ad_yy,$ad_mm,$ad_dd) = explode('-',$mysqldate);
					$displaydate = $ad_mm.'-'.$ad_dd.'-'.$ad_yy;
				}
				
				date_default_timezone_set($_SESSION['rep_tz']);
				
				// DISPLAY TODAY'S EVENTS from fc_events
				//
				//MySQL date_format: %l is hour, %i is the minutes, %p is am/pm
				list($yy,$mm,$dd) = explode('-',$mysqldate);
				$getEvent_sql = "SELECT id, rep_id, title, description, 
										date_format(start, '%l:%i %p') as fmt_start, 
										date_format(end, '%l:%i %p') as fmt_end
								FROM fc_events 
								WHERE rep_id = '$repsid' 
								AND month(start) = '$mm'
								AND dayofmonth(start) = '$dd' 
								AND year(start)= '$yy' 
								ORDER BY start";
				$r = mysqli_query($dbc, $getEvent_sql);

				if (mysqli_num_rows($r) > 0){
					echo '<div style="text-align:justify;width:50%;margin:0 auto;">
						   <p align="center">VFG Calendar Events for '.$displaydate.'</p>
					       <ul>';
					while ($ev = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						$event_title = stripslashes($ev['title']);
						$event_desc = stripslashes($ev['description']);
						$fmt_start = $ev['fmt_start'];
						$fmt_end = $ev['fmt_end'];
						$eid = $ev['id'];
						echo '<li><strong>'.$fmt_start.' to '.$fmt_end.'</strong>  '.
								  $event_title.' - '.$event_desc.
								  '</li>';
					}
					echo '</ul></div>';
					mysqli_free_result($r);
				} 
				
				
				//*********************************************************
				//Pagination
				//*********************************************************

				//Max Display
				$display = 10;

				if (isset($_GET['p']) && is_numeric($_GET['p'])) {
					//already determined
					$pages = $_GET['p'];

				} else {
					//need to determine based on count in db
					//
					 /*$q = "SELECT count(rep_id) FROM contacts
					  WHERE rep_id='$repsid'
					  AND next_action_date='$mysqldate'";*/
					$q = "SELECT count(rep_id) FROM contacts
						  WHERE nad_set_by='$repsid'
						  AND next_action_date='$mysqldate'";
					$r = @mysqli_query($dbc, $q);

					$row = @mysqli_fetch_array($r, MYSQLI_NUM);
					$records = $row[0];
					 
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
				//Get total recs for refresh purposes
				/*$q = "SELECT count(rep_id) FROM contacts
					  WHERE rep_id='$repsid'
					  AND next_action_date='$mysqldate'";*/
				$q = "SELECT count(rep_id) FROM contacts
					  WHERE nad_set_by='$repsid'
					  AND next_action_date='$mysqldate'";
				$r = @mysqli_query($dbc, $q);
				$row = @mysqli_fetch_array($r, MYSQLI_NUM);
				$_SESSION['todays_total_recs'] = $row[0];
				mysqli_free_result($r);
				
				/*
				//ASC and DESC - TOGGLE
				$sort_order = 'DESC'; 
				if(isset($_GET['sortorder']))	{ 
					if($_GET['sortorder'] == 'ASC') { 
						$sort_order = 'DESC'; 
					} else { 
						$sort_order = 'ASC'; 
					} 
				} 
				//SORT OPTION
				$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'entrydt';
				switch ($sort){
					case 'ln':
						case 'ln':
							$order_by = 'a.lastname '.$sort_order;
							break;
						case 'entrydt':
							$order_by = 'a.entry_date '.$sort_order;
							break;
					default:
						$order_by = 'a.entry_date ASC';
						break;
				}
				*/

				// Make the query:
				//01/14/2014 include contacts assigned to rep
				//
				// DKF 03/18/2014 Take out the roles....it duplicates the results
				//
				/*$q = "SELECT a.contact_id, a.firstname, a.lastname, a.email, 
							a.phone, a.tier_status, a.entry_date, a.contact_type, b.status_desc, 'Your Contact' as role 
					  FROM contacts a
					  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
					  WHERE a.rep_id = '$repsid'
					  AND a.nad_set_by = '$repsid' 
					  AND a.next_action_date = '$mysqldate'
					  UNION
					  SELECT a.contact_id, a.firstname, a.lastname, a.email, 
							a.phone, a.tier_status, a.entry_date, a.contact_type, b.status_desc, 'Manager' as role 
					  FROM contacts a
					  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
					  WHERE a.assigned_manager = '$repsvfgid' 
					  AND a.nad_set_by = '$repsid' 
					  AND a.next_action_date = '$mysqldate'
					  UNION
					  SELECT a.contact_id, a.firstname, a.lastname, a.email, 
							a.phone, a.tier_status, a.entry_date, a.contact_type, b.status_desc, 'Consultant' as role 
					  FROM contacts a
					  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
					  WHERE a.assigned_consultant = '$repsvfgid' 
					  AND a.nad_set_by = '$repsid' 
					  AND a.next_action_date = '$mysqldate'
					  UNION
					  SELECT a.contact_id, a.firstname, a.lastname, a.email, 
							a.phone, a.tier_status, a.entry_date, a.contact_type, b.status_desc, 'Additional Rep' as role 
					  FROM contacts a
					  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
					  WHERE a.additional_rep = '$repsvfgid' 
					  AND a.nad_set_by = '$repsid'
					  AND a.next_action_date = '$mysqldate'
					  LIMIT $start, $display";
				*/
				$q = "SELECT a.contact_id, a.firstname, a.lastname, a.email,							
							a.phone, a.tier_status, a.entry_date, a.contact_type, 
							a.rep_id, a.assigned_manager, a.assigned_consultant, a.additional_rep,
							b.status_desc 
					  FROM contacts a
					  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
					  WHERE a.nad_set_by = '$repsid' 
					  AND a.next_action_date = '$mysqldate'
					  LIMIT $start, $display";
				$r = @mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the records.

					//<th scope="col"><a href="rep_todays_tasks_and_events.php?sort=entrydt&sortorder='.$sort_order.'&actiondate='.$mysqldate.
					//					'&nextactiondate='.$displaydate.'">Entry Date</a></th>
					echo '<table id="csstable">
						<tr>
							<th colspan=8>CONTACTS WITH TASKS ('.$_SESSION['todays_total_recs'].' total) for '.$displaydate.'</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Tier</th>
							<th scope="col">Status</th>
							<th scope="col">Roles</th>
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
														
						# 09/24/2013 Take the modal link off the edit.  Going back to a regular screen for the edits.
						echo '<tr class="'.$rowspec.'">
								<td><div id="contact-form"><a href="rep_edit_contact.php?s='.$start.'&cid='.$row['contact_id'].'" class="bluelink" " >Edit</a></div>
									&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Dump</a></div></td>
								<td>'.$row['lastname'].'</td>';
								
						echo '<td>'.$row['firstname'].'</td>
								<td>'.$row['email'].'</td>';
						
						if ($row['phone'] != ''){
							//echo '<td>'.$row['phone'].'<a href="'.$row['contact_id'].'" class="phonelist_modal" >
							//			<img src="./images/smallicons/cellphone1.png" class="phonelink"/></a></td>';
							
							// REMOTE DIALER AJAX CALL
							echo '<td>'.$row['phone'].'<a onclick="callAjax( \''.$row['phone'].'\');" class="phonelist_modal" href="#">
										<img src="./images/smallicons/remotedial.png" class="phonelink"/></a></td>';
						} else {
							echo '<td>'.$row['phone'].'</td>';
						}
						
						/*echo '<td>'.$formatted_entrydt.'</td>
								<td>'.substr($row['tier_status'],0,1).'</td>
								<td>'.$row['status_desc'].'</td>
								</tr>';
						*/
						echo '<td>'.substr($row['tier_status'],0,1).'</td>
								<td>'.$row['status_desc'].'</td>
								<td>';
						
						// Build the Roles cell
						if ($row['rep_id'] == $repsid){
							echo 'Inviter<br />';
						}
						if ($row['assigned_manager'] == $repsvfgid){
							echo 'Manager<br />';
						}
						if ($row['assigned_consultant'] == $repsvfgid){
							echo 'Consultant<br />';
						}
						if ($row['additional_rep'] == $repsvfgid){
							echo 'Addl Rep';
						}
						echo '</td></tr>';
						
					}
					
					//CREATE PAGE LINKS TO OTHER RECS
					if ($pages > 1) {
						
						echo '<tr>
								<th colspan=7 align="center">
								<div class="paginator">';
						

						$current_page = ($start/$display) + 1;

						//if not first page - make previous button
						if ($current_page != 1) {
							//echo '<a href="javascript: pageResults('.($start - $display).','.$pages.');">Previous</a>&nbsp;';
							echo '<a href="rep_todays_tasks_and_events.php?s='.($start - $display).'&p='.$pages.
									'&actiondate='.$mysqldate.'">Previous</a>&nbsp;';
						}
						//make number links to pages
						for ($i = 1; $i <= $pages; $i++){
							if ($i != $current_page){
								//echo '<a href="javascript: pageResults('.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
								echo '<a href="rep_todays_tasks_and_events.php?s='.(($display * ($i - 1))).'&p='.$pages.
									'&actiondate='.$mysqldate.'">'.$i.'</a>&nbsp;';
							} else {
								//current page isn't a link
								echo $i . ' ';
							}
						} //end for loop
								
						//not last page - make a next link
						if ($current_page != $pages){
							//echo '&nbsp;<a href="javascript: pageResults('.($start + $display).','.$pages.');">Next</a>';
							echo '&nbsp;<a href="rep_todays_tasks_and_events.php?s='.($start + $display).'&p='.$pages.
									'&actiondate='.$mysqldate.'">Next</a>';
						}

						//close p
						//echo '</p>';

						//Close Paginator Div and Table
						echo '</div></th></tr></table>';
							
					} else {
						//no nav needed
						echo '</table>';
					}//end if ($pages > 1)

					//refresh button
					echo '<p align="center">
						<form action="rep_todays_tasks_and_events.php" method="GET" >
							<input type="hidden" name="s" value="'.$start.'" />
							<input type="hidden" name="actiondate" value="'.$mysqldate.'" />
							<input type="hidden" name="nextactiondate" value="'.$displaydate.'" />
							<input type="submit" name="submit" class="generalbutton" value="Refresh List" />
						</form>
						</p>';

					mysqli_free_result ($r); // Free up the resources.	

				} else { // If it did not run OK.

					// Public message:
					echo '<p>Your contacts could not be retrieved. We will fix the problem shortly.</p>';
					//echo '</div>';
					
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
				} // End of if ($r)

				//unset($_GET);
				mysqli_close($dbc); // Close the database connection.
			?>
		</div> <!-- close ajax_viewcontacts -->
		<div class="cleardiv"></div>
		
		
	</div> <!-- close main content -->

</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>