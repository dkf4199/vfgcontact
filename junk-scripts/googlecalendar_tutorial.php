<?php
date_default_timezone_set('America/Los_Angeles');
$path = 'C:/public_html/eapcrm/Zend';
// Append the library path to existing paths
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
 
// User whose calendars you want to access
$user = 'dkf4199@gmail.com';
$pass = 'deronfrederickson';
$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar
 
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);

$service = new Zend_Gdata_Calendar($client);
 
// Get the calendar list feed - ALL CALENDARS
//
$listFeed = $service->getCalendarListFeed();
 
echo "<h1>Calendar List (All Calendars)</h1>";
echo "<ul>";
foreach ($listFeed as $calendar) {
    echo "<li>" . $calendar->title . " (" . $calendar->id . ")</li>";
}
echo "</ul>";
//**********************************************************************

//RETRIEVE EVENTS FROM CALENDAR
//*****************************
$query = $service->newEventQuery();
// Set different query parameters
$query->setUser('default');	//default calendar
$query->setVisibility('private');
$query->setProjection('full');
$query->setOrderby('starttime');
// Start date from where to get the events
$query->setStartMin('2013-08-01');
// End date
#$query->setStartMax('2013-03-15');
//****** END query parms *************
 
// Get the event list
try {
    $eventFeed = $service->getCalendarEventFeed($query);
} catch (Zend_Gdata_App_Exception $e) {
    echo "Error: " . $e->getMessage();
}

echo '<h3>Pull Events from Calendar</h3>';
echo "<ul>";
foreach ($eventFeed as $event) {
    echo "<li>" . $event->title . " </li>";
}
echo "</ul>";
//***** END event retrieval *****

//********************************
# CREATE SINGLE OCCURENCE EVENT
//********************************
// Create a new event object using calendar service's factory method.
// We will then set different attributes of event in this object.
$event= $service->newEventEntry();
 
// Create a new title instance and set it in the event
$event->title = $service->newTitle("New Single Event");
// Where attribute can have multiple values and hence passing an array of where objects
$event->where = array($service->newWhere("Las Vegas, Nevada"));
$event->content = $service->newContent("Event created in googlecalendar_tutorial.php.");
 
// Create an object of When and set start and end datetime for the event
$when = $service->newWhen();
// Set start and end times in RFC3339 (http://www.ietf.org/rfc/rfc3339.txt)
$when->startTime = "2013-08-05 16:30:00"; // 5th August 2013, 4:30 pm 
// If you want the event to last the whole day then only set
// the date in startTime and skip endTime attribute of “when”
#$when->startTime = "2010-07-08"; // 8th July 2010, Whole day event
$when->endTime = "2013-08-05 17:30:00"; // 5th August 2013, 5:30 pm
// Set the when attribute for the event
$event->when = array($when);
 
// Create the event on google server
$newEvent = $service->insertEvent($event);
// URI of the new event which can be saved locally for later use
$eventUri = $newEvent->id->text;
/***** END single occurance event *********************************************

//RECURRING EVENTS
//*************************
/*
Recurring events are created almost similar to non recurring events. The only difference is 
that instead of “where” attribute we would set “recurrence” attribute for the event. 
Recurrence attribute is a string pattern in iCalendar standard RFC2445.
*/
/*
// This event will occur all day every Friday starting from 9th July 2010 until 8th Dec 2015
$recurrence = "DTSTART;VALUE=DATE:20100709\r\n" .
        "DTEND;VALUE=DATE:20100710\r\n" .
        "RRULE:FREQ=WEEKLY;BYDAY=Fr;UNTIL=20151208\r\n";
 
$event->recurrence = $service->newRecurrence($recurrence);
*/

// UPDATING AN EVENT
//************************
/*
For modifying an event, we first need to get its reference. 
This can be done using the event URI which we got after we 
created the event (see above). Once we get the event reference, 
we can pretty much change any attribute of it (as we did while creating an event)
and then we have to save it.
*/
/*
// URI of the event which we got after creating it.
$eventUri = "http://www.google.com/calendar/feeds/default/private/full/53608ibmrtnb57o7hqf8l1tsu4";
// Get the event
$event = $service->getCalendarEventEntry($eventUri);
// Change the title
$event->title = $service->newTitle("New Title!");
// Save the event
$event->save();
*/

// DELETING AN EVENT
//****************************
/*
Deleting an event is very simple. Just get the event 
reference (as we did for updating) and call the event’s delete method.
*/
/*
// Get the event
$event = $service->getCalendarEventEntry($eventUri);
// Delete the event
$event->delete();
*/
?>