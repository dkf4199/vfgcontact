<?php
	function generateRandomString($length = 15){
		return substr(sha1(rand()), 0, $length);
	}
	
	echo generateRandomString();
?>