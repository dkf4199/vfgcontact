<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repid = $_SESSION['rep_id'];
	//$fromemail = $_SESSION['rep_email'];
	$fromemail = $_SESSION['rep_gmail'];	//rep's gmail
	
	$currcid = $_GET['currentid'];
	$fname = $_GET['currentfirstname'];
	$lname = $_GET['currentlastname'];
	$toemail = $_GET['currentemail'];
	//$fname = 'XXX';
	//$lname = 'YYY';
	
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$options = '<option value="">Select</option>'."\n".'<option value="blank">Blank Email</option>'."\n";
	$hiddenoptions = '';
	//Get distinct templates from vfg_globalemail_settings table
	$q = "SELECT DISTINCT t_id, template_name
		  FROM vfg_customemail_settings
		  WHERE rep_id = '$repid'";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) > 0){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$options .= "<option value=\"".$row['t_id']."\">".$row['template_name']."</option>\n";
			$hiddenoptions .= $row['t_id'].":".$row['template_name'].",";
		}
		mysqli_free_result($r);
	}
	
	$displayform = '<div style="display:none">
		<div class="contact-content">
			<h1 class="contact-title">Send Email To:  '.$fname.' '.$lname.' at '.$toemail.'.</h1>
			<div class="contact-loading" style="display:none"></div>
			<div class="contact-message" >You must have your Gmail credentials entered in the system to send mail.<br />
					Go to "Edit My Data" under the Rep tab to enter them.</div>
			<br />
			<div class="contentleft">
				<h4>Send A Template</h4>
				<ul>
					<li>
						<label>Email Templates:</label>
						<input type="radio" name="template_cat" id="custom" value="custom" onClick="javascript: setTemplateList(this.value);" checked />
						<span>Personal</span>
						<input type="radio" name="template_cat" id="global" value="global" onClick="javascript: setTemplateList(this.value);" />
						<span>Company</span>
					</li>
					<li>
						<label for="custom_templates">Email To Send:</label>
						<select name="template_list" id="template_list" onChange="javascript: previewEmailTemplate();" >'.$options.'</select>
					</li>
					<li>
						&nbsp;
					</li>
					<li>
						<label>&nbsp;</label>
						<a href="'.$currcid.'" class="sendlink">Send Email To '.$fname.'</a>
						
						<input type="hidden" name="mailto_firstname" id="mailto_firstname" value="'.$fname.'" />
						<input type="hidden" name="mailto_lastname" id="mailto_lastname" value="'.$lname.'" />
						<input type="hidden" name="mailto_email" id="mailto_email" value="'.$toemail.'" />
						<input type="hidden" name="mailto_from" id="mailto_from" value="'.$fromemail.'" />
						<input type="hidden" name="mailer_currentid" id="mailer_currentid" value="'.$currcid.'" />
						<input type="hidden" name="options_string" id="options_string" value="'.$hiddenoptions.'" />
					</li>
				</ul>
				
			</div> <!-- close contentleft -->
			<div class="contentright">
				<h4>Create An Email To Send</h4>
				<p>
					<label for="blank_subject">Subject:</label>
					<input type="text" id="blank_subject" name="blank_subject" class="input300"  value="" />
				</p>
				<p>
					<label for="blank_body">Body:</label>
					<textarea id="blank_body" name="blank_body" rows="6" cols="70"></textarea>
				</p>
				<p align="center">
					<a href="'.$currcid.'" class="sendlink_blank">Send This Email To '.$fname.'</a>
				</p>
			</div>
			<div class="cleardiv"></div>
			<p align="center"><a href="'.$currcid.'" class="closeemailer">Close Email Window</a></p>
			<div id="email_response">Email Response Status</div>
		 </div>
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>