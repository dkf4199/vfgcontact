<?php
include ('includes/gc_AuthSub_calendar_connect.php');
// Make sure that the user has a valid session, so we can record the
// AuthSub session token once it is available AND USE IT ON OTHER SCRIPTS
//************************************************************************
session_start();
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
//Zend_Loader::loadClass('Zend_Http_Client');
// Create an instance of the Calendar service, redirecting the user
// to the AuthSub server if necessary.
$service = new Zend_Gdata_Calendar(getAuthSubHttpClient());

$query = $service->newEventQuery();
$query->setUser('default');
// Set to $query->setVisibility('private-magicCookieValue') if using
// MagicCookie auth
$query->setStartMin('2013-08-01');
$query->setStartMax('2013-08-16');
$query->setUser('default');
$query->setVisibility('private');
$query->setProjection('basic');
$query->setOrderby('starttime');
# comment out futureevents('true') if
# using date range.  It cancels out dates.
//$query->setFutureevents('true');
 
// Retrieve the EVENT from the calendar server
try {
    $eventFeed = $service->getCalendarEventFeed($query);
} catch (Zend_Gdata_App_Exception $e) {
    echo "Error: " . $e->getMessage();
}
 
// Iterate through the list of events, outputting them as an HTML list
echo "<ul>";
foreach ($eventFeed as $event) {
    echo "<li>" . $event->title . " (Event ID: " . $event->id . ")</li>";
}
echo "</ul>";

?>