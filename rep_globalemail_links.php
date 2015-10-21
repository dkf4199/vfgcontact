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
<title>VFG Contact - Add Company Email Image Links</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
<script>
$(document).ready(function() {
});
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
				
		<?php
			include ('includes/selectlists.php');
			include ('includes/phpfunctions.php');
			include ('includes/config.inc.php');
			//DB Connection
			require_once (MYSQL);
			
			$repsid = $_SESSION['rep_id'];
			
			//check form submission
			if (isset($_POST['submitted']) && $_POST['submitted'] == "updatelinks"){
				
				
				$errors = array();		//initialize errors array
				$messages = array();	//initialize messages array
				
				$have_data = false;
				$data_changed = false;
				//VALIDATE FIELDS
				// 08/02/2013 dkf - Change to allow for blank fields
				//                  fill with 'none'
				//*******************************************************
				//Link1
				if (empty($_POST['link_1'])){
					$link1 = '';
				} else {
					$link1 = trim($_POST['link_1']);
					$have_data = true;	
				}
				//Link2
				if (empty($_POST['link_2'])){
					$link2 = '';
				} else {
					$link2 = trim($_POST['link_2']);
					$have_data = true;
				}
				//Link3
				if (empty($_POST['link_3'])){
					$link3 = '';
				} else {
					$link3 = trim($_POST['link_3']);
					$have_data = true;
				}
				//Link4
				if (empty($_POST['link_4'])){
					$link4 = '';
				} else {
					$link4 = trim($_POST['link_4']);
					$have_data = true;
				}
				//Link5
				if (empty($_POST['link_5'])){
					$link5 = '';
				} else {
					$link5 = trim($_POST['link_5']);
					$have_data = true;
				}
				
				//Checks to see if data has changed
				if ($link1 != $_POST['og_link1']){
					$data_changed = true;
				}
				if ($link2 != $_POST['og_link2']){
					$data_changed = true;
				}
				if ($link3 != $_POST['og_link3']){
					$data_changed = true;
				}
				if ($link4 != $_POST['og_link4']){
					$data_changed = true;
				}
				if ($link5 != $_POST['og_link5']){
					$data_changed = true;
				}
				//*************** END FIELD VALIDATION ***********************
				
				if (empty($errors)){
				
					$rec_exists = false;
					//Does Record Exist in vfg_global_imagelinks
					$query = "SELECT rep_id FROM vfg_global_imagelinks WHERE rep_id = '$repsid'";
					$rs = mysqli_query ($dbc, $query);
					if ($rs){
						if (mysqli_num_rows($rs) == 1) {
							//id is unique
							$rec_exists = true;
						}
						mysqli_free_result($rs);
					}
					
					//Do Insert or Update
					if ($rec_exists){
						//update
						if ($data_changed) {
							$updatesql = "UPDATE vfg_global_imagelinks 
										  SET link1='$link1', 
											  link2='$link2', 
											  link3='$link3',
											  link4='$link4',
											  link5='$link5'
										  WHERE rep_id='$repsid' LIMIT 1"; 
							//RUN UPDATE QUERY
							$rs= mysqli_query($dbc, $updatesql);
							
							if (mysqli_affected_rows($dbc) == 1){
								$messages[] = 'Update successful.';
							} 
							
						} else {
							$messages[] = 'No data change.';
							
						}	//end data_changed
						
					} else {
						//insert
						$q = sprintf("INSERT INTO vfg_global_imagelinks (rep_id, link1, link2, link3, link4, link5)
										VALUES ('%s','%s','%s','%s','%s','%s')", $repsid, $link1, $link2, $link3, $link4, $link5);
						$r = mysqli_query($dbc,$q);
						if (mysqli_affected_rows($dbc) == 1){
							$messages[] = 'Links Added.';
						} else {
							$messages[] = 'Insert Error: '.mysqli_error($dbc);
						}
					}
									
				}	//close empty(errors) 
					
			}	//close isset submitted
			
			$tlink1 = $tlink2 = $tlink3 = $tlink4 = $tlink5 = '';
			
			//PULL RECORD FOR DISPLAY
			$query = "SELECT link1, link2, link3, link4, link5 FROM vfg_global_imagelinks WHERE rep_id = '$repsid'";
			$rs = mysqli_query ($dbc, $query);
			if ($rs){
				if (mysqli_num_rows($rs) == 1) {
					while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
						$tlink1 = $row['link1'];
						$tlink2 = $row['link2'];
						$tlink3 = $row['link3'];
						$tlink4 = $row['link4'];
						$tlink5 = $row['link5'];
					}
				}
				mysqli_free_result($rs);
			}
			//CLOSE connection
			mysqli_close($dbc);
		  ?>
		<!-- Company Email Links Form -->
		<div class="globallinks roundcorners opacity80">
			<form name="rep_global_links" id="rep_global_links" action="rep_globalemail_links.php" method="POST" >
				<table width="100%" border="0">
					<tr>
						<th colspan="2">Image Links for VFG Image Templates</th>
					</tr>
					<tr>
						<td>
							<h4>VFG Agent Intro</h4>
							<img src="./images/VFGAgentIntro.jpg"><br />
							http://<input type="text" id="link_1" name="link_1" class="input200" maxlength="80"
							value="<?php echo $tlink1; ?>" />
						</td>
						<td>
							<h4>VFG Incomplete Intro</h4>
							<img src="./images/VFGIncompleteIntro.jpg"><br />
							http://<input type="text" id="link_2" name="link_2" class="input200" maxlength="80"
							value="<?php echo $tlink2; ?>" />
						</td>	
					</tr>
					<tr>
						<td>
							<h4>VFG RE Intro 1</h4>
							<img src="./images/VFGREIntro1.jpg"><br />
							http://<input type="text" id="link_3" name="link_3" class="input200" maxlength="80"
							value="<?php echo $tlink3; ?>" />
						</td>
						<td>
							<h4>VFG RE Intro 2</h4>
							<img src="./images/VFGREIntro2.jpg"><br />
							http://<input type="text" id="link_4" name="link_4" class="input200" maxlength="80"
							value="<?php echo $tlink4; ?>" />
						</td>
					</tr>
					<tr>
						<td>
							<h4>VFG Thanks For Your Interest</h4>
							<img src="./images/VFGThanksInterest.jpg"><br />
							http://<input type="text" id="link_5" name="link_5" class="input200" maxlength="80"
							value="<?php echo $tlink5; ?>" />
						</td>
						<td>&nbsp;</td>
					</tr>	
					<tr>
						<td colspan="2" align="center">
							<input type="hidden" name="submitted" value="updatelinks" />
							<input type="hidden" name="og_link1" value="<?php echo $tlink1; ?>" />
							<input type="hidden" name="og_link2" value="<?php echo $tlink2; ?>" />
							<input type="hidden" name="og_link3" value="<?php echo $tlink3; ?>" />
							<input type="hidden" name="og_link4" value="<?php echo $tlink4; ?>" />
							<input type="hidden" name="og_link5" value="<?php echo $tlink5; ?>" />
							<input type="submit" class="generalbutton" value="Update Links" />
						</td>
					</tr>
				</table>
				<div id="messages">
					<?php
						//Display error messages, if any.
						if (!empty($errors)) {
							echo 'ERROR:<br />';
							foreach ($errors as $msg) {
								echo " - $msg<br />\n";
							}
						}
						//Display script messages, if any.
						if (!empty($messages)) {
							foreach ($messages as $msg) {
								echo "$msg<br />\n";
							}
						}
					?>
				</div>
			</form>
		</div>		
	</div> <!-- close main content -->

</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>