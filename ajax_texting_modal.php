<?php
session_start();
	//echo '<p>You selected '.$_GET['companyid'].'</p>';
	$repid = $_SESSION['rep_id'];
	$fromemail = $_SESSION['rep_email'];
	
	$currcid = $_GET['currentid'];
	$fname = $_GET['currentfirstname'];
	$lname = $_GET['currentlastname'];
	$toemail = $_GET['currentemail'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	$options = '<option value="">Select</option><option value="blank">Blank Template</option>'."\n";
	$hiddenoptions = '';
	//Get distinct templates from vfg_globalemail_settings table
	$q = "SELECT DISTINCT tt_id, text_template_name
		  FROM vfg_rep_text_templates
		  WHERE rep_id = '$repid'";
	$r = @mysqli_query ($dbc, $q); // Run the query.
	if (mysqli_num_rows($r) > 0){
		//Build the options string with the template ids
		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			$options .= "<option value=\"".$row['tt_id']."\">".$row['text_template_name']."</option>\n";
			$hiddenoptions .= $row['tt_id'].":".$row['text_template_name'].",";
		}
		mysqli_free_result($r);
	}
	
	$displayform = '<div style="display:none">
		<div class="contact-top"></div>
		<div class="contact-content">
		<h1 class="contact-title">Text Templates</h1>
		<div class="contact-loading" style="display:none"></div>
		<div class="contact-message" style="display:none"></div>
		<br />
		<div class="divleft">
		<ul>
			<li>
				<label for="personal_text_templates">Text Templates:</label>
				<select name="personal_text_templates" id="personal_text_templates" 
						onChange="javascript: getTextTemplate(this.value);">'.$options.'</select>
			</li>
			<li>
				&nbsp;
			</li>
			<li>
				&nbsp;
			</li>
			<li>
				<label>&nbsp;</label>
				<a href="'.$currcid.'" class="texttemplatecloselink">Close</a>
				<input type="hidden" name="contact_id" id="contact_id" value="'.$currcid.'" />
				<input type="hidden" name="reps_id" id="reps_id" value="'.$repid.'" />
				<input type="hidden" name="mailto_firstname" id="mailto_firstname" value="'.$fname.'" />
				<input type="hidden" name="mailto_lastname" id="mailto_lastname" value="'.$lname.'" />
				<input type="hidden" name="mailto_email" id="mailto_email" value="'.$toemail.'" />
				<input type="hidden" name="mailto_from" id="mailto_from" value="'.$fromemail.'" />
				<input type="hidden" name="options_string" id="options_string" value="'.$hiddenoptions.'" />
			</li>
		</ul>
		</div>
		<div id="ajax_display_textmessage" class="divright"></div>
		<div class="cleardiv"></div>
	 </div> <!-- close class="contact-content" -->
	</div>';
	echo $displayform;
	
	
	//unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>