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
<title>VFG Contact - Manage Team Reps</title>
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
<script type='text/javascript' src='./js/rep_modal_handlers.js'></script>
<script>
//function pageResults - pagination for the links
// ajax pagination links
function pageResults(repvfgid, repfirst, replast, start, page) {
	
	$.ajax({
			url:"ajax_teamreps_pagination.php",
			type: "GET",
			data: {repvfgid: repvfgid,
				   repfirst: repfirst,
				   replast: replast,
				   s: start, 
				   p: page},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewcontacts").html(result);			
			}	//end success:function
	}); //end $.ajax

}
function nextRep(repid){
	//alert(repid);
	$.ajax({
		url:"ajax_nextrep_downline.php",
		type: "GET",
		data: {repid: repid},
		dataType: "html",		
		success:function(result){
			$("#ajax_viewcontacts").html(result);			
		}	//end success:function
	}); //end $.ajax
	return false;
}

function repInfoModal(repid){

	$.get("ajax_getrep_info_modal.php", { currentrepid: repid }, function(data){
		// create a modal dialog with the data
		$(data).modal({
			closeHTML: "<a href='#' title='Close' class='modal-close'>close X</a>",
			position: ["5%",], //top position
			opacity: 70,
			overlayId: 'contact-overlay',
			containerId: 'contact-container',
			minHeight: 700,
			minWidth: 950,
			maxHeight: 700,
			maxWidth: 950,
			zIndex: 3000,
			onClose: close
		});
	});
	return false;
	
}

function repEmailer(repemail){
	alert('Email to '+repemail);
	return false;
}
function repTexter(repphone){
	alert('Texting to '+repphone);
	return false;
}
function repDialer(repphone){
	alert('Phone call to '+repphone);
	return false;
}
</script>
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
			<!-- Refresh Rep's Direct Downline -->
			<p align="center">
				<form action="rep_manage_team_reps.php" method="GET" >
				<input type="submit" id="refresh_listing" class="generalbutton" value="My Downline" /></form>
			</p>
			<?php
				$repsid = $_SESSION['rep_id'];
				$reps_vfgid = $_SESSION['vfgrep_id'];
				$rep_firstname = $_SESSION['rep_firstname'];
				$rep_lastname = $_SESSION['rep_lastname'];
				
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
					//need to determine direct recruits count count in db
					//
					 $q = "SELECT count(rep_id) FROM reps
						   WHERE recruiter_vfgid='$reps_vfgid'";
					 $r = @mysqli_query($dbc, $q);

					 $row = @mysqli_fetch_array($r, MYSQLI_NUM);
					 $records = $row[0];
					 $_SESSION['team_total_reps'] = $records;
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
						$order_by = 'lastname '.$sort_order;
						break;
					case 'entrydt':
						$order_by = 'signup_date '.$sort_order;
						break;
					default:
						$order_by = 'signup_date ASC';
						break;
				}
				//Get total recs for refresh
				$q = "SELECT count(rep_id) FROM reps
					  WHERE recruiter_vfgid='$reps_vfgid'";
				$r = @mysqli_query($dbc, $q);
				$row = @mysqli_fetch_array($r, MYSQLI_NUM);
				$_SESSION['team_total_reps'] = $row[0];
				mysqli_free_result($r);
				
				
				// Make the query:
				$q = "SELECT rep_id, firstname, lastname, email, phone, rep_licensed, purchased_policy
					  FROM reps
					  WHERE recruiter_vfgid = '$reps_vfgid' 
					  ORDER BY $order_by 
					  LIMIT $start, $display";	
					
				$r = @mysqli_query ($dbc, $q); // Run the query.

				if ($r) { // If it ran OK, display the records.

					echo '<table id="csstable">
						<tr>
							<th colspan=8>'.$rep_firstname.' '.$rep_lastname.'\'s REP TEAM ('.$_SESSION['team_total_reps'].' total)</th>
						</tr>
						<tr>
							<th scope="col">Actions</th>
							<th scope="col">Last</th>
							<th scope="col">First</th>
							<th scope="col">Email</th>
							<th scope="col">Phone</th>
							<th scope="col">Licensed</th>
							<th scope="col">Has Policy</th>
							<th scope="col">Downline</th>
						</tr>';
				
					//alternate row class css spec: spec, specalt
					$rowspec = 'specalt';
					$results_repfirst = $results_replast = ''; 
					// Fetch and print all the records:
					while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
						
						$results_repfirst = $row['firstname'];
						$results_replast = $row['lastname'];
						
						//switch the rowspec value
						//$rowspec = ($rowspec=='#ffffff' ? '#f5fafa' : '#ffffff');
						$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
											
						//create each row
						echo '<tr class="'.$rowspec.'">
								<td><div id="contact-form"><a href="#" class="bluelink" onClick="javascript: return repInfoModal(\''.$row['rep_id'].'\');">View</a></div></td>
								<td>'.$row['lastname'].'</td>
								<td>'.$row['firstname'].'</td>	
								<td>'.$row['email'].'</td>
								<td>'.$row['phone'].'</td>
								<td>'.$row['rep_licensed'].'</td>
								<td>'.$row['purchased_policy'].'</td>
								<td><a href="#" onClick="javascript: return nextRep(\''.$row['rep_id'].'\');"><img src="./images/smallicons/heirarchy.png" /></a></td>
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
							echo '<a href="javascript: pageResults(\''.$reps_vfgid.'\',\''.$rep_firstname.'\',\''.$rep_lastname.'\','.($start - $display).','.$pages.');">Previous</a>&nbsp;';
							//echo '<a href="rep_manage_team_reps.php?s='.($start - $display).'&p='.$pages.'">Previous</a>&nbsp;';
						}
						//make number links to pages
						for ($i = 1; $i <= $pages; $i++){
							if ($i != $current_page){
								echo '<a href="javascript: pageResults(\''.$reps_vfgid.'\',\''.$rep_firstname.'\',\''.$rep_lastname.'\','.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
								//echo '<a href="rep_manage_team_reps.php?s='.(($display * ($i - 1))).'&p='.$pages.'">'.$i.'</a>&nbsp;';
							} else {
								//current page isn't a link
								echo $i . ' ';
							}
						} //end for loop
								
						//not last page - make a next link
						if ($current_page != $pages){
							echo '&nbsp;<a href="javascript: pageResults(\''.$reps_vfgid.'\',\''.$rep_firstname.'\',\''.$rep_lastname.'\','.($start + $display).','.$pages.');">Next</a>';
							//echo '&nbsp;<a href="rep_manage_team_reps.php?s='.($start + $display).'&p='.$pages.'">Next</a>';
						}

						//close p
						//echo '</p>';

						//Close Paginator Div and Table
						echo '</div></th></tr></table>';
							
					} else {
						//no nav needed
						echo '</table>';
					}//end if ($pages > 1)

					mysqli_free_result ($r); // Free up the resources.	

				} else { // If it did not run OK.

					// Public message:
					echo '<p>Your team could not be retrieved. We will fix the problem shortly.</p>';
					
					// Debugging message:
					//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
					
				} // End of if ($r)

				mysqli_close($dbc); // Close the database connection.

			?>
		</div> <!-- close ajax_viewcontacts -->
		
	</div> <!-- close main content -->

</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>