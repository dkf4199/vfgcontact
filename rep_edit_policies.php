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
if (isset($_GET['cid'])) {
	$_SESSION['get_contactid'] = $_GET['cid'];
	$currcid = $_GET['cid'];
}
if (isset($_GET['s'])) {
	$_SESSION['get_s'] = $_GET['s'];
	$start = $_GET['s'];
}
if (isset($_GET['p'])) {
	$_SESSION['get_p'] = $_GET['p'];
}
$fname = $lname = '';
if (isset($_GET['fn'])) {
	$fname = $_GET['fn'];
}
if (isset($_GET['ln'])) {
	$lname = $_GET['ln'];
}
$inviter = '';
if (isset($_GET['iv'])){
	$inviter = $_GET['iv'];
}
$manager = '';
if (isset($_GET['am'])){
	$manager = $_GET['am'];
}
$consultant = '';
if (isset($_GET['ac'])){
	$consultant = $_GET['ac'];
}
$repsid = $_SESSION['rep_id'];
$repsvfgid = $_SESSION['vfgrep_id'];

include ('includes/selectlists.php');
include ('includes/phpfunctions.php');
include ('includes/config.inc.php');
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
<title>VFG Contacts - Edit Contact</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_tables.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/button.css" />
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
	<?php include('includes/html/newheader_log.html'); ?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
		
		<!-- Take Menu off the edit page, it goes here if needed -->
		<?php
			date_default_timezone_set($_SESSION['rep_tz']);
			//DB Connection
			require_once (MYSQL);
			
			
			//GET POLICY INFO
			//*****************************************************************
			//Policy 1
			$p1_first = $p1_last = $p1_type = $p1_status = $p1_carrier = $p1_tp = '';
			$q1 = "SELECT firstname, lastname, policy_type, household_status, carrier, target_premium
				  FROM policy
				  WHERE contact_id = '$currcid'
				  AND policy_num = '1' LIMIT 1";
			$r = mysqli_query ($dbc, $q1); // Run the query.
			if (mysqli_num_rows($r) == 1){
				//Build the options string with the template ids
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$p1_first = $row['firstname'];
					$p1_last = $row['lastname'];
					$p1_type = $row['policy_type'];
					$p1_status = $row['household_status'];
					$p1_carrier = $row['carrier'];
					$p1_tp = $row['target_premium'];
				}
				mysqli_free_result($r);
			}
			//Policy 2
			$p2_first = $p2_last = $p2_type = $p2_status = $p2_carrier = $p2_tp = '';
			$q2 = "SELECT firstname, lastname, policy_type, household_status, carrier, target_premium
				  FROM policy
				  WHERE contact_id = '$currcid'
				  AND policy_num = '2' LIMIT 1";
			$r = mysqli_query ($dbc, $q2); // Run the query.
			if (mysqli_num_rows($r) == 1){
				//Build the options string with the template ids
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$p2_first = $row['firstname'];
					$p2_last = $row['lastname'];
					$p2_type = $row['policy_type'];
					$p2_status = $row['household_status'];
					$p2_carrier = $row['carrier'];
					$p2_tp = $row['target_premium'];
				}
				mysqli_free_result($r);
			}
			//Policy 3
			$p3_first = $p3_last = $p3_type = $p3_status = $p3_carrier = $p3_tp = '';
			$q3 = "SELECT firstname, lastname, policy_type, household_status, carrier, target_premium
				  FROM policy
				  WHERE contact_id = '$currcid'
				  AND policy_num = '3' LIMIT 1";
			$r = mysqli_query ($dbc, $q3); // Run the query.
			if (mysqli_num_rows($r) == 1){
				//Build the options string with the template ids
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$p3_first = $row['firstname'];
					$p3_last = $row['lastname'];
					$p3_type = $row['policy_type'];
					$p3_status = $row['household_status'];
					$p3_carrier = $row['carrier'];
					$p3_tp = $row['target_premium'];
				}
				mysqli_free_result($r);
			}
			//Policy 4
			$p4_first = $p4_last = $p4_type = $p4_status = $p4_carrier = $p4_tp = '';
			$q4 = "SELECT firstname, lastname, policy_type, household_status, carrier, target_premium
				  FROM policy
				  WHERE contact_id = '$currcid'
				  AND policy_num = '4' LIMIT 1";
			$r = mysqli_query ($dbc, $q4); // Run the query.
			if (mysqli_num_rows($r) == 1){
				//Build the options string with the template ids
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$p4_first = $row['firstname'];
					$p4_last = $row['lastname'];
					$p4_type = $row['policy_type'];
					$p4_status = $row['household_status'];
					$p4_carrier = $row['carrier'];
					$p4_tp = $row['target_premium'];
				}
				mysqli_free_result($r);
			}
			mysqli_close($dbc); // Close the database connection.
		?>
			
		<div class="editpolicybox roundcorners opacity80">
			<input type="hidden" name="policy_cid" id="policy_cid" value="<?php echo $currcid; ?>" />
			<input type="hidden" name="p_inviter" id="p_inviter" value="<?php echo $inviter; ?>" />
			<input type="hidden" name="p_manager" id="p_manager" value="<?php echo $manager; ?>" />
			<input type="hidden" name="p_consultant" id="p_consultant" value="<?php echo $consultant; ?>" />
			<div class="webform">
				<div class="refresheditdiv">
					<form action="rep_edit_policies.php" method="GET" >
						<input type="hidden" name="s" value="<?php echo $start; ?>" />
						<input type="hidden" name="cid" value="<?php echo $currcid; ?>" />
						<input type="hidden" name="fn" value="<?php echo $fname; ?>" />
						<input type="hidden" name="ln" value="<?php echo $lname; ?>" />
						<input type="submit" id="refresh_rec" class="backtobutton" value="Refresh Policies" />
					</form>
				</div>
				<div class="backtodiv">
					<form action="rep_edit_contact.php" method="GET" >
						<input type="hidden" name="cid" value="<?php echo $currcid; ?>" />
						<input type="hidden" name="s" value="<?php echo $start; ?>" />
						<input type="hidden" name="srch" value="<?php echo $_SESSION['srch_string']; ?>" />
						<input type="submit" id="back_to_list" class="backtobutton" value="Back To Edit Screen" />
					</form>
				</div>
				<div class="cleardiv"></div>
				<div class="editdivclientname">Policies for: <?php echo $fname.' '.$lname; ?></div>
				
				<!-- POLICY 1 DIV -->
				<div class="policyleftdiv">
					<h4>Policy 1</h4>
					<ul>
						<li>
							<label>First Name:</label>
							<input type="text" id="p1_first_name" name="p1_first_name" class="input150 capitalwords" maxlength="30"
										value="<?php echo $p1_first; ?>" />
						</li>
						<li>
							<label>Last Name:</label>
							<input type="text" id="p1_last_name" name="p1_last_name" class="input150 capitalwords" maxlength="50"
										value="<?php echo $p1_last; ?>" />
						</li>
						<li>
							<label>Household Status:</label>
							<?php
								$selected_p1_status = $p1_status;
								
								echo '<select name="p1_hstatus" id="p1_hstatus">';
							
								foreach($household_status as $id=>$name){
									if($selected_p1_status == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Policy Type:</label>
							<?php
								$selected_p1_type = $p1_type;
								
								echo '<select name="p1_ptype" id="p1_ptype">';
							
								foreach($policy_type as $id=>$name){
									if($selected_p1_type == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Carrier:</label>
							<?php
								$selected_p1_carrier = $p1_carrier;
								
								echo '<select name="p1_carrier" id="p1_carrier">';
							
								foreach($carriers as $id=>$name){
									if($selected_p1_carrier == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Target Premium:</label>
							<input type="text" id="p1_target_premium" name="p1_target_premium" class="input100" maxlength="30"
										value="<?php echo $p1_tp; ?>" />
						</li>
						<li>
							<label>&nbsp;</label>
							<a href="" id="save_policy_1" class="myButton">Save/Update Policy 1</a>
							<div id="policy1_msgs"></div>
					</ul>
				</div> <!-- close policyleftdiv -->
				
				<!-- POLICY 2 DIV -->
				<div class="policyrightdiv">
					<h4>Policy 2</h4>
					<ul>
						<li>
							<label>First Name:</label>
							<input type="text" id="p2_first_name" name="p2_first_name" class="input150 capitalwords" maxlength="30"
										value="<?php echo $p2_first; ?>" />
						</li>
						<li>
							<label>Last Name:</label>
							<input type="text" id="p2_last_name" name="p2_last_name" class="input150 capitalwords" maxlength="50"
										value="<?php echo $p2_last; ?>" />
						</li>
						<li>
							<label>Household Status:</label>
							<?php
								$selected_p2_status = $p2_status;
								
								echo '<select name="p2_hstatus" id="p2_hstatus">';
							
								foreach($household_status as $id=>$name){
									if($selected_p2_status == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Policy Type:</label>
							<?php
								$selected_p2_type = $p2_type;
								
								echo '<select name="p2_ptype" id="p2_ptype">';
							
								foreach($policy_type as $id=>$name){
									if($selected_p2_type == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Carrier:</label>
							<?php
								$selected_p2_carrier = $p2_carrier;
								
								echo '<select name="p2_carrier" id="p2_carrier">';
							
								foreach($carriers as $id=>$name){
									if($selected_p2_carrier == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Target Premium:</label>
							<input type="text" id="p2_target_premium" name="p2_target_premium" class="input100" maxlength="30"
										value="<?php echo $p2_tp; ?>" />
						</li>
						<li>
							<label>&nbsp;</label>
							<a href="" id="save_policy_2" class="myButton">Save/Update Policy 2</a>
							<div id="policy2_msgs"></div>
					</ul>
				</div> <!-- close policyrightdiv -->
				<div class="cleardiv"></div>
				
				<!-- POLICY 3 DIV -->
				<div class="policyleftdiv">
					<h4>Policy 3</h4>
					<ul>
						<li>
							<label>First Name:</label>
							<input type="text" id="p3_first_name" name="p3_first_name" class="input150 capitalwords" maxlength="30"
										value="<?php echo $p3_first; ?>" />
						</li>
						<li>
							<label>Last Name:</label>
							<input type="text" id="p3_last_name" name="p3_last_name" class="input150 capitalwords" maxlength="50"
										value="<?php echo $p3_last; ?>" />
						</li>
						<li>
							<label>Household Status:</label>
							<?php
								$selected_p3_status = $p3_status;
								
								echo '<select name="p3_hstatus" id="p3_hstatus">';
							
								foreach($household_status as $id=>$name){
									if($selected_p3_status == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Policy Type:</label>
							<?php
								$selected_p3_type = $p3_type;
								
								echo '<select name="p3_ptype" id="p3_ptype">';
							
								foreach($policy_type as $id=>$name){
									if($selected_p3_type == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Carrier:</label>
							<?php
								$selected_p3_carrier = $p3_carrier;
								
								echo '<select name="p3_carrier" id="p3_carrier">';
							
								foreach($carriers as $id=>$name){
									if($selected_p3_carrier == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Target Premium:</label>
							<input type="text" id="p3_target_premium" name="p3_target_premium" class="input100" maxlength="30"
										value="<?php echo $p3_tp; ?>" />
						</li>
						<li>
							<label>&nbsp;</label>
							<a href="" id="save_policy_3" class="myButton">Save/Update Policy 3</a>
							<div id="policy3_msgs"></div>
					</ul>
					<!-- <a href="" class="linkbutton">Save Policy 1</a> -->
				</div> <!-- close policyleftdiv -->
				
				<!-- POLICY 4 DIV -->
				<div class="policyrightdiv">
					<h4>Policy 4</h4>
					<ul>
						<li>
							<label>First Name:</label>
							<input type="text" id="p4_first_name" name="p4_first_name" class="input150 capitalwords" maxlength="30"
										value="<?php echo $p4_first; ?>" />
						</li>
						<li>
							<label>Last Name:</label>
							<input type="text" id="p4_last_name" name="p4_last_name" class="input150 capitalwords" maxlength="50"
										value="<?php echo $p4_last; ?>" />
						</li>
						<li>
							<label>Household Status:</label>
							<?php
								$selected_p4_status = $p4_status;
								
								echo '<select name="p4_hstatus" id="p4_hstatus">';
							
								foreach($household_status as $id=>$name){
									if($selected_p4_status == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Policy Type:</label>
							<?php
								$selected_p4_type = $p4_type;
								
								echo '<select name="p4_ptype" id="p4_ptype">';
							
								foreach($policy_type as $id=>$name){
									if($selected_p4_type == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Carrier:</label>
							<?php
								$selected_p4_carrier = $p4_carrier;
								
								echo '<select name="p4_carrier" id="p4_carrier">';
							
								foreach($carriers as $id=>$name){
									if($selected_p4_carrier == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
							?>
							</select>
						</li>
						<li>
							<label>Target Premium:</label>
							<input type="text" id="p4_target_premium" name="p4_target_premium" class="input100" maxlength="30"
										value="<?php echo $p4_tp; ?>" />
						</li>
						<li>
							<label>&nbsp;</label>
							<a href="" id="save_policy_4" class="myButton">Save/Update Policy 4</a>
							<div id="policy4_msgs"></div>
					</ul>
					<!-- <a href="" class="linkbutton">Save Policy 1</a> -->
				</div> <!-- close policyrightdiv -->
				<div class="cleardiv"></div>
			</div> <!-- close webform -->
			
		</div>	
		
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>