<?php
/**************************************/
/*  FUNCTIONS FOR EAPCRM.COM  */
/**************************************/

//  ABSOLUTE_URL()
//*********************
function absolute_url ($page = 'index.php'){

	//url is http://+hostname+currentdirectory

	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	//remove trailing slashes

	$url = rtrim($url, '/\\');

	//add page to redirect to
	$url .= '/' . $page;

	return $url;

}  //end absolute_url()

//This function separates the extension from the rest of a file name and returns it 
 function get_file_extension($filename) { 
	 $filename = strtolower($filename); 
	 $exts = explode(".", $filename); 
	 $n = count($exts)-1; 
	 $exts = $exts[$n]; 
	 return $exts; 
 } 
 
//  VALID_FIRSTNAME()
//********************
function valid_firstname($name){

	if (!preg_match("/^[A-Za-z]+$/", trim($name))) {
		return false;
	} else {
		return true;
	}
}

//  VALID_LASTAME()
//*********************
function valid_lastname($name){

	if (!preg_match("/^[A-Za-z -]+$/", trim($name))) {
		return false;
	} else {
		return true;
	}

}

//  VALID_EMAIL()
//**********************
function valid_email($em){

	if (!preg_match("/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/", trim($em))) {
		return false;
	} else {
		return true;
	}

}

//  VALID_PHONE()
//*******************************
//second parm is the part of the phone
//number to validate
function valid_phone($phone, $part){

	if ($part == "area" || $part == "prefix"){
		if (!preg_match("/^[0-9]{3}$/", trim($phone))){
			return false;
		} else {
			return true;
		}
	} elseif ($part == "suffix"){
		if (!preg_match("/^[0-9]{4}$/", trim($phone))){
			return false;
		}else {
			return true;
		}
	}
}

function status_desc($status = ''){
	
	//status vals are V, N, P, B
	$status_desc = '';
	switch ($status) {
		case 'V':
			$status_desc = 'Verified';
			break;
		case 'N':
			$status_desc = 'Not Reachable';
			break;
		case 'P':
			$status_desc = 'Pending';
			break;
		case 'B':
			$status_desc = 'Bad Lead';
			break;
		default:
			$status_desc = 'Unknown';
			break;
	}
	return $status_desc;
}

function mysql_date_to_display($dt){
		$display_dt = substr($dt,5,2).'-'.substr($dt,8,2).'-'.substr($dt,0,4);
		return $display_dt;
}
function display_date_to_mysql($dt){
		$mysql_dt = substr($dt,6,4).'-'.substr($dt,0,2).'-'.substr($dt,3,2);
		return $mysql_dt;
}

/*   REP_LOGIN()
//****************************************************************
/* This function validates the staff login data (the email and password).
 * If both are present, the database is queried.
 * The function requires a database connection.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result
 */
function rep_login($dbc, $vfgid = '', $pass = '') {

	$errors = array(); // Initialize error array.
	
	// Validate the Username:
	if (empty($vfgid)) {
		$errors[] = 'Enter your VFG Rep ID.';
	} else {
		$id = mysqli_real_escape_string($dbc, trim($vfgid));
	}
	
	// Validate the password:
	if (empty($pass)) {
		$errors[] = 'Enter your password.';
	} else {
		$pwd = mysqli_real_escape_string($dbc, trim($pass));
	}

	if (empty($errors)) { // If everything's OK.

		// Retrieve the company_name and uniqueid using login credentials:
		$q = "SELECT b.firstname, b.lastname, b.phone, b.email, 
					 b.rep_timezone, b.rep_id, b.vfgrepid, 
					 b.gmail_acct, b.gmail_pass, b.replevel_manager,
					 b.replevel_consultant, b.replevel_svp, b.unique_id, 
					 b.terms_agreed, b.email_bcc, b.team_stats_id, b.homepage_link
			    FROM rep_login_id a INNER JOIN reps b ON a.vfgid = b.vfgrepid
			   WHERE a.vfgid='$id' AND a.password='$pwd'";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		// Check the result:
		if (mysqli_num_rows($r) == 1) {
		
			// Fetch the record:
			$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
			
			// Return true and the record:
			return array(true, $row);
			
		} else { // Not a match!
			$errors[] = 'The Rep ID and/or password entered do not match those on file.';
		}
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

} // End of rep_login() function.

//*********************
// REP_ENCRYPT_LOGIN
//*********************
function rep_encrypt_login($dbc, $vfgid = '', $pass = '') {

	$errors = array(); // Initialize error array.
	
	// Validate the Username:
	if (empty($vfgid)) {
		$errors[] = 'Enter your VFG Rep ID.';
	} else {
		$id = mysqli_real_escape_string($dbc, trim($vfgid));
	}
	
	// Validate the password:
	if (empty($pass)) {
		$errors[] = 'Enter your password.';
	} else {
		$pwd = mysqli_real_escape_string($dbc, trim($pass));
	}

	// Get password hash or give error
	// Retrieve the encrypted password based on the
	//          vfgid passed to function
	$password_hash = '';
	$p = "SELECT pwd FROM rep_login_id WHERE vfgid = '$id' LIMIT 1";
	$rt = @mysqli_query ($dbc, $p); // Run the query.
	if ($rt){
		if (mysqli_num_rows($rt) == 1) {
			while ($row = mysqli_fetch_array($rt, MYSQLI_ASSOC)) {
				$password_hash = $row['pwd'];
			}
			
		} else { // Cannot get password hash based on vfgid
			$errors[] = "Cannot retrieve data. Make sure your VFGID is correct.";
		}
		
		mysqli_free_result($rt);
	}
	// Hash the supplied password and compare to user supplied value
	$login_hash = crypt($pwd, $password_hash);
	if ($login_hash != $password_hash){
		$errors[] = "Incorrect login. Please check your values and re-enter.\n".$login_hash."\n".$password_hash."\n";
	}
		
	if (empty($errors)) { // If everything's OK.

		
		// Retrieve the company_name and uniqueid using login credentials:
		$q = "SELECT a.pwd, b.firstname, b.lastname, b.phone, b.email, 
					 b.rep_timezone, b.rep_id, b.vfgrepid, 
					 b.gmail_acct, b.gmail_pass, b.replevel_manager,
					 b.replevel_consultant, b.replevel_svp, b.unique_id, 
					 b.terms_agreed, b.email_bcc, b.team_stats_id, b.homepage_link
			    FROM rep_login_id a INNER JOIN reps b ON a.vfgid = b.vfgrepid
			   WHERE a.vfgid='$id' ";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		// Check the result:
		if (mysqli_num_rows($r) == 1) {
		
			// Fetch the record:
			$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
			
			// Return true and the record:
			return array(true, $row);
			
		} else { // Not a match!
			$errors[] = 'The Rep ID and/or password entered do not match those on file.';
		}
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

} // End of rep_login() function.


function tcs_admin_login($dbc, $email = '', $pass = '') {

	$errors = array(); // Initialize error array.
	
	// Validate the Username:
	if (empty($email)) {
		$errors[] = 'Enter email.';
	} else {
		$em = mysqli_real_escape_string($dbc, trim($email));
	}
	
	// Validate the password:
	if (empty($pass)) {
		$errors[] = 'Enter your password.';
	} else {
		$pwd = mysqli_real_escape_string($dbc, trim($pass));
	}

	if (empty($errors)) { // If everything's OK.

		// Retrieve the company_name and uniqueid using login credentials:
		$q = "SELECT firstname, lastname, vfg_repid
			    FROM master_login 
			   WHERE email='$em' AND password='$pwd'";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		// Check the result:
		if (mysqli_num_rows($r) == 1) {
		
			// Fetch the record:
			$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
			
			// Return true and the record:
			return array(true, $row);
			
		} else { // Not a match!
			$errors[] = 'The email and/or password entered do not match those on file.';
		}
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

} // End of tcs_admin_login() function.

/*   REP_FORGOT_PASS()
//****************************************************************
/* This function retrieves agent's password (the email and password).
 * If both are present, the database is queried.
 * The function requires a database connection.
 * The function returns an array of information, including:
 * - a TRUE/FALSE variable indicating success
 * - an array of either errors or the database result
 */
function rep_forgot_pass($dbc, $email = '', $vid = '') {

	$errors = array(); // Initialize error array.
	$repid = '';
	
	// Validate the Username:
	if (empty($email)) {
		$errors[] = 'Enter your Gmail on record.';
	} else {
		$em = mysqli_real_escape_string($dbc, trim($email));
		/*list($addr, $box) = explode('@',$em);
		if ($box != 'gmail.com'){
			$errors[] = 'Gmail account entered is not a gmail address.';
		}*/
	}
	
	// Validate the vfgid:
	if (empty($vid)) {
		$errors[] = 'Enter your VFG ID.';
	} else {
		$vfgid = mysqli_real_escape_string($dbc, strtoupper(trim($vid)));
		
		//Is this the rep's EMAIL AND VFG ID on record?
		$query = "SELECT rep_id, vfgrepid FROM reps 
				  WHERE gmail_acct = '$em' 
				  AND vfgrepid = '$vfgid' LIMIT 1";
		//RUN QUERY
		$rs = @mysqli_query ($dbc, $query);
		if (mysqli_num_rows($rs) != 1) {
			//vfgid is not the one on reps record
			$errors[] = 'Gmail and/or VFG Rep ID does not match your record.';
		} else {
			while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
				$repid = $row['rep_id'];
			}
		}
		mysqli_free_result($rs);
				
	}

	if (empty($errors)) { // If everything's OK.

		// Retrieve the company_name and uniqueid using login credentials:
		$q = "SELECT password 
			    FROM rep_login_id
			   WHERE rep_id='$repid'";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		// Check the result:
		if (mysqli_num_rows($r) == 1) {
		
			// Fetch the record:
			$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
			
			// Return true and the record:
			return array(true, $row);
			
		} 
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

} // End of rep_get_password() function.

// IMAGE_SPECS()
//**************************************
//function takes the image name and returns
//the true width and height specs for the image
//from the images/ directory
function image_specs($imagename){

	// images directory path
	$dir = './images';
			
	$imagewh = getimagesize("$dir/$imagename");

	return $imagewh;

} //close image_specs()

/* function getStandardTZname(reptz):
	Takes 1 argument and returns the standard designation
	for timezones. Eastern, Mountain, Pacific, Central..etc
*/
function getStandardTZname($tzone){
	$timeZones = array(
	  'America/New_York'=>'Eastern', 
	  'America/Chicago'=>'Central', 
	  'America/Boise'=>'Mountain', 
	  'America/Phoenix'=>'Arizona Mountain', 
	  'America/Los_Angeles'=>'Pacific', 
	  'America/Juneau'=>'Alaska', 
	  'Pacific/Honolulu'=>'Hawaii' 
	);

	$standard_tz = '';
	foreach($timeZones as $id=>$name ){
		if ($tzone == $id){
			$standard_tz = $name;
		}
	}
	return $standard_tz;
}
//**********************************************************************
/* The function takes one argument: a string.
* The function returns a clean version of the string.
* The clean version may be either an empty string or
* just the removal of all newline characters.
*/
function spam_scrubber($value) {

	// List of very bad values:
	$very_bad = array('to:', 
					  'cc:', 
					  'bcc:', 
					  'content-type:', 
					  'mime-version:', 
					  'multipart-mixed:', 
					  'content-transfer-encoding:');
		
	// If any of the very bad strings are in 
	// the submitted value, return an empty string:
	foreach ($very_bad as $v) {
		if (stripos($value, $v) !== false) return '';
	}
		
	// Replace any newline characters with spaces:
	$value = str_replace(array( "\r", "\n", "%0a", "%0d"), ' ', $value);
		
	// Return the value:
	return trim($value);
	
} // End of spam_scrubber() function.

//Get the GLOBAL TEMPLATE ID and create the html body from that
function getGlobalTemplate($global_tid, $link){

	$str = '<html><body><p>';
	//test
	//$imagepath = './images/';
	//prod
	$imagepath = 'http://www.vfgcontact.com/images/';
	
	switch ($global_tid) {
		case '1':
			$str .= '<a href="http://'.$link.'" target="_blank"><img src="'.$imagepath.'VFGAgentIntro.jpg"></a>';
			break;
		case '2':
			$str .= '<a href="http://'.$link.'" target="_blank"><img src="'.$imagepath.'VFGIncompleteIntro.jpg"></a>';
			break;
		case '3':
			$str .= '<a href="http://'.$link.'" target="_blank"><img src="'.$imagepath.'VFGREIntro1.jpg"></a>';
			break;
		case '4':
			$str .= '<a href="http://'.$link.'" target="_blank"><img src="'.$imagepath.'VFGREIntro2.jpg"></a>';
			break;
		case '5':
			$str .= '<a href="http://'.$link.'" target="_blank"><img src="'.$imagepath.'VFGThanksInterest.jpg"></a>';
			break;
		default:
			$str .= '<a href="http://'.$link.'" target="_blank"><img src="'.$imagepath.'VFGAgentIntro.jpg"></a>';
			break;
	}
	$str .= '</p></body></html>';
	return $str;
}
// Original PHP code by Chirp Internet: www.chirp.com.au
// Please acknowledge use of this code by including this header.
//
// FOR HIGH SECURITY APPS: 
//  increase the rounds; 
//  use a more random salt generator; 
//  or generate a hash using multiple hashing mechanisms in sequence
//
function better_crypt($input, $rounds = 7) {
	$salt = "";
	$salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
	for($i=0; $i < 22; $i++) {
		$salt .= $salt_chars[array_rand($salt_chars)];
	}
	// use $2y$ vs $2a$ for php 5.5 and higher
	return crypt($input, sprintf('$2y$%02d$', $rounds) . $salt);
}
?>