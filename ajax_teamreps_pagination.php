<?php
session_start();

	$reps_vfgid = $_GET['repvfgid'];
	$rep_firstname = $_GET['repfirst'];
	$rep_lastname = $_GET['replast'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	//DB Connection
	require_once (MYSQL);
	//*********************************************************
	//Pagination
	//*********************************************************

	//Max Display
	$display = 2;

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
	$q = "SELECT rep_id, firstname, lastname, email, phone
		  FROM reps
		  WHERE recruiter_vfgid = '$reps_vfgid' 
		  ORDER BY $order_by 
		  LIMIT $start, $display";	
		
	$r = @mysqli_query ($dbc, $q); // Run the query.

	if ($r) { // If it ran OK, display the records.

		echo '<table id="csstable">
			<tr>
				<th colspan=6>'.$rep_firstname.' '.$rep_lastname.'\'s REP TEAM ('.$_SESSION['team_total_reps'].' total)</th>
			</tr>
			<tr>
				<th scope="col">Actions</th>
				<th scope="col">Lastname</th>
				<th scope="col">Firstname</th>
				<th scope="col">Email</th>
				<th scope="col">Phone</th>
				<th scope="col">Downline</th>
			</tr>';

		//alternate row class css spec: spec, specalt
		$rowspec = 'specalt';	
		// Fetch and print all the records:
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			
			$rowspec = ($rowspec=='spec' ? 'specalt' : 'spec');
								
			//create each row
			echo '<tr class="'.$rowspec.'">
					<td><div id="contact-form"><a href="#" class="reps" onClick="javascript: return repInfoModal(\''.$row['rep_id'].'\');">View</a></div></td>
					<td>'.$row['lastname'].'</td>
					<td>'.$row['firstname'].'</td>	
					<td>'.$row['email'].'</td>
					<td>'.$row['phone'].'</td>
					<td><a href="#" onClick="javascript: return nextRep(\''.$row['rep_id'].'\');"><img src="./images/downline.jpg" /></a></td>
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
		echo '<p>Your contacts could not be retrieved. We will fix the problem shortly.</p>';
		echo '</div>';
		
		// Debugging message:
		//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
		
	} // End of if ($r)

	unset($_GET);
	mysqli_close($dbc); // Close the database connection.

?>