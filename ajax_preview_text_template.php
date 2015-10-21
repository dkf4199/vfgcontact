<?php
session_start();
	$repid = $_SESSION['rep_id'];
	$repfname = $_SESSION['rep_firstname'];
	$replname = $_SESSION['rep_lastname'];
	$repgmail = $_SESSION['rep_gmail'];
	
	$templateid = $_GET['tid'];
	$currcid = $_GET['cid'];
	$fname = $_GET['fname'];
	
	include ('includes/config.inc.php');
	include ('includes/phpfunctions.php');
	include ('includes/selectlists.php');
	//DB Connection
	require_once (MYSQL);
	
	
	if ($templateid == 'blank'){
	
		$displaystr = '<h4>Create An Email To Send</h4>
				<p>
					<label for="blank_subject">Subject:</label>
					<input type="text" id="blank_subject" name="blank_subject" class="input300"  value="" />
				</p>
				<p>
					<label for="blank_body">Body:</label>
					<textarea id="blank_body" name="blank_body" rows="6" cols="70"></textarea>
				</p>
				<p align="center">
					<a href="#" class="sendlink_blank" onClick="javascript: return sendBlankEmail(\''.$currcid.'\'); ">Send This Email To '.$fname.'</a>	
				</p>
			</div>
			<div class="cleardiv"></div>';
			
	} else {
		
		//GET template data
		$q = "SELECT t_id, template_name, subject, salutation, body,
					img_link, closing 
			  FROM vfg_customemail_settings
			  WHERE rep_id = '$repid' 
			  AND t_id = '$templateid'
			  LIMIT 1";	
			
		$r = mysqli_query ($dbc, $q); // Run the query.

		$sal = $close = '';
		$templatename = $subject = $salutation = $body = $ilink = $closing = '';
		if ($r) { // If it ran OK, get the data if there is a record.
			if (mysqli_num_rows($r) == 1){
				// Fetch and print all the records:
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					$templatename = $row['template_name'];
					$subject = $row['subject'];
					$salutation = $row['salutation'];
					$body = $row['body'];
					$ilink = $row['img_link'];
					$closing = $row['closing'];
				}
				
				switch ($salutation){
					case '1':
						$sal = "Dear Friend,";
						break;
					case '2':
						$sal = "Dear ".$fname.",";
						break;
					case '3':
						$sal = "Greetings Friend,";
						break;
					case '4':
						$sal = "Greetings ".$fname.",";
						break;
					default:
						$sal = "Dear Friend,";
						break;
				}
				
				switch ($closing){
					case '1':
						$close = "Sincerely,";
						break;
					case '2':
						$close = "Regards,";
						break;
					case '3':
						$close = "Best Regards,";
						break;
					case '4':
						$close = "Respectfully,";
						break;
					case '5':
						$close = "Thank You,";
						break;
					default:
						$close = "Dear Friend,";
						break;
				}
			}
		}
		$bodywrapped = wordwrap($body, 70);
		
		$displaystr = '<h4>'.$templatename.'</h4>
						<p>Subject: '.$subject.'</p>
						<p>'.$sal.'<br /><br />'.$bodywrapped.'</p>
						<p>'.$close.'<br /><br />'.$repfname.' '.$replname.'<br />'.$repgmail.'<br />
						<a href="">'.$ilink.'</a></p>';
	}
	mysqli_close($dbc);
	
	echo $displaystr;
	
?>