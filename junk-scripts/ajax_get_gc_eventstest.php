<?php
session_start();

//"From Date" always set - check performed in jquery
$fromdt = $_GET['fromdt'];

//"To Date" can be blank, if it is - increment from date
//by one to get "from date's" events
if (!isset($_GET['todt']) || $_GET['todt'] == ''){
	
	//mktime routine
	$thisday = $fromdt;
	list($y,$m,$d)=explode('-',$thisday);
	$todt = Date("Y-m-d", mktime(0,0,0,$m,$d+1,$y));
	
	//strtotime routine
	#$today=$fromdt;
	#$nextday=strftime("%Y-%m-%d", strtotime("$today +1 day"));
	
} else {
	$todt = $_GET['todt'];
}

/*
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Http_Client');

//$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
$user = $_SESSION['rep_google_email'];
$pass = $_SESSION['rep_google_pass'];
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, 'cl');
$gcal = new Zend_Gdata_Calendar($client);
*/
	
echo 'From Date: '.$fromdt.'<br />To Date: '.$todt.'<br />';
echo 'User: '.$_SESSION['rep_google_email'].'<br />Pass: '.$_SESSION['rep_google_pass'];

?>
    