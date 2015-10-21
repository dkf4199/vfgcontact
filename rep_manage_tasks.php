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
$_SESSION['from_page'] = 'rep_manage_tasks.php';
$_SESSION['back_btn_text'] = 'Back To Manage Tasks';
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
<title>VFG Contact - Manage Tasks</title>
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
<script src="./js/dynamic_toptier_list.js"></script>
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
			<form action="rep_manage_tasks.php" method="GET">
				<label>Tier:</label>
				<input type="radio" name="toptier" id="toptier1" value="1" onClick="javascript: setTopList(this.value);" checked /><label for="toptier1">1</label>
				<input type="radio" name="toptier" id="toptier2" value="2" onClick="javascript: setTopList(this.value);" /><label for="toptier2">2</label>
				<input type="radio" name="toptier" id="toptier3" value="3" onClick="javascript: setTopList(this.value);" /><label for="toptier3">3</label>
				<input type="radio" name="toptier" id="toptier4" value="4" onClick="javascript: setTopList(this.value);" /><label for="toptier4">4</label>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<label>Status:</label>
				<select name="topstep" id="topstep">
					<option value="A">No Initial Contact</option>
					<option value="B">3x3 First Call Made</option>
					<option value="C">3x3 Second Call Made</option>
					<option value="D">3x3 Third Call Made</option>
					<option value="H">Contact Made</option>
					<option value="E">Contacted 25 Minute Sent</option>
					<option value="F">Follow-up After Video</option>
					<option value="G">Schedule Tier 2 Call</option>
				</select>
				<input type="submit" name="submit" value="Find Contacts">
			</form><br /><br />
			<?php
				$repsid = $_SESSION['rep_id'];
				if (isset($_GET['toptier'])){
					$tier = $_GET['toptier'];
				} else {
					$tier = '1';
				}
				if (isset($_GET['topstep']) && $_GET['topstep'] != ''){
					$tierstep = $_GET['topstep'];
				} else {
					$tierstep = 'A';
				}
				$tierstatus = $tier.$tierstep;
				
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
						   WHERE rep_id='$repsid' AND tier_status='$tierstatus'";
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
				$q = "SELECT count(rep_id) 
					  FROM contacts
					  WHERE rep_id='$repsid' 
					  AND tier_status='$tierstatus'";
				$r = @mysqli_query($dbc, $q);
				$row = @mysqli_fetch_array($r, MYSQLI_NUM);
				$_SESSION['total_tier_recs'] = $row[0];
				mysqli_free_result($r);
				
					
				//ASC and DESC - TOGGLE ON ENTRY DATE HEADER CLICK
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
					  FROM contacts a
					  INNER JOIN tierstatus_lookup b ON a.tier_status = b.tier_status
					  WHERE a.rep_id = '$repsid' 
					  AND a.tier_status = '$tierstatus' 
					  ORDER BY $order_by 
					  LIMIT $start, $display";	
					
				$r = @mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the records.

					echo '<table id="csstable">
						<tr>
							<th colspan=8>CONTACTS BY TASK ('.$_SESSION['total_tier_recs'].' total)</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Lastname</th>
							<th scope="col">Firstname</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col"><a href="rep_manage_tasks.php?sort=entrydt&sortorder='.$sort_order.'&toptier='.$tier.'&topstep='.$tierstep.'">Entry Date</a></th>
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
						
						# 09/24/2013 Take the modal link off the edit.  Going back to a regular screen for the edits.
						echo '<tr class="'.$rowspec.'">
								<td><div id="contact-form"><a href="rep_edit_contact.php?s='.$start.'&cid='.$row['contact_id'].'" class="bluelink" >Edit</a></div>
									&nbsp;<div id="dumpcontact-form"><a href="'.$row['contact_id'].'" class="bluelink">Dump</a></div></td>
								<td>'.$row['lastname'].'</td>';
						
						echo '<td>'.$row['firstname'].'</td>
							<td>'.$row['email'].'</td>';
						
						if ($row['phone'] != ''){
							//echo '<td>'.$row['phone'].'<a href="'.$row['contact_id'].'" class="phonelist_modal" >
							//		<img src="./images/smallicons/cellphone1.png" class="phonelink"/></a></td>';
							
							// REMOTE DIALER AJAX CALL
							echo '<td>'.$row['phone'].'<a onclick="callAjax( \''.$row['phone'].'\');" class="phonelist_modal" href="#">
										<img src="./images/smallicons/remotedial.png" class="phonelink"/></a></td>';
										
						} else {
							echo '<td>'.$row['phone'].'</td>';
						}
						
						echo '<td>'.$formatted_entrydt.'</td>
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
							echo '<a href="rep_manage_tasks.php?s='.($start - $display).'&p='.$pages.
								'&toptier='.$tier.'&topstep='.$tierstep.'">Previous</a>&nbsp;';
						}
						//make number links to pages
						for ($i = 1; $i <= $pages; $i++){
							if ($i != $current_page){
								//echo '<a href="javascript: pageResults('.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
								echo '<a href="rep_manage_tasks.php?s='.(($display * ($i - 1))).'&p='.$pages.
									'&toptier='.$tier.'&topstep='.$tierstep.'">'.$i.'</a>&nbsp;';
							} else {
								//current page isn't a link
								echo $i . ' ';
							}
						} //end for loop
								
						//not last page - make a next link
						if ($current_page != $pages){
							//echo '&nbsp;<a href="javascript: pageResults('.($start + $display).','.$pages.');">Next</a>';
							echo '&nbsp;<a href="rep_manage_tasks.php?s='.($start + $display).'&p='.$pages.
							'&toptier='.$tier.'&topstep='.$tierstep.'">Next</a>';
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
						<form action="rep_manage_tasks.php" method="GET" >
						<input type="hidden" name="s" value="'.$start.'" />
						<input type="hidden" name="toptier" value="'.$tier.'" />
						<input type="hidden" name="topstep" value="'.$tierstep.'" />
						<input type="submit" name="submit" class="generalbutton" value="Refresh List" /></form>
						</p>';

					mysqli_free_result ($r); // Free up the resources.	

				} else { // If it did not run OK.

					// Public message:
					echo '<p>No contacts for this Tier and Status.</p>';
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