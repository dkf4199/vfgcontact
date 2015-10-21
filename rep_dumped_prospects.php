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
<title>VFG Contact - Dumped Prospects</title>
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
			$repsid = $_SESSION['rep_id'];
			
			include ('includes/config.inc.php');
			include ('includes/phpfunctions.php');
			//DB Connection
			require_once (MYSQL);
			
			//Total Dumped Recs
			$tr = "SELECT count(rep_id) FROM dumped_contacts
				      WHERE rep_id='$repsid'";
			$rs = @mysqli_query($dbc, $tr);

			$row = @mysqli_fetch_array($rs, MYSQLI_NUM);
			$_SESSION['dump_total_recs'] = $row[0];
			mysqli_free_result($rs);
			
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
				$q = "SELECT count(rep_id) FROM dumped_contacts
				      WHERE rep_id='$repsid'";
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
					$order_by = 'a.lastname '.$sort_order;
					break;
				case 'entrydt':
					$order_by = 'a.entry_date '.$sort_order;
					break;
				default:
					$order_by = 'a.entry_date ASC';
					break;
			}

			// Make the query:
			$q = "SELECT a.contact_id, a.firstname, a.lastname, a.email, 
							a.phone, a.tier_status, a.entry_date, a.contact_type, b.status_desc 
				  FROM dumped_contacts a
				  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
				  WHERE a.rep_id = '$repsid' 
				  ORDER BY $order_by 
				  LIMIT $start, $display";	
				
			$r = @mysqli_query ($dbc, $q); // Run the query.

			if ($r) { // If it ran OK, display the records.

				echo '<table id="csstable">
					<tr>
						<th colspan=8>YOUR DUMPED PROSPECTS ('.$_SESSION['dump_total_recs'].' total)</th>
					</tr>
					<tr>
						<th scope="col">Actions</th>
						<th scope="col">Lastname</th>
						<th scope="col">Firstname</th>
						<th scope="col">Email</th>
						<th scope="col">Phone</th>
						<th scope="col"><a href="rep_dumped_prospects.php?sort=entrydt&sortorder='.$sort_order.'">Entry Date</a></th>
						<th scope="col">Tier</th>
						<th scope="col">Status</th>
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
					//		<td><a href="javascript: viewDumpContact(\''.$row['contact_id'].'\');">Restore</a>&nbsp;
					//			<a href="javascript: viewFlushContact(\''.$row['contact_id'].'\');">Flush</a></td>
					#SIMPLEMODAL mod 08/14/2013 dkf
					# href attribute holds the contacts id.  it is sliced off and used in the
					# $.get() ajax call in eapcrm_contacts.js
					echo '<tr class="'.$rowspec.'">
							<td><div id="restorecontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Restore</a></div>
								&nbsp;<div id="flushcontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Flush</a></div></td>
							<td>'.$row['lastname'].'</td>
							<td>'.$row['firstname'].'</td>
							<td>'.$row['email'].'</td>
							<td>'.$row['phone'].'</td>
							<td>'.$formatted_entrydt.'</td>
							<td>'.substr($row['tier_status'],0,1).'</td>
							<td>'.$row['status_desc'].'</td>
							
						  </tr>';
					
				}
				
				//CREATE PAGE LINKS TO OTHER RECS
				if ($pages > 1) {
					
					echo '<tr>
							<th colspan=8 align="center">
							<div class="paginator">';
					

					$current_page = ($start/$display) + 1;

					//if not first page - make previous button
					if ($current_page != 1) {
						//echo '<a href="javascript: pageResults('.($start - $display).','.$pages.');">Previous</a>&nbsp;';
						echo '<a href="rep_dumped_prospects.php?s='.($start - $display).'&p='.$pages.'">Previous</a>&nbsp;';
					}
					//make number links to pages
					for ($i = 1; $i <= $pages; $i++){
						if ($i != $current_page){
							//echo '<a href="javascript: pageResults('.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
							echo '<a href="rep_dumped_prospects.php?s='.(($display * ($i - 1))).'&p='.$pages.'">'.$i.'</a>&nbsp;';
						} else {
							//current page isn't a link
							echo $i . ' ';
						}
					} //end for loop
							
					//not last page - make a next link
					if ($current_page != $pages){
						//echo '&nbsp;<a href="javascript: pageResults('.($start + $display).','.$pages.');">Next</a>';
						echo '&nbsp;<a href="rep_dumped_prospects.php?s='.($start + $display).'&p='.$pages.'">Next</a>';
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
					<form action="rep_dumped_prospects.php" method="GET" >
					<input type="hidden" name="s" value="'.$start.'" />
					<input type="submit" id="refresh_listing" class="generalbutton" value="Refresh List" /></form>
					</p>';

				mysqli_free_result ($r); // Free up the resources.	

			} else { // If it did not run OK.

				// Public message:
				echo '<p>Your dumped contacts could not be retrieved. We will fix the problem shortly.</p>';
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