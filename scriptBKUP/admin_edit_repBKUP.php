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
$curr_rid = '';
if (isset($_GET['rid'])) {
	$_SESSION['get_rid'] = $_GET['rid'];
	$curr_rid = $_GET['rid'];
}
if (isset($_GET['s'])) {
	$_SESSION['get_s'] = $_GET['s'];
	$start = $_GET['s'];
}
if (isset($_GET['p'])) {
	$_SESSION['get_p'] = $_GET['p'];
}
$admins_id = $_SESSION['admin_vfgrepid'];


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
<script>
$(window).bind('beforeunload', function(){

	var dataChanged = false;
	
	//Dynamic Fields
	var repLicensed = $("#rep_licensed").val();
	var repPurchasedPolicy = $("#purchased_policy").val();
	var repConsultant = $("#rep_consultant").val();
	
	//OG Fields - hidden
	var ogRl = $("#og_rl").val();
	var ogPp = $("#og_pp").val();
	var ogRc = $("#og_rl_con").val();
	
	//Checks for data changes
	if (repLicensed != ogRl) {dataChanged = true;}
	if (repPurchasedPolicy != ogPp) {dataChanged = true;}
	if (repConsultant != ogRc) {dataChanged = true;}
	
	if (dataChanged) {
		$("#update_button").removeClass("generalbuttongreen");
		$("#update_button").addClass("generalbuttonred");
		return "You have unsaved changes on this page.";
		
	}
	
});
</script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<?php include('includes/html/admin_header_log.html'); ?>
	
	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['admin_firstname'].' '.$_SESSION['admin_lastname'].'.'; ?></div>
		
		<!-- Take Menu off the edit page, it goes here if needed -->
		<?php
			
			//DB Connection
			require_once (MYSQL);
			
			$fn = $ln = $ph = $tzone = $sudt = $vid = $rvid = $gmail = $gpass = $rl_con = '';
			$ev_in = $ev_mg = $ev_cn = $ev_sv = $updt = $updt_by = $rl = $pp = '';
			// Make the query to pick the contact data:
			$q = "SELECT firstname, lastname, phone,
						rep_timezone, signup_date, vfgrepid,
						recruiter_vfgid, gmail_acct, gmail_pass, replevel_consultant,
						eventlevel_inviter, eventlevel_manager,
						eventlevel_consultant, eventlevel_svp,
						updated_by, update_date, rep_licensed, purchased_policy
				  FROM reps
				  WHERE rep_id = '$curr_rid' LIMIT 1";
			$rs = mysqli_query ($dbc, $q); // Run the query.
			if ($rs){
				if (mysqli_num_rows($rs) == 1) {
					while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						$fn = $row['firstname'];
						$ln = $row['lastname'];
						$ph = $row['phone'];
						$tzone = $row['rep_timezone'];
						//Format the signup_date
						if ($row['signup_date'] != ''){
							$signupdt = strtotime( $row['signup_date'] );
							$formatted_signupdt = date( 'm-d-Y h:i:s a', $signupdt );
						} else {
							$formatted_signupdt = '';
						}
						$vid = $row['vfgrepid'];
						$rvid = $row['recruiter_vfgid'];
						$gmail = $row['gmail_acct'];
						$gpass = $row['gmail_pass'];
						$rl_con = $row['replevel_consultant'];
						$ev_in = $row['eventlevel_inviter'];	//Eventlevels are Y or N
						$ev_mg = $row['eventlevel_manager'];
						$ev_cn = $row['eventlevel_consultant'];
						$ev_sv = $row['eventlevel_svp'];
						//Format the update_date
						if ($row['update_date'] != ''){
							$updatedt = strtotime( $row['update_date'] );
							$formatted_updatedt = date( 'm-d-Y h:i:s a', $updatedt );
						} else {
							$formatted_updatedt = '';
						}
						$updt_by = $row['updated_by'];
						$rl = $row['rep_licensed'];
						$pp = $row['purchased_policy'];
						
					}
				}
				mysqli_free_result($rs);
			}
			
			mysqli_close($dbc); // Close the database connection.
		?>
			
		<div class="editcontactbox roundcorners opacity80">
			<div class="editcontactform">
					<div class="refresheditdiv">
						<form action="admin_edit_rep.php" method="GET" >
							<input type="hidden" name="s" value="<?php echo $start; ?>" />
							<input type="hidden" name="rid" value="<?php echo $curr_rid; ?>" />
							<input type="submit" id="refresh_rec" class="backtobutton" value="Refresh Record" />
						</form>
					</div>
					<div class="backtodiv">
						<form action="<?php echo $_SESSION['from_page']; ?>" method="GET" >
							<input type="hidden" name="s" value="<?php echo $start; ?>" />
							<input type="hidden" name="srch" value="<?php echo $_SESSION['srch_string']; ?>" />
							<input type="submit" id="back_to_list" class="backtobutton" value="<?php echo $_SESSION['back_btn_text']; ?>" />
						</form>
					</div>
					<div class="cleardiv"></div>
					<div class="editdivclientname">Agent Edit For: <?php echo $fn.' '.$ln.'.'; ?></div>
					<div class="cleardiv"></div>
					<form name="edit_rep_record" id="edit_rep_record" onSubmit="return updateRep();" >
					<table align="center" border="0" width="80%">
						<tr>	<!-- First name, Last name -->
							<td><label style="display: inline;">First Name:</label>
								<span class="displayonly"><?php echo $fn; ?></span>
							</td>
							<td>
								<label style="display: inline;">Last Name:</label>
								<span class="displayonly"><?php echo $ln; ?></span>
							</td>
						</tr>
						<tr>	<!-- Gmail, Phone -->
							<td>
								<label style="display: inline;">Gmail:</label>
								<span class="displayonly"><?php echo $gmail; ?></span>
							</td>
							<td>
								<label style="display: inline;">Phone:</label>
								<span class="displayonly"><?php echo $ph; ?></span>
							</td>
						</tr>
						<tr>	<!-- VFG Rep Id, Recruiter Id -->
							<td>
								<label style="display: inline;">VFG Rep Id:</label>
								<!--<input type="text" id="vfgrepid" name="vfgrepid" class="input125" maxlength="40"  
									value="" />-->
								<span class="displayonly"><?php echo $vid; ?></span>
							</td>
							<td>
								<label style="display: inline;">Recruiter VFG Id:</label>
								<span class="displayonly"><?php echo $rvid; ?></span>
							</td>
						</tr>
						<tr>	<!-- Timezone, Signup Date -->
							<td>
								<label style="display: inline;">Timezone:</label>
								<span class="displayonly"><?php echo $tzone; ?></span>
							</td>
							<td>	
								<label style="display: inline;">Signup Date:</label>
								<span class="displayonly"><?php echo $formatted_signupdt; ?></span>
							</td>
						</tr>
						<tr>	<!-- Event Levels for Emails -->
							<?php 
								$inviter_checked = $manager_checked = $consultant_checked = $svp_checked = '';
								if ($ev_in == 'Y') {
									$inviter_checked = ' checked="checked" ';
								}
								if ($ev_mg == 'Y') {
									$manager_checked = ' checked="checked" ';
								}
								if ($ev_cn == 'Y') {
									$consultant_checked = ' checked="checked" ';
								}
								if ($ev_sv == 'Y') {
									$svp_checked = ' checked="checked" ';
								}
							?>
							<td>
								<label>VFG Position:</label>
								<input type="checkbox" name="eventlevel_inviter" id="eventlevel_inviter" value="Y" <?php echo $inviter_checked; ?>>Inviter&nbsp;
								<input type="checkbox" name="eventlevel_manager" id="eventlevel_manager" value="Y" <?php echo $manager_checked; ?>>Manager&nbsp;
								<input type="checkbox" name="eventlevel_consultant" id="eventlevel_consultant" value="Y" <?php echo $consultant_checked; ?>>Consultant&nbsp;
								<input type="checkbox" name="eventlevel_svp" id="eventlevel_svp" value="Y" <?php echo $svp_checked; ?>>SVP
							</td>
							<td>
								<label style="display: inline;">Licensing:</label>&nbsp;
								<a href="<?php echo $curr_rid; ?>" class="rep_licensing_modal">
									<img src="./images/search_glass.png" />
								</a>
							</td>
						</tr>
						<tr>
							<td><!-- Rep Licensed -->
								<label for="rep_licensed">Rep Licensed:</label>
								<?php
								$selected_rl = "";
								$selected_rl = $rl;
								
								echo '<select name="rep_licensed" id="rep_licensed">';
							
								foreach($yes_or_no as $id=>$name){
									if($selected_rl == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
							<td><!-- Purchased Policy -->
								<label for="purchased_policy">Purchased Policy:</label>
								<?php
								$selected_pp = "";
								$selected_pp = $pp;
								
								echo '<select name="purchased_policy" id="purchased_policy">';
							
								foreach($yes_or_no as $id=>$name){
									if($selected_pp == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td><!-- Rep Licensed -->
								<label for="rep_consultant">Consultant:</label>
								<?php
								$selected_rlc = "";
								$selected_rlc = $rl_con;
								
								echo '<select name="rep_consultant" id="rep_consultant">';
							
								foreach($yes_or_no as $id=>$name){
									if($selected_rlc == $id){
										$sel = 'selected="selected"';
									}
									else{
										$sel = '';
									}
									echo "<option $sel value=\"$id\">$name</option>";
								}
								?>
								</select>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2" align="center" >
								<input type="hidden" name="submitted" value="updatecontact" />
								<input type="hidden" id="firstname" name="firstname" value="<?php echo $fn; ?>" />
								<input type="hidden" id="lastname" name="lastname" value="<?php echo $ln; ?>" />
								<input type="hidden" id="og_firstname" name="og_firstname" value="<?php echo $fn; ?>" />
								<input type="hidden" id="og_lastname" name="og_lastname" value="<?php echo $ln; ?>" />
								<input type="hidden" id="og_gmail" name="og_gmail" value="<?php echo $gmail; ?>" />
								<input type="hidden" id="og_phone" name="og_phone" value="<?php echo $ph; ?>" />
								<input type="hidden" id="og_timezone" name="og_timezone" value="<?php echo $tzone; ?>" />
								<input type="hidden" id="og_recruiter_vfgid" name="og_recruiter_vfgid" value="<?php echo $rvid; ?>" />
								<input type="hidden" id="og_rl_con" name="og_rl_con" value="<?php echo $rl_con; ?>" />
								<input type="hidden" id="og_ev_in" name="og_ev_in" value="<?php echo $ev_in; ?>" />
								<input type="hidden" id="og_ev_mg" name="og_ev_mg" value="<?php echo $ev_mg; ?>" />
								<input type="hidden" id="og_ev_cn" name="og_ev_cn" value="<?php echo $ev_cn; ?>" />
								<input type="hidden" id="og_ev_sv" name="og_ev_sv" value="<?php echo $ev_sv; ?>" />
								<input type="hidden" id="og_rl" name="og_rl" value="<?php echo $rl; ?>" />
								<input type="hidden" id="og_pp" name="og_pp" value="<?php echo $pp; ?>" />
								
								<input type="hidden" id="data_changed" name="data_changed" value="false" />
								<input type="hidden" id="rid" name="rid" value="<?php echo $curr_rid; ?>" />
								<input type="submit" id="update_button" class="generalbuttongreen" value="Update" /><br />
								<div id="ajax_verify_update"></div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>	
	</div> <!-- close main content -->
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>