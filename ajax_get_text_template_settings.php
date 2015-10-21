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
	$q = "SELECT tt_id, text_template_name, text_body 
		  FROM vfg_rep_text_templates
		  WHERE rep_id = '$repsid' 
		  AND tt_id = '$tid'
		  LIMIT 1";	
		
	$r = mysqli_query ($dbc, $q); // Run the query.

	if ($r) { // If it ran OK, get the data if there is a record.
		if (mysqli_num_rows($r) == 1){
			// Fetch and print all the records:
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				
				$formstr = '<div id="preview-link"><!--<a href="#" onClick="return previewTemplate(\''.$row['tt_id'].'\')" >Preview Template</a>--></div>
				<div class="formbox roundcorners opacity80">
					<div class="webform">
				<form name="text_config_form" id="text_config_form" onSubmit="return updateTextTemplateSettings()" >
					<h2>Text Template '.$row['text_template_name'].'</h2>
					<ul>
						<li>
							<label for="tt_name">Template Name:</label>
							<input type="text" id="tt_name" name="tt_name" class="input150" value="'.$row['text_template_name'].'" />
						</li>
						<li>
							<label for="tt_body">Body:</label>
							<textarea id="tt_body" name="tt_body" rows="6" cols="40">'.$row['text_body'].'</textarea>
						</li>
						<li>
							<input type="hidden" name="submitted" value="updatetextsettings" />
							<input type="hidden" name="rep" id="rep" value="'.$repsid.'" />
							<input type="hidden" name="tid" id="tid" value="'.$tid.'" />
							<input type="hidden" name="original_name" id="original_name" value="'.$row['text_template_name'].'" />
							<input type="hidden" name="original_body" id="original_body" value="'.$row['text_body'].'" />
							<input type="hidden" id="data_changed" name="data_changed" value="false" />
							<input type="submit" class="button" value="Update" />
						</li>
						<li>
							<div id="ajax_emailsettings_update"></div>
						</li>
					</ul>
				</form>
				</div>
				</div>';
				echo $formstr;
			}	//close while
		
		} 	//close num_rows
			
	} //close if($r)
	mysqli_close($dbc);
?>