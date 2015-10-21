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
$_SESSION['from_page'] = 'admin_reps.php';
$_SESSION['back_btn_text'] = 'Back To Reps';
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
<title>VFG Contact - Manage Recruits</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfg_jquery_ui_menu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/modaldivs.css" />
<!-- JS -->
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript' src="./js/jquery.simplemodal.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<!-- Handlers for the image clicks to actions and modals -->
<script type='text/javascript' src='./js/tcs_admin_modal_handlers.js'></script>
<!-- REMOTE DIALER library-->
<script type='text/javascript' src='./js/gettemp_contact.js'></script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php include('includes/html/admin_header_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['admin_firstname'].' '.$_SESSION['admin_lastname'].'.'; ?></div>
				
		<div id="ajax_viewcontacts" class="opacity80">
			<?php
								
				include ('includes/config.inc.php');
				include ('includes/phpfunctions.php');
				//DB Connection
				require_once (MYSQL);
				//*********************************************************
				//Pagination
				//*********************************************************

				//Max Display
				$display = 20;

				if (isset($_GET['p']) && is_numeric($_GET['p'])) {
					//already determined
					$pages = $_GET['p'];

				} else {
					//need to determine based on count in db
					//
					 $q = "SELECT count(rep_id) FROM reps";
					 $r = mysqli_query($dbc, $q);

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
				$q = "SELECT count(rep_id) FROM reps";
				$r = mysqli_query($dbc, $q);
				$row = mysqli_fetch_array($r, MYSQLI_NUM);
				$_SESSION['total_reps'] = $row[0];
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
				$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'signup_date';
				switch ($sort){
					case 'ln':
						$order_by = 'lastname '.$sort_order;
						break;
					case 'signup_date':
						$order_by = 'signup_date '.$sort_order;
						break;
					default:
						$order_by = 'signup_date ASC';
						break;
				}

				// Make the query:
				$q = "SELECT rep_id, firstname, lastname, gmail_acct, phone, signup_date 
					  FROM reps
					  ORDER BY $order_by 
					  LIMIT $start, $display";	
					
				$r = mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the records.

					echo '<table id="csstable">
						<tr>
							<th colspan=7>AGENTS ('.$_SESSION['total_reps'].' total)</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col"><a href="admin_reps.php?sort=ln&sortorder='.$sort_order.'">Lastname</a></th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col"><a href="admin_reps.php?sort=signup_date&sortorder='.$sort_order.'">Signup Date</a></th>
							
						</tr>';
				
					//alternate row class css spec: spec, specalt
					$rowspec = 'specalt';	
					// Fetch and print all the records:
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						
						$rawdate = strtotime( $row['signup_date'] );
						$formatted_signupdt = date( 'm-d-Y h:i a', $rawdate );
						
						//switch the rowspec value
						//$rowspec = ($rowspec=='#ffffff' ? '#f5fafa' : '#ffffff');
						$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
											
						# 09/24/2013 Take the modal link off the edit.  Going back to a regular screen for the edits.
						echo '<tr class="'.$rowspec.'">
								<td>
									<div id="editrep_div"><a href="admin_edit_rep.php?s='.$start.'&rid='.$row['rep_id'].'" class="bluelink" >Edit</a></div>
									<div id="dumprep_div"><a href="'.$row['rep_id'].'" class="bluelink" >Dump</a></div>
								</td>
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
						
						echo '<td>'.$formatted_signupdt.'</td>	
							 </tr>';
						
					}
					
					//CREATE PAGE LINKS TO OTHER RECS
					if ($pages > 1) {
						
						echo '<tr>
								<th colspan=6 align="center">
								<div class="paginator">';
						

						$current_page = ($start/$display) + 1;

						//if not first page - make previous button
						if ($current_page != 1) {
							echo '<a href="admin_reps.php?s='.($start - $display).'&p='.$pages.'">Previous</a>&nbsp;';
							//echo '<a href="rep_view_contact.php?&s='.($start - $display).'&p='.
							//	$pages.'">Previous</a> ';
						}
						//make number links to pages
						for ($i = 1; $i <= $pages; $i++){
							if ($i != $current_page){
								echo '<a href="admin_reps.php?s='.(($display * ($i - 1))).'&p='.$pages.'">'.$i.'</a>&nbsp;';
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
							echo '&nbsp;<a href="admin_reps.php?s='.($start + $display).'&p='.$pages.'">Next</a>';
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

					//refresh button
					echo '<p align="center">
						<form action="admin_reps.php" method="GET" >
						<input type="hidden" name="s" value="'.$start.'" />
						<input type="submit" id="refresh_listing" class="generalbutton" value="Refresh List" /></form>
						</p>';
					
					//echo '</div> <!-- close viewleads -->';

					mysqli_free_result ($r); // Free up the resources.	

				} else { // If it did not run OK.

					// Public message:
					echo '<p>Reps could not be retrieved. We will fix the problem shortly.</p>';
					//echo '</div>';
					
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
				} // End of if ($r)

				mysqli_close($dbc); // Close the database connection.

			?>
		</div> <!-- close ajax_viewcontacts -->
		<div class="cleardiv"></div>
		
		
	</div> <!-- close main content -->

</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>