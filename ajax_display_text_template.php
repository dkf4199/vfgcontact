<?php
session_start();
		
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');	
	//DB Connection
	require_once (MYSQL);
	
	$repsid = $_SESSION['rep_id'];
	
	//$_GET VARS
	if (isset($_GET['tid'])){
		$tid = $_GET['tid'];
	} else {
		$tid = '';
	}
	//$_GET VARS
	if (isset($_GET['cid'])){
		$cid = $_GET['cid'];
	} else {
		$cid = '';
	}
	
	if ($tid != 'blank'){
		// Make the query to get template data:
		$q = "SELECT text_template_name, text_body 
			  FROM vfg_rep_text_templates
			  WHERE rep_id = '$repsid' 
			  AND tt_id = '$tid'
			  LIMIT 1";	
			
		$r = mysqli_query ($dbc, $q); // Run the query.

		if ($r) { // If it ran OK, get the data if there is a record.
			if (mysqli_num_rows($r) == 1){
				// Fetch and print all the records:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					
					$ul_list = '<ul>
									<li>
										<label>Template:</label>
										<span class="readonly_nobox">'.$row['text_template_name'].'</span>
									</li>
									<li>
										<label>Message:</label>
										<textarea id="notes" name="notes" rows="6" cols="40">'.stripslashes($row['text_body']).'</textarea>
									</li>
									<li>
										<label>&nbsp;</label>
										<a href="#" class="savetm" onClick="javascript: return saveTextToHistory(\''.$cid.'\');">Text Sent. Save to History.</a>
									</li>
									<input type="hidden" name="txt_template" id="txt_template" value="'.$row['text_template_name'].'" />
								</ul>';
					echo $ul_list;
				}	//close while
			
			} 	//close num_rows
				
		} //close if($r)
	
	} else {	//blank template
		
		$ul_list = '<ul>
					<li>
						<label>Template:</label>
						<span class="readonly_nobox">Blank Text</span>
					</li>
					<li>
						<label>Message:</label>
						<textarea id="notes" name="notes" rows="6" cols="40"></textarea>
					</li>
					<li>
						<label>&nbsp;</label>
						<a href="#" class="savetm" onClick="javascript: return saveTextToHistory(\''.$cid.'\');">Text Sent. Save to History.</a>
					</li>
					<input type="hidden" name="txt_template" id="txt_template" value="blank" />
				</ul>';
		echo $ul_list;
	}
	mysqli_close($dbc);
?>