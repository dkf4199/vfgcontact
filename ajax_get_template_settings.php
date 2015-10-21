<?php
session_start();
		
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');	
	//DB Connection
	require_once (MYSQL);
	
	//$_GET VARS
	if (isset($_GET['thistemplate'])){
		$tid = $_GET['thistemplate'];
	} else {
		$tid = '';
	}
	if (isset($_GET['thisrepid'])){
		$repsid = $_GET['thisrepid'];
	} else {
		$repsid = '';
	}
	// Make the query to get template data:
	$q = "SELECT t_id, template_name, subject, salutation, body,
				img_link, closing 
		  FROM vfg_customemail_settings
		  WHERE rep_id = '$repsid' 
		  AND t_id = '$tid'
		  LIMIT 1";	
		
	$r = mysqli_query ($dbc, $q); // Run the query.

	if ($r) { // If it ran OK, get the data if there is a record.
		if (mysqli_num_rows($r) == 1){
			// Fetch and print all the records:
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				
				$formstr = '<!--<div id="preview-link"><a href="#" onClick="return previewTemplate(\''.$row['t_id'].'\')" >Preview Template</a></div>-->
				<div class="formbox roundcorners opacity80">
					<div class="webform">
				<form name="email_config_form" id="email_config_form" onSubmit="return updateTemplateSettings();" >
					<h2>Template Settings for Template '.$row['template_name'].'</h2>
					<ul>
						<li>
							<label for="t_name">Template Name:</label>
							<input type="text" id="t_name" name="t_name" class="input150" value="'.$row['template_name'].'" />
						</li>
						<li>
							<label for="t_subject">Subject:</label>
							<input type="text" id="t_subject" name="t_subject" class="input200"  
								value="'.$row['subject'].'" />
						</li>
						<li>
							<label for="t_salutation">Salutation:</label>
							<select name="t_salutation" id="t_salutation">';
							$selected_salutation = $row['salutation'];
							foreach($email_salutations as $id=>$name){
								if($selected_salutation == $id){
									$sel = 'selected="selected"';
								}
								else{
									$sel = '';
								}
								$formstr .= "<option $sel value=\"$id\">$name</option>";
							}
					$formstr .= '</select>
						</li>
						<li>
							<label for="t_body">Body:</label>
							<textarea id="t_body" name="t_body" rows="6" cols="40">'.stripslashes($row['body']).'</textarea>
						</li>
						<li>
							<label for="t_imagelink">Page Link:</label>
							http://<input type="text" id="t_imagelink" name="t_imagelink" class="input200"  
								value="'.$row['img_link'].'" />
						</li>
						<li>
							<label for="t_closing">Closing:</label>
							<select name="t_closing" id="t_closing">';
							$selected_closing = $row['closing'];
							foreach($email_closings as $id=>$name){
								if($selected_closing == $id){
									$sel = 'selected="selected"';
								}
								else{
									$sel = '';
								}
								$formstr .= "<option $sel value=\"$id\">$name</option>";
							}
							
					$formstr .= '</select>
						</li>
						<li>
							<input type="hidden" name="submitted" value="updatesettings" />
							<input type="hidden" name="rep" id="rep" value="'.$repsid.'" />
							<input type="hidden" name="tid" id="tid" value="'.$tid.'" />
							<input type="hidden" name="original_name" id="original_name" value="'.$row['template_name'].'" />
							<input type="hidden" name="original_subject" id="original_subject" value="'.$row['subject'].'" />
							<input type="hidden" name="original_salutation" id="original_salutation" value="'.$row['salutation'].'" />
							<input type="hidden" name="original_body" id="original_body" value="'.$row['body'].'" />
							<input type="hidden" name="original_imglink" id="original_imglink" value="'.$row['img_link'].'" />
							<input type="hidden" name="original_closing" id="original_closing" value="'.$row['closing'].'" />
							<input type="hidden" id="data_changed" name="data_changed" value="false" />
							<input type="submit" class="button" value="Update" />
						</li>
					</ul>
					<div id="ajax_emailsettings_update"></div>
				</form>
				</div>
				</div>';
				echo $formstr;
			}	//close while
		
		} 	//close num_rows
			
	} //close if($r)
	mysqli_close($dbc);
?>