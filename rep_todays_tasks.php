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
<title>VFG Contact - Today's Tasks</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<link href="./css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet" />
<!-- Load JavaScript files -->
<script type='text/javascript' src="./js/jquery.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- eapcrm_modal_handlers.js contains the jquery that runs the "edit" "dump" "restore" "flush" modal divs -->
<script type='text/javascript' src='./js/eapcrm_modal_handlers.js'></script>

</head>

<body>
<div class="wrapper showgoldborder">
	<!-- Header -->
	<?php include('includes/html/newheader_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<form action="rep_todays_tasks.php" method="GET">
			<label>Select Tasks for:</label>
			<input type="text" name="nextactiondate" id="datepickerstatic" 
					value="<?php if (isset($_GET['nextactiondate'])) echo $_GET['nextactiondate']; ?>" />
			<input type="submit" id="taskbutton" value="Get Tasks" />
		</form>
		
		<div id="ajax_viewcontacts">
			<?php
				$repsid = $_SESSION['rep_id'];
				
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
				
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				//DB Connection
				require_once (MYSQL);
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
					 $q = "SELECT count(rep_id) FROM contacts
						   WHERE rep_id='$repsid'
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
				$q = "SELECT count(rep_id) FROM contacts
					  WHERE rep_id='$repsid'
					  AND next_action_date='$mysqldate'";
				$r = @mysqli_query($dbc, $q);
				$row = @mysqli_fetch_array($r, MYSQLI_NUM);
				$_SESSION['todays_total_recs'] = $row[0];
				mysqli_free_result($r);
				
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
							$order_by = 'lastname '.$sort_order;
							break;
						case 'entrydt':
							$order_by = 'entry_date '.$sort_order;
							break;
					default:
						$order_by = 'entry_date ASC';
						break;
				}

				// Make the query:
				$q = "SELECT contact_id, firstname, lastname, email, phone, tier_status, entry_date 
					  FROM contacts
					  WHERE rep_id = '$repsid'
					  AND next_action_date = '$mysqldate'
					  ORDER BY $order_by 
					  LIMIT $start, $display";
				
				$r = @mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the records.

					echo '<table id="csstable">
						<tr>
							<th colspan=7>CONTACTS WITH TASKS ('.$_SESSION['todays_total_recs'].' total) for '.$displaydate.'</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col"><a href="rep_todays_tasks.php?sort=entrydt&sortorder='.$sort_order.'&actiondate='.$mysqldate.
												'&nextactiondate='.$displaydate.'">Entry Date</a></th>
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
						//echo '<tr class="'.$rowspec.'">
						//		<td><a href="javascript: contactEditInfo(\''.$row['contact_id'].'\');">Edit</a>
						//					&nbsp;<a href="javascript: contactDumpInfo(\''.$row['contact_id'].'\');">Dump</a></td>
						#SIMPLEMODAL mod 08/14/2013 dkf
						# href attribute holds the contacts id.  it is sliced off and used in the
						# $.get() ajax call in eapcrm_contacts.js
						echo '<tr class="'.$rowspec.'">
							<td><div id="contact-form"><a href="'.$row['contact_id'].'" class="contact">Edit</a></div>
								&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="dumpcontact">Dump</a></div></td>
								<td>'.$row['lastname'].'</td>
								<td>'.$row['firstname'].'</td>
								<td>'.$row['email'].'</td>
								<td>'.$row['phone'].'</td>
								<td>'.$formatted_entrydt.'</td>
								<td>'.substr($row['tier_status'],0,1).'</td>
								</tr>';
						
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
							echo '<a href="rep_todays_tasks.php?s='.($start - $display).'&p='.$pages.
									'&actiondate='.$mysqldate.'">Previous</a>&nbsp;';
						}
						//make number links to pages
						for ($i = 1; $i <= $pages; $i++){
							if ($i != $current_page){
								//echo '<a href="javascript: pageResults('.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
								echo '<a href="rep_todays_tasks.php?s='.(($display * ($i - 1))).'&p='.$pages.
									'&actiondate='.$mysqldate.'">'.$i.'</a>&nbsp;';
							} else {
								//current page isn't a link
								echo $i . ' ';
							}
						} //end for loop
								
						//not last page - make a next link
						if ($current_page != $pages){
							//echo '&nbsp;<a href="javascript: pageResults('.($start + $display).','.$pages.');">Next</a>';
							echo '&nbsp;<a href="rep_todays_tasks.php?s='.($start + $display).'&p='.$pages.
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
						<form action="rep_todays_tasks.php" method="GET" >
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
					echo '</div>';
					
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
				} // End of if ($r)

				//unset($_GET);
				mysqli_close($dbc); // Close the database connection.
			?>
		
		</div> <!-- close ajax_viewcontacts -->
		
		<?php include('includes/html/footer.html'); ?>
		
	</div> <!-- close main content -->
	
	

</div>	<!-- close wrapper -->
</body>
</html>