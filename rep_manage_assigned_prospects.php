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
$_SESSION['from_page'] = 'rep_manage_assigned_prospects.php';
$_SESSION['back_btn_text'] = 'Back To Assigned Prospects';
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
<title>VFG Contact - Manage Assigned Recruits</title>
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
				
		<div id="ajax_viewcontacts" class="opacity80">
		<?php
			$repsid = $_SESSION['rep_id'];			//system assigned id
			$repsvfgid = $_SESSION['vfgrep_id'];	//vfg id
			
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
				 $q = "SELECT count(contact_id) FROM contacts
					   WHERE assigned_manager = '$repsvfgid'
					   OR assigned_consultant = '$repsvfgid' 
					   OR additional_rep = '$repsvfgid'";
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
			$q = "SELECT count(contact_id) FROM contacts
				  WHERE assigned_manager = '$repsvfgid'
			      OR assigned_consultant = '$repsvfgid'
				  OR additional_rep = '$repsvfgid'";
			$r = @mysqli_query($dbc, $q);
			$row = @mysqli_fetch_array($r, MYSQLI_NUM);
			$_SESSION['total_assigned_recs'] = $row[0];
			mysqli_free_result($r);
			
			//ASC and DESC - TOGGLE
			/*$sort_order = 'DESC'; 
			if(isset($_GET['sortorder']))	{ 
				if($_GET['sortorder'] == 'ASC') { 
					$sort_order = 'DESC'; 
				} else { 
					$sort_order = 'ASC'; 
				} 
			} 
			//SORT OPTION
			$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'lastname';
			switch ($sort){
				case 'ln':
					$order_by = 'lastname '.$sort_order;
					break;
				case 'entrydt':
					$order_by = 'entry_date '.$sort_order;
					break;
				default:
					$order_by = 'lastname ASC';
					break;
			}*/

			// Make the query - using a UNION to get manager and consultant roles:
			$q = "SELECT 1 as 'rank', a.contact_id, a.firstname, a.lastname, a.email, 
						 a.phone, 'Manager' as role, a.man_assigned_by as 'assigned_vfgid', a.manager_action_taken as 'action_taken', 
						 b.vfgrepid as 'assigner_vfgid', CONCAT(b.firstname, ' ', b.lastname) as 'fullname'
				  FROM contacts a LEFT JOIN reps b ON a.man_assigned_by = b.vfgrepid
				  WHERE a.assigned_manager = '$repsvfgid'
				  UNION
				  SELECT 2 as 'rank', a.contact_id, a.firstname, a.lastname, a.email, 
						 a.phone, 'Consultant' as role, a.con_assigned_by as 'assigned_vfgid', a.consultant_action_taken as 'action_taken',
						 b.vfgrepid as 'assigner_vfgid', CONCAT(b.firstname, ' ', b.lastname) as 'fullname'
				  FROM contacts a LEFT JOIN reps b ON a.con_assigned_by = b.vfgrepid
				  WHERE a.assigned_consultant = '$repsvfgid'
				  UNION
				  SELECT 3 as 'rank', a.contact_id, a.firstname, a.lastname, a.email, 
						 a.phone, 'Additional Rep' as role, a.addl_assigned_by as 'assigned_vfgid', a.addlrep_action_taken as 'action_taken',
						 b.vfgrepid as 'assigner_vfgid', CONCAT(b.firstname, ' ', b.lastname) as 'fullname'
				  FROM contacts a LEFT JOIN reps b ON a.addl_assigned_by = b.vfgrepid  
				  WHERE a.additional_rep = '$repsvfgid'
				  ORDER BY rank, action_taken
				  LIMIT $start, $display";	
				
			$r = @mysqli_query ($dbc, $q); // Run the query.

			if ($r) { // If it ran OK, display the records.

				echo '<table id="csstable">
					<tr>
						<th colspan=8>YOUR ASSIGNED PROSPECTS ('.$_SESSION['total_assigned_recs'].' total)</th>
					</tr>
					<tr>
						<th scope="col">Actions</th>
						<th scope="col">Lastname</th>
						<th scope="col">Firstname</th>
						<th scope="col">Email</th>
						<th scope="col">Phone</th>
						<th scope="col">Role</th>
						<th scope="col">Assigned By</th>
						<th scope="col">Action Taken</th>
					</tr>';
			
				//alternate row class css spec: spec, specalt
				$rowspec = 'specalt';	
				// Fetch and print all the records:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				
					//switch the rowspec value
					//$rowspec = ($rowspec=='#ffffff' ? '#f5fafa' : '#ffffff');
					$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
										
									
					# 09/24/2013 Take the modal link off the edit.  Going back to a regular screen for the edits.
					echo '<tr class="'.$rowspec.'">';
					
					//parse the role and make the query string for rep_edit_contact.php?....
					switch($row['role']){
						case 'Manager':
							echo '<td><div id="contact-form"><a href="rep_edit_contact.php?s='.$start.'&cid='.$row['contact_id'].'&role=manager" class="bluelink" >Edit</a></div>';
							break;
						case 'Consultant':
							echo '<td><div id="contact-form"><a href="rep_edit_contact.php?s='.$start.'&cid='.$row['contact_id'].'&role=consult" class="bluelink" >Edit</a></div>';
							break;
						case 'Additional Rep':
							echo '<td><div id="contact-form"><a href="rep_edit_contact.php?s='.$start.'&cid='.$row['contact_id'].'&role=addlrep" class="bluelink" >Edit</a></div>';
							break;
					}
					echo '&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Dump</a></div></td>
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
					echo '<td>'.$row['role'].'</td>
						  <td>'.$row['assigner_vfgid'].' '.$row['fullname'].'</td>
						  <td>'.$row['action_taken'].'</td>
						  </tr>';
					
					//echo '<td>'.$formatted_entrydt.'</td>
					//		<td>'.substr($row['tier_status'],0,1).'</td>	
					//	 </tr>';
					
				}
				
				//CREATE PAGE LINKS TO OTHER RECS
				if ($pages > 1) {
					
					echo '<tr>
							<th colspan=8 align="center">
							<div class="paginator">';
					

					$current_page = ($start/$display) + 1;

					//if not first page - make previous button
					if ($current_page != 1) {
						echo '<a href="rep_manage_assigned_prospects.php?s='.($start - $display).'&p='.$pages.'">Previous</a>&nbsp;';
						//echo '<a href="rep_view_contact.php?&s='.($start - $display).'&p='.
						//	$pages.'">Previous</a> ';
					}
					//make number links to pages
					for ($i = 1; $i <= $pages; $i++){
						if ($i != $current_page){
							echo '<a href="rep_manage_assigned_prospects.php?s='.(($display * ($i - 1))).'&p='.$pages.'">'.$i.'</a>&nbsp;';
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
						echo '&nbsp;<a href="rep_manage_assigned_prospects.php?s='.($start + $display).'&p='.$pages.'">Next</a>';
						//echo '<a href="rep_view_contact.php?&s='.($start + $display).'&p='.
						//	$pages.'">Next</a> ';
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
					<form action="rep_manage_assigned_prospects.php" method="GET" >
					<input type="hidden" name="s" value="'.$start.'" />
					<input type="submit" id="refresh_listing" class="generalbutton" value="Refresh List" /></form>
					</p>';
				
				//echo '</div> <!-- close viewleads -->';

				mysqli_free_result ($r); // Free up the resources.	

			} else { // If it did not run OK.

				// Public message:
				echo '<p>Your assigned contacts could not be retrieved. We will fix the problem shortly.</p>';
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