<?php
// optionally
//set_include_path(get_include_path() . PATH_SEPARATOR . '/includes/');
require_once ('Zend/Mail.php');
require_once ('Zend/Mail/Transport/Sendmail.php');

$toemail = 'deronfrederickson@gmail.com';
$repgmail = 'dkf4199@gmail.com';
$repgpass = 'deronfrederickson';

$tr = new Zend_Mail_Transport_Sendmail('smtp.gmail.com', array(
	'auth' => 'login',
	'username' => $repgmail,
	'password' => $repgpass,
	'ssl' => 'ssl',
	'port' => 465)
);
Zend_Mail::setDefaultTransport($tr);

$mail = new Zend_Mail();
$mail->setBodyHtml('<b>This is a test email to see if this works.<br />It is sent using the gmail SMTP servers.</b>');
$mail->addTo($toemail);
$mail->setSubject('Test GMail From VFGCONTACT via PHP');
$mail->setFrom($repgmail);
//
try {
    $mail->send();
    echo "Message sent to dkf4199.<br />\n";
} catch (Exception $ex) {
    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
}


//********************************************************************
// SMTP STUFF
//********************************************************************
/*
require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';

$config    = array(//'ssl' => 'tls',
                   'port' => '25', //465',
                   'auth' => 'login',
                   'username' => 'user',
                   'password' => 'password');

$transport = new Zend_Mail_Transport_Smtp('smtp.example.com', $config);

$mail = new Zend_Mail();
$mail->addTo('user@domain')
     ->setSubject('Mail Test')
     ->setBodyText("Hello,\nThis is a Zend Mail message...\n")
     ->setFrom('sender@domain');

try {
    $mail->send($transport);
    echo "Message sent!<br />\n";
} catch (Exception $ex) {
    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
}
*/
?>