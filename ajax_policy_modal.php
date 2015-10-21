<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	
	$curr_cid = $_GET['currentid'];
	$fname = $_GET['currentfirstname'];
	$lname = $_GET['currentlastname'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	//Get the policy info data
	//Policy 1
	$p1_first = $p1_last = $p1_type = $p1_status = $p1_carrier = $p1_tp = '';
	$q1 = "SELECT a.firstname, a.lastname, b.policy_desc, c.household_desc, d.carrier, a.target_premium
		  FROM policy a 
		  INNER JOIN ref_policy_type b ON a.policy_type = b.policy_code
		  INNER JOIN ref_household_status c ON a.household_status = c.household_code
		  INNER JOIN ref_carriers d ON a.carrier = d.carrier_code
		  WHERE a.contact_id = '$curr_cid'
		  AND policy_num = '1' LIMIT 1";
	$r = mysqli_query ($dbc, $q1); // Run the query.
	if (mysqli_num_rows($r) == 1){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$p1_first = $row['firstname'];
			$p1_last = $row['lastname'];
			$p1_type = $row['policy_desc'];
			$p1_status = $row['household_desc'];
			$p1_carrier = $row['carrier'];
			$p1_tp = $row['target_premium'];
		}
		mysqli_free_result($r);
	}
	//Policy 2
	$p2_first = $p2_last = $p2_type = $p2_status = $p2_carrier = $p2_tp = '';
	$q2 = "SELECT a.firstname, a.lastname, b.policy_desc, c.household_desc, d.carrier, a.target_premium
		  FROM policy a 
		  INNER JOIN ref_policy_type b ON a.policy_type = b.policy_code
		  INNER JOIN ref_household_status c ON a.household_status = c.household_code
		  INNER JOIN ref_carriers d ON a.carrier = d.carrier_code
		  WHERE a.contact_id = '$curr_cid'
		  AND policy_num = '2' LIMIT 1";
	$r = mysqli_query ($dbc, $q2); // Run the query.
	if (mysqli_num_rows($r) == 1){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$p2_first = $row['firstname'];
			$p2_last = $row['lastname'];
			$p2_type = $row['policy_desc'];
			$p2_status = $row['household_desc'];
			$p2_carrier = $row['carrier'];
			$p2_tp = $row['target_premium'];
		}
		mysqli_free_result($r);
	}
	//Policy 3
	$p3_first = $p3_last = $p3_type = $p3_status = $p3_carrier = $p3_tp = '';
	$q3 = "SELECT a.firstname, a.lastname, b.policy_desc, c.household_desc, d.carrier, a.target_premium
		  FROM policy a 
		  INNER JOIN ref_policy_type b ON a.policy_type = b.policy_code
		  INNER JOIN ref_household_status c ON a.household_status = c.household_code
		  INNER JOIN ref_carriers d ON a.carrier = d.carrier_code
		  WHERE a.contact_id = '$curr_cid'
		  AND policy_num = '3' LIMIT 1";
	$r = mysqli_query ($dbc, $q3); // Run the query.
	if (mysqli_num_rows($r) == 1){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$p3_first = $row['firstname'];
			$p3_last = $row['lastname'];
			$p3_type = $row['policy_desc'];
			$p3_status = $row['household_desc'];
			$p3_carrier = $row['carrier'];
			$p3_tp = $row['target_premium'];
		}
		mysqli_free_result($r);
	}
	//Policy 4
	$p4_first = $p4_last = $p4_type = $p4_status = $p4_carrier = $p4_tp = '';
	$q4 = "SELECT a.firstname, a.lastname, b.policy_desc, c.household_desc, d.carrier, a.target_premium
		  FROM policy a 
		  INNER JOIN ref_policy_type b ON a.policy_type = b.policy_code
		  INNER JOIN ref_household_status c ON a.household_status = c.household_code
		  INNER JOIN ref_carriers d ON a.carrier = d.carrier_code
		  WHERE a.contact_id = '$curr_cid'
		  AND policy_num = '4' LIMIT 1";
	$r = mysqli_query ($dbc, $q4); // Run the query.
	if (mysqli_num_rows($r) == 1){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$p4_first = $row['firstname'];
			$p4_last = $row['lastname'];
			$p4_type = $row['policy_desc'];
			$p4_status = $row['household_desc'];
			$p4_carrier = $row['carrier'];
			$p4_tp = $row['target_premium'];
		}
		mysqli_free_result($r);
	}
	
	$displayform = '<div style="display:none">
		<div class="modal-content">
			<h1 class="content-title">Policies For:  '.$fname.' '.$lname.'</h1>
			<div class="policyleft">
				<h4>Policy 1</h4>
				<ul>
					<li>
						<label>First Name:</label>
						<label class="displayonly">'.$p1_first.'</label>
					</li>
					<li>
						<label>Last Name:</label>
						<label class="displayonly">'.$p1_last.'</label>
					</li>
					<li>
						<label>Household Status:</label>
						<label class="displayonly">'.$p1_status.'</label>
					</li>
					<li>
						<label>Policy Type:</label>
						<label class="displayonly">'.$p1_type.'</label>
					</li>
					<li>
						<label>Carrier:</label>
						<label class="displayonly">'.$p1_carrier.'</label>
					</li>
					<li>
						<label>Target Premium:</label>
						<label class="displayonly">'.$p1_tp.'</label>
					</li>
				</ul>
				<!-- <a href="" class="linkbutton">Save Policy 1</a> -->
			</div> <!-- close contentleft -->
			
			<div class="policyright">
				<h4>Policy 2</h4>
				<ul>
					<li>
						<label>First Name:</label>
						<label class="displayonly">'.$p2_first.'</label>
					</li>
					<li>
						<label>Last Name:</label>
						<label class="displayonly">'.$p2_last.'</label>
					</li>
					<li>
						<label>Household Status:</label>
						<label class="displayonly">'.$p2_status.'</label>
					</li>
					<li>
						<label>Policy Type:</label>
						<label class="displayonly">'.$p2_type.'</label>
					</li>
					<li>
						<label>Carrier:</label>
						<label class="displayonly">'.$p2_carrier.'</label>
					</li>
					<li>
						<label>Target Premium:</label>
						<label class="displayonly">'.$p2_tp.'</label>
					</li>
				</ul>
			</div>
			<div class="cleardiv"></div>
			<div class="policyleft">
				<h4>Policy 3</h4>
				<ul>
					<li>
						<label>First Name:</label>
						<label class="displayonly">'.$p3_first.'</label>
					</li>
					<li>
						<label>Last Name:</label>
						<label class="displayonly">'.$p3_last.'</label>
					</li>
					<li>
						<label>Household Status:</label>
						<label class="displayonly">'.$p3_status.'</label>
					</li>
					<li>
						<label>Policy Type:</label>
						<label class="displayonly">'.$p3_type.'</label>
					</li>
					<li>
						<label>Carrier:</label>
						<label class="displayonly">'.$p3_carrier.'</label>
					</li>
					<li>
						<label>Target Premium:</label>
						<label class="displayonly">'.$p3_tp.'</label>
					</li>
				</ul>
			</div> <!-- close contentleft -->
			
			<div class="policyright">
				<h4>Policy 4</h4>
				<ul>
					<li>
						<label>First Name:</label>
						<label class="displayonly">'.$p4_first.'</label>
					</li>
					<li>
						<label>Last Name:</label>
						<label class="displayonly">'.$p4_last.'</label>
					</li>
					<li>
						<label>Household Status:</label>
						<label class="displayonly">'.$p4_status.'</label>
					</li>
					<li>
						<label>Policy Type:</label>
						<label class="displayonly">'.$p4_type.'</label>
					</li>
					<li>
						<label>Carrier:</label>
						<label class="displayonly">'.$p4_carrier.'</label>
					</li>
					<li>
						<label>Target Premium:</label>
						<label class="displayonly">'.$p4_tp.'</label>
					</li>
				</ul>
			</div>
		 </div> <!-- close modal-content -->
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>