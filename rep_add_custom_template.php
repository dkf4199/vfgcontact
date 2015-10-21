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
<title>VFG Contacts - Add custom email template</title>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact.css" />
<link rel="stylesheet" type="text/css" media="screen" href="./css/vfgcontact_forms.css" />
<link rel="stylesheet" href="./css/start/jquery-ui-1.10.3.custom.css">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.3.custom.js"></script>
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
				
				//check form submission
				if (isset($_POST['submitted']) && $_POST['submitted'] == "addcustomtemplate"){
					//DB Connection
					require_once (MYSQL);
					
					$repsid = $_SESSION['rep_id'];
					$errors = array();		//initialize errors array
					$iomsgs = array();		//io messages array
					
					// Send $_POST data thru the scrubber
					// This gets rid of any directives used to spam people
					//
					$scrubbed = array_map('spam_scrubber',$_POST);
					
					//Template Name
					if (empty($scrubbed['t_name']) || $scrubbed['t_name'] == ''){
						$errors[] = 'Enter template name.';
					} else {
						$tname = strip_tags(trim($scrubbed['t_name']));
						//$templatename = ucwords(strtolower($tname));
						$templatename = mysqli_real_escape_string($dbc, ucwords(strtolower($tname)));
							//IS template name already in db?
							$query = "SELECT template_name 
									  FROM vfg_customemail_settings
									  WHERE rep_id = '$repsid'
									  AND template_name = '$templatename'";
							//RUN QUERY
							$rs = @mysqli_query ($dbc, $query);
							if (mysqli_num_rows($rs) == 1) {
								//email already exists!
								$errors[] = 'You have already used this template name.';
							}
							mysqli_free_result($rs);
					}
					
					//Subject
					if ( empty($scrubbed['t_subject']) ){
						$errors[] = 'Please enter subject line.';
					} else {
						$subject = mysqli_real_escape_string($dbc, trim($scrubbed['t_subject']));
					}
					
					//Salutation
					if ( empty($scrubbed['t_salutation']) ){
						$errors[] = 'Please select a salutation.';
					} else {
						$salutation = $scrubbed['t_salutation'];
					}
					
					//body
					if ( empty($scrubbed['t_body']) ){
						$errors[] = 'Please enter body content.';
					} else {
						$body = mysqli_real_escape_string($dbc, trim($scrubbed['t_body']));
					}
					
					//Image Link
					if ( empty($scrubbed['t_imagelink']) ){
						$errors[] = 'Please provide link to your VFG landing page.';
					} else {
						$imagelink = mysqli_real_escape_string($dbc, trim($scrubbed['t_imagelink']));
					}
					
					//Closing
					if ( $scrubbed['t_closing'] == '' ){
						$errors[] = 'Please select a closing.';
					} else {
						$closing = $scrubbed['t_closing'];
					}
					
					//*************** END FIELD VALIDATION ***********************
									
					if (empty($errors)){
					
						//Generate the t_id key
						$idexists = true;
						do {
							$randnum = mt_rand();
							
							//IS UNIQUEID ALREADY IN vfg_customemail_settings table?
							$query = "SELECT t_id 
									  FROM vfg_customemail_settings 
									  WHERE t_id = '$randnum'";
							//RUN QUERY
							$rs = @mysqli_query ($dbc, $query);
							if (mysqli_num_rows($rs) != 1) {
								//id is unique
								$idexists = false;
							}
							mysqli_free_result($rs);
						} while ($idexists);
						// End Generate
						
						$insertsql = sprintf("INSERT INTO vfg_customemail_settings (t_id, rep_id, template_name, subject, salutation, body, img_link, closing)
										VALUES (%d,'%s','%s','%s','%s','%s','%s','%s')", 
										$randnum, $repsid, $templatename, $subject, $salutation, $body, $imagelink, $closing);
						$r = mysqli_query($dbc,$insertsql);
						if (mysqli_affected_rows($dbc) == 1){
							$iomsgs[] = 'Template inserted.';
						} else {
							$iomsgs[] = 'Insert failed.';
							//echo '<p>'.mysqli_error($dbc).'</p>';
						}
						//display $iomsgs[] array
						echo '<div id="messages">';
						foreach ($iomsgs as $msg){
							echo "$msg<br />\n";
						}
						echo '</div>';
						
						unset($_POST);
						mysqli_close($dbc);
					} 
					
				}  //close isset submitted
					
			?>
			
		<!-- Add Custom Email Template Form -->
		<div class="formbox roundcorners opacity80">
			<h2>New Personal Email Template</h2>
			<div class="webform">
				<form name="email_config_form" id="email_config_form" action="rep_add_custom_template.php" method="POST" >
					<ul>
						<li>
							<label for="t_name">Template Name:</label>
							<input type="text" id="t_name" name="t_name" class="input150" 
								value="<?php if (isset($_POST['t_name'])) echo $_POST['t_name']; ?>" />
						</li>
						<li>
							<label for="t_subject">Subject:</label>
							<input type="text" id="t_subject" name="t_subject" class="input200" 
								value="<?php if (isset($_POST['t_subject'])) echo $_POST['t_subject']; ?>" />
						</li>
						<li>
							<label for="t_salutation">Salutation:</label>
							<select name="t_salutation" id="t_salutation">
								<?php
									$selected_salutation = '';
									if (isset($_POST['t_salutation'])){
										$selected_salutation = $_POST['t_salutation'];
									} 
									foreach($email_salutations as $id=>$name){
										if($selected_salutation == $id){
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
							<label for="t_body">Body:</label>
							<textarea id="t_body" name="t_body" rows="6" cols="40">
								<?php if (isset($_POST['t_body'])) echo $_POST['t_body']; ?>
							</textarea>
						</li>
						<li>
							<label for="t_imagelink">Page Link:</label>
							http://<input type="text" id="t_imagelink" name="t_imagelink" class="input200" 
								value="<?php if (isset($_POST['t_imagelink'])) echo $_POST['t_imagelink']; ?>" />
						</li>
						<li>
							<label for="t_closing">Closing:</label>
							<select name="t_closing" id="t_closing">
								<?php
									$selected_closing = '';
									if (isset($_POST['t_closing'])){
										$selected_closing = $_POST['t_closing'];
									}
									foreach($email_closings as $id=>$name){
										if($selected_closing == $id){
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
							<input type="hidden" name="submitted" value="addcustomtemplate" />
							<input type="hidden" name="original_name" id="original_name" value="" />
							<input type="hidden" name="original_subject" id="original_subject" value="" />
							<input type="hidden" name="original_salutation" id="original_salutation" value="" />
							<input type="hidden" name="original_body" id="original_body" value="" />
							<input type="hidden" name="original_imglink" id="original_imglink" value="" />
							<input type="hidden" name="original_closing" id="original_closing" value="" />
							<input type="hidden" id="data_changed" name="data_changed" value="false" />
							<input type="submit" class="button" value="Update" />
						</li>
						<li>
							<div id="ajax_emailsettings_update"></div>
						</li>
					</ul>
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
		</div>	
	</div> <!-- close main content -->	
</div>	<!-- close wrapper -->
<?php include('includes/html/footer.html'); ?>
</body>
</html>