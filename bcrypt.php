<?PHP
	if(defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
		echo "CRYPT_BLOWFISH is enabled!<br /><br />";
	} else {
		echo "CRYPT_BLOWFISH is NOT enabled!<br /><br />";
	}
	
	$password = 'shamus4199';
	#$password_hash = crypt($password);			// native crypt function
	$password_hash = better_crypt($password);	// better_crypt - blowfish
	echo '<p>Created Password Hash: '.$password_hash.'</p>';
	
	//simulate login - $login_pass would be from the login form page
	$login_pass = 'shamus4199';
	//$login_hash = crypt($login_pass, $password_hash);
	$login_hash = crypt($login_pass, $password_hash);
	echo '<p>Simulated Password Hash: '.$login_hash.'</p>';
	
	if ($login_hash == $password_hash){
		echo '<p>Login successful.</p>';
	} else {
		echo '<p>Login failed.</p>';
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