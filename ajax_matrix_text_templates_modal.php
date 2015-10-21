<?php
session_start();
	$repsid = $_SESSION['rep_id'];	
	$currcid = $_GET['currentid'];
		
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	$tt1 = $tt2 = $tt3 = $tt4 = $tt5 = '';
	// Make the query:
	$q = "SELECT text_template1, text_template2, text_template3, text_template4, text_template5
		  FROM vfg_rep_text_templates
		  WHERE rep_id = '$repsid' LIMIT 1";		
	$r = mysqli_query ($dbc, $q); // Run the query.
	
	if ($r) { // If it ran OK, display the record.
	  
		if (mysqli_num_rows($r) == 1){
			// Fetch the templates:
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				$tt1 = $row['text_template1'];
				$tt2 = $row['text_template2'];
				$tt3 = $row['text_template3'];
				$tt4 = $row['text_template4'];
				$tt5 = $row['text_template5'];
			}
		}
		mysqli_free_result($r);
	} 
			
	$displayform = "<div style='display:none'>
		<div class='contact-content'>
		<h1 class='contact-title'>Text Templates</h1>
		<div class='contact-loading' style='display:none'></div>
		<div class='contact-message' style='display:none'></div>";
		
	$displayform .= '<p>Create/save text templates that you can cut and paste into your remote dialer software.<br />
						If you are looking for the Remote Phone Call for Android application, 
						<a href="http://www.justremotephone.com" target="_blank">click here.</a></p>
					<p>
						<label>Text 1:</label>
						<textarea class="text_template_1" id="textmsg1" cols="40" rows="3">'.$tt1.'</textarea>
					</p>
					<p>
						<label>Text 2:</label>
						<textarea class="text_template_2" id="textmsg2" cols="40" rows="3">'.$tt2.'</textarea>
					</p>
					<p>
						<label>Text 3:</label>
						<textarea class="text_template_3" id="textmsg3" cols="40" rows="3">'.$tt3.'</textarea>
					</p>
					<p>
						<label>Text 4:</label>
						<textarea class="text_template_4" id="textmsg4" cols="40" rows="3">'.$tt4.'</textarea>
					</p>
					<p>
						<label>Text 5:</label>
						<textarea class="text_template_5" id="textmsg5" cols="40" rows="3">'.$tt5.'</textarea>
					</p>
					<p align="center">
						<a href="#" class="texttemplatesavelink">Save Templates</a>&nbsp;&nbsp;
						<a href="'.$currcid.'" class="texttemplatecloselink">Close</a><br />
						<div id="text_template_insert"></div>
					</p>
					<input type="hidden" name="cid" id="txtcid" value="'.$currcid.'" />
			 </div>
			</div>';
	echo $displayform;
			
	unset($_GET);
	mysqli_close($dbc); // Close the database connection.		
?>