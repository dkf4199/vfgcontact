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
<title>VFG Contacts - Add Personal Text Template</title>
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
	<?php include('includes/html/newheader_log.html'); ?>

	<div class="maincontent">
		<div class="welcomespan">Welcome <?php echo $_SESSION['rep_firstname'].' '.$_SESSION['rep_lastname'].'.'; ?></div>
				
			<?php
				include ('includes/selectlists.php');
				include ('includes/phpfunctions.php');
				include ('includes/config.inc.php');
				
				//check form submission
				if (isset($_POST['submitted']) && $_POST['submitted'] == "addcustomtexttemplate"){
					//DB Connection
					require_once (MYSQL);
					
					$repsid = $_SESSION['rep_id'];
					$errors = array();		//initialize errors array
					$messages = array();	//messages array
					
					// Send $_POST data thru the scrubber
					// This gets rid of any directives used to spam people
					//
					$scrubbed = array_map('spam_scrubber',$_POST);
					
					//Template Name
					if (empty($scrubbed['tt_name']) || $scrubbed['tt_name'] == ''){
						$errors[] = 'Enter template name.';
					} else {
						$ttname = strip_tags(trim($scrubbed['tt_name']));
						//$templatename = ucwords(strtolower($ttname));
						$templatename = mysqli_real_escape_string($dbc, ucwords(strtolower($ttname)));
						
						//IS template name already in db?
						$query = "SELECT text_template_name 
								  FROM vfg_rep_text_templates
								  WHERE rep_id = '$repsid'
								  AND text_template_name = '$templatename'";
						//RUN QUERY
						$rs = @mysqli_query ($dbc, $query);
						if (mysqli_num_rows($rs) == 1) {
							//email already exists!
							$errors[] = 'You have already used this template name.';
						}
						mysqli_free_result($rs);
					}
					
					//body
					if ( empty($scrubbed['tt_body']) ){
						$errors[] = 'Please enter text message content.';
					} else {
						$ttbody = mysqli_real_escape_string($dbc, trim($scrubbed['tt_body']));
					}
					
					//*************** END FIELD VALIDATION ***********************
									
					if (empty($errors)){
					
						//Generate the t_id key
						$idexists = true;
						do {
							$randnum = mt_rand();
							
							//IS UNIQUEID ALREADY IN vfg_customemail_settings table?
							$query = "SELECT tt_id 
									  FROM vfg_rep_text_templates
									  WHERE tt_id = '$randnum'";
							//RUN QUERY
							$rs = @mysqli_query ($dbc, $query);
							if (mysqli_num_rows($rs) != 1) {
								//id is unique
								$idexists = false;
							}
							mysqli_free_result($rs);
						} while ($idexists);
						// End Generate
						
						$insertsql = sprintf("INSERT INTO vfg_rep_text_templates (tt_id, rep_id, text_template_name, text_body)
										VALUES (%d,'%s','%s','%s')", 
										$randnum, $repsid, $templatename, $ttbody);
						$r = mysqli_query($dbc,$insertsql);
						if (mysqli_affected_rows($dbc) == 1){
							$messages[] = 'Text template inserted.';
						} else {
							$messages[] = 'Insert failed.';
							//echo '<p>'.mysqli_error($dbc).'</p>';
						}
												
						unset($_POST);
						mysqli_close($dbc);
					} 
					
				}  //close isset submitted
					
			?>
			
		<!-- New Custom Email Template Form -->
		<div class="formbox roundcorners opacity80">
			<h2>New Personal Text Template</h2>
			<div class="webform">
				<form name="text_config_form" id="text_config_form" action="rep_add_custom_text_template.php" method="POST" >
					
					<ul>
						<li>
							<label for="tt_name">Template Name:</label>
							<input type="text" id="tt_name" name="tt_name" class="input150" 
								value="<?php if (isset($_POST['tt_name'])) echo $_POST['tt_name']; ?>" />
						</li>
						<li>
							<label for="tt_body">Body:</label>
							<textarea id="tt_body" name="tt_body" rows="6" cols="40">
								<?php if (isset($_POST['tt_body'])) echo $_POST['tt_body']; ?>
							</textarea>
						</li>
						<li>
							<input type="hidden" name="submitted" value="addcustomtexttemplate" />
							<input type="hidden" name="original_name" id="original_name" value="" />
							<input type="hidden" name="original_body" id="original_body" value="" />
							<input type="hidden" id="data_changed" name="data_changed" value="false" />
							<input type="submit" class="button" value="Create Text Template" />
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