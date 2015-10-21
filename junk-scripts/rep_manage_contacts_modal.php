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
<title>EAPCRM Leads Management - It's Easy As Pie</title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/eapcrm.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_navmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/eap_modal.css" />
<script type="text/javascript" src="./js/autotab.js"></script>
<script src="./js/jquery1-9-1.js"></script>
<script src="./js/jquery_migrate1-1-0.js"></script>
<script src="./js/tier_list.js"></script>
<script src="./js/dynamic_tier_list.js"></script>
<script>
//function pageResults - pagination for the links
// ajax pagination links
function pageResults(start, page) {
	
	$.ajax({
			url:"ajaxgetcontacts_pagination.php",
			type: "GET",
			data: {s: start, 
				   p: page},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewcontacts").html(result);
				//clear out the editable div
				$("#ajax_updatecontact").html("");
			}	//end success:function
	}); //end $.ajax

}
//function refreshList
function refreshList(start){
	//alert("Refresh button clicked: start value is "+start);
	
	$.ajax({
			url:"ajaxgetcontacts_pagination.php",
			type: "GET",
			data: {s: start},
			dataType: "html",		
			success:function(result){
				$("#ajax_viewcontacts").html(result);
				//clear out the editable div
				$("#ajax_updatecontact").html("");
			}	//end success:function
	}); //end $.ajax
	
	return false;
}
//function contactEditInfo
function contactEditInfo(contactid) {
	//alert("Edit link clicked: value is "+contactid);
	//pull the info for the lead
	//display in form to update
	$.ajax({
			url:"ajaxgetcontact_info.php",
			type: "GET",
			data: {currentcontactid: contactid},
			dataType: "html",		
			success:function(result){
				$("#ajax_updatecontact").html(result);
			}	//end success:function
	}); //end $.ajax
	$("#openModal").show();
	return false;
}
function updateContact(){

	var dataChanged = false;
		
	var firstName = $("#first_name").val();
	var ogFirstName = $("#original_firstname").val();
	var lastName = $("#last_name").val();
	var ogLastName = $("#original_lastname").val();
	var email = $("#email").val();
	var ogEmail = $("#original_email").val();
	var phone = $("#phone").val();
	var ogPhone = $("#original_phone").val();
	var city = $("#city").val();
	var ogCity = $("#original_city").val();
	var state = $("#state :selected").val();
	var ogState = $("#original_state").val();
	var timeZone = $("#timezone :selected").val();
	var ogTimeZone = $("#original_timezone").val();
	var tier = $("input:radio[name=tier]:checked").val();
	var ogTier = $("#original_tier").val();
	var tierStep = $("#tierstep :selected").val();
	var ogTierStep = $("#original_tierstep").val();
	var teamMember = $("#team_member :selected").val();
	var ogTeamMember = $("#original_teammember").val();
	var notes = $("#notes").val();
	var ogNotes = $("#original_notes").val();
	//Serialize the form
	//compare original vals from php program 
	//to form vars on update click
	//NOTE: CONVERT to lowercase - JS is case sensitive!
	if (firstName.toLowerCase() != ogFirstName.toLowerCase()) {dataChanged = true;}
	if (lastName.toLowerCase() != ogLastName.toLowerCase()) {dataChanged = true;}
	if (email != ogEmail) {dataChanged = true;}
	if (phone != ogPhone) {dataChanged = true;}
	if (city != ogCity) {dataChanged = true;}
	if (state != ogState) {dataChanged = true;}
	if (timeZone != ogTimeZone) {dataChanged = true;}
	if (tier != ogTier) {dataChanged = true;}
	if (tierStep != ogTierStep) {dataChanged = true;}
	if (teamMember != ogTeamMember) {dataChanged = true;}
	if (notes != ogNotes) {dataChanged = true;}
	
	if (dataChanged) {
		//set hidden form var data_changed to true
		$("#data_changed").val('true');
	}
		
	var formdata = $('#editcontact_form').serialize();
	
	//alert(tier+' '+tierStep);
	//call ajax_updatelead.php
	//the ajax_verify_update div is contained in the output
	//from the call to ajaxgetlead_info.php
	//
	$.ajax({
		url:"ajax_updatecontact.php",
		type: "POST",
		data: formdata,
		dataType: "html",		
		success:function(result){
			$("#ajax_verify_update").html(result);
		}	//end success:function
	}); //end $.ajax
	
	//Have to reset the original values equal to what
	//was just submitted to start fresh for any
	//subsequent update!
	//
	//SET og hiddens to the extracted js variables from form fields
	//after ajax runs
	$("#original_firstname").val(firstName);
	$("#original_lastname").val(lastName);
	$("#original_email").val(email);
	$("#original_phone").val(phone);
	$("#original_city").val(city);
	$("#original_state").val(state);
	$("#original_timezone").val(timeZone);
	$("#original_tier").val(tier);
	$("#original_tierstep").val(tierStep);
	$("#original_teammember").val(teamMember);
	$("#original_notes").val(notes);
	
	//reset the data_changed hidden flag
	$("#data_changed").val('false');
	
	
	
	//alert("Update Form Button clicked.\n"+"First Name: "+firstName+"\n"+"Last Name: "+lastName+"\n");
	//alert("Update Form Button clicked.\n"+"Data Changed Flag: "+dataChanged);
	return false;
}
//function contactDumpInfo
function contactDumpInfo(contactid) {
	//alert("Edit link clicked: value is "+contactid);
	//pull the info for the contact
	//to display for the dump
	$.ajax({
			url:"ajaxgetcontact_dumpinfo.php",
			type: "GET",
			data: {currentcontactid: contactid},
			dataType: "html",		
			success:function(result){
				$("#ajax_updatecontact").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
//function contactDumpInfo
function dumpContact() {

	var formdata = $('#dumpcontact_form').serialize();
	//move the contact to the dump table
	//and out of the contacts table
	$.ajax({
			url:"ajax_dumpcontact.php",
			type: "POST",
			data: formdata,
			dataType: "html",		
			success:function(result){
				$("#ajax_verify_update").html(result);
			}	//end success:function
	}); //end $.ajax
	return false;
}
</script>
</head>

<body>
<div class="wrapper showgoldborder">
	<div class="header">
		<span>EAPCRM.com.  Leads Management - "It's easy as pie..."</span>
	</div>
	<div class="topnav">
		<ul id="menu_top">
			<li><a href="rep_maindash.php">Home</a></li>
			<li><a href="rep_add_contact.php">Add Contact</a></li>
			<li><a href="#">Active Contacts</a>
				<ul id="static_rpt_submenu">
					<li><a href="rep_manage_contacts.php">Manage Contacts</a></li>
					<li><a href="rep_manage_tasks.php">Manage Tasks</a></li>
					<li><a href="rep_manage_team.php">Manage Team</a></li>
				</ul>
			</li>
			<li><a href="rep_dumped_contacts.php">Dumped Contacts</a></li>
			<li><a href="rep_logout.php">Logout</a></li>
		</ul>
	</div>
	
	<div class="maincontent">
		<p class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></p>
		
		<div id="ajax_viewcontacts">
		<?php
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
				 $q = "SELECT count(rep_id) FROM contacts
					   WHERE rep_id='$repsid'";
				 $r = @mysqli_query($dbc, $q);

				 $row = @mysqli_fetch_array($r, MYSQLI_NUM);
				 $records = $row[0];
				 $_SESSION['total_recs'] = $records;
				 
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


			// Make the query:
			$q = "SELECT contact_id, firstname, lastname, email, phone, tier_status, entry_date FROM contacts
				  WHERE rep_id = '$repsid' 
				  ORDER BY entry_date ";	
				
			//LIMIT CLAUSE FOR PAGINATION
			$pageclause = "LIMIT $start, $display";
					
			//concatenate to the $q string
			$q .= $pageclause;

			$r = @mysqli_query ($dbc, $q); // Run the query.

			if ($r) { // If it ran OK, display the records.

				echo '<table id="csstable">
					<tr>
						<th colspan=7>YOUR CONTACTS ('.$_SESSION['total_recs'].' total)</th>
					</tr>
					<tr>
						<th scope="col">Actions</th>
						<th scope="col">Lastname</th>
						<th scope="col">Firstname</th>
						<th scope="col">Email</th>
						<th scope="col">Phone</th>
						<th scope="col">Entry Date</th>
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
							<td><a href="javascript: contactEditInfo(\''.$row['contact_id'].'\');">Edit</a>
								&nbsp;<a href="javascript: contactDumpInfo(\''.$row['contact_id'].'\');">Dump</a></td>
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
				echo '<p>Your contacts could not be retrieved. We will fix the problem shortly.</p>';
				echo '</div>';
				
				// Debugging message:
				//echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
				
			} // End of if ($r)

			mysqli_close($dbc); // Close the database connection.

		?>
		</div> <!-- close ajax_viewcontacts -->
		
		
		<div id="openModal" class="modalDialog">
			<div>
				<a href="#close" title="Close" class="close">X</a>
				<!-- div for update form -->
				<div id="ajax_updatecontact"></div>
			</div>
		</div> <!-- close openModal -->
		
	</div> <!-- close main content -->
	
	<?php include('includes/html/footer.html'); ?>

</div>	<!-- close wrapper -->
</body>
</html>