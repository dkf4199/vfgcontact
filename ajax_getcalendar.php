<?php
session_start();

	//ajax_getcalendar.php
	//
	$email = $_GET['email'];
	$password = $_GET['password'];
	
	echo '<iframe src="https://www.google.com/calendar/embed?src=dkf4199%40gmail.com&ctz=America/Los_Angeles" 
				style="border: 0" frameborder="0" width="800px" height="600px" scrolling="no"></iframe>';
?>