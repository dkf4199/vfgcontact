<?php
session_start();

	$repsid = $_SESSION['rep_id'];
			
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
	$q = "SELECT contact_id, firstname, lastname, 
				email, phone, tier_status, entry_date 
		  FROM dumped_contacts
		  WHERE rep_id = '$repsid' 
		  ORDER BY $order_by 
		  LIMIT $start, $display";

	$r = @mysqli_query ($dbc, $q); // Run the query.

	if ($r) { // If it ran OK, display the records.

		echo '<table id="csstable">
			<tr>
				<th colspan=7>YOUR DUMPED CONTACTS ('.$_SESSION['dump_total_recs'].' total)</th>
			</tr>
			<tr>
				<th scope="col">Actions</th>
				<th scope="col">Lastname</th>
				<th scope="col">Firstname</th>
				<th scope="col">Email</th>
				<th scope="col">Phone</th>
				<th scope="col"><a href="javascript: sortList('.$start.', \'entrydt\', \''.$sort_order.'\');">Entry Date</a></th>
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
			echo '<tr class="'.$rowspec.'">
					<td><a href="javascript: viewDumpContact(\''.$row['contact_id'].'\');">Restore</a>&nbsp;
								<a href="javascript: viewFlushContact(\''.$row['contact_id'].'\');">Flush</a></td>
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
				echo '<a href="javascript: pageResults('.($start - $display).','.$pages.');">Previous</a>&nbsp;';
				//echo '<a href="rep_view_contact.php?&s='.($start - $display).'&p='.
				//	$pages.'">Previous</a> ';
			}
			//make number links to pages
			for ($i = 1; $i <= $pages; $i++){
				if ($i != $current_page){
					echo '<a href="javascript: pageResults('.(($display * ($i - 1))).','.$pages.');">'.$i.'</a>&nbsp;';
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
				echo '&nbsp;<a href="javascript: pageResults('.($start + $display).','.$pages.');">Next</a>';
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
			<input type="button" id="refresh_listing" class="button" value="Refresh List" onClick="javascript: refreshList(\''.$start.'\');" />
			</p>';
		//echo '</div> <!-- close viewleads -->';

		mysqli_free_result ($r); // Free up the resources.	

	} else { // If it did not run OK.

		// Public message:
		echo '<p>Your dumped contacts could not be retrieved. We will fix the problem shortly.</p>';
		echo '</div>';
		
		// Debugging message:
		//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
		
	} // End of if ($r)

	unset($_GET);
	mysqli_close($dbc); // Close the database connection.
?>