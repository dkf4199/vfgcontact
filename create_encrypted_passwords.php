<?php
	include ('./includes/selectlists.php');
	include ('./includes/config.inc.php');

	//DB Connection
	require_once (MYSQL);
	
	$rep_ids = array();	
	
	// Get list of all entries in the rep_login_id table
	//pull data for display
	$q = "SELECT rep_id, password FROM rep_login_id";
	$rs = mysqli_query ($dbc, $q);
	if ($rs){
		if (mysqli_num_rows($rs) >= 1) {
			while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
				$rep_ids[] = $row['rep_id'].':'.$row['password'];
			}
			
		}
		mysqli_free_result($rs);
	}
		
	// Debug - Display rep ids
	if (!empty($rep_ids)) {
		foreach ($rep_ids as $rid) {
			$id_pwd = explode(":", $rid);
			echo $id_pwd[0].' ==> '.$id_pwd[1]."<br />\n";
		}
	}
	
	// PREPARED STATEMENT
	//prepared statement - INSERT DATA into reps
	$q = "UPDATE rep_login_id
			SET pwd = ?
		  WHERE rep_id = ?";
					
	// LOOP THRU ARRAY, CREATE BCRYPT PASSWORD AND UPDATE REP_LOGIN_ID
	if (!empty($rep_ids)) {
		foreach ($rep_ids as $rid) {
			$id_pwd = explode(":", $rid);
			
			$password_hash = better_crypt($id_pwd[1]);	// better_crypt - blowfish
			$repid = $id_pwd[0];
			
			$stmt = mysqli_prepare($dbc, $q);
			//Do update
			$stmt->bind_param('ss',$password_hash, $repid);
			$stmt->execute();
			if (mysqli_stmt_affected_rows($stmt) == 1) {	//rep data insert successful
				mysqli_stmt_close($stmt);
			}
		
		}
	}
	
	//CLOSE connection
	mysqli_close($dbc);
	
	
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