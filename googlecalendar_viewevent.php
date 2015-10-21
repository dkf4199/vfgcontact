<!DOCTYPE html>
<html>
<head>
<meta  http-equiv="Content-Type" content="text/html; charset=utf-8" />
<META NAME="copyright" CONTENT="&copy; 2013 Deron Frederickson">
<META NAME="robots" CONTENT="index,follow"> 
<META HTTP-EQUIV="cache-control" CONTENT="no-cache">
<META HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta name="revisit-after" content="30 days" />
    <title>Listing calendar contents</title>
    <style>
    body {
      font-family: Verdana;      
    }
    li {
      border-bottom: solid black 1px;      
      margin: 10px; 
      padding: 2px; 
      width: auto;
      padding-bottom: 20px;
    }
    h2 {
      color: red; 
      text-decoration: none;  
    }
    span.attr {
      font-weight: bolder;  
    }
    </style>    
  </head>
  <body>
    <?php
    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');
    Zend_Loader::loadClass('Zend_Http_Client');
    
    $gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
    $user = "dkf4199@gmail.com";
    $pass = "deronfrederickson";
    $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
    $gcal = new Zend_Gdata_Calendar($client);
    
    $query = $gcal->newEventQuery();
	//date range
	$query->setStartMin('2014-01-01');
	$query->setStartMax('2014-05-31');
    $query->setUser('default');
    $query->setVisibility('private');
    $query->setProjection('full');
    $query->setOrderby('starttime');
    if(isset($_GET['q'])) {
      $query->setQuery($_GET['q']);      
    }
    
    try {
      $feed = $gcal->getCalendarEventFeed($query);
    } catch (Zend_Gdata_App_Exception $e) {
      echo "Error: " . $e->getResponse();
    }
    ?>
    <h1><?php echo $feed->title; ?></h1>
    <?php echo $feed->totalResults; ?> event(s) found.
    <p/>
    <ol>

    <?php        
    foreach ($feed as $event) {
      echo "<li>\n";
      echo "<h2>" . stripslashes($event->title) . "</h2>\n";
      echo stripslashes($event->summary) . " <br/>\n";
      $id = substr($event->id, strrpos($event->id, '/')+1);
      echo "<a href=\"googlecalendar_editevent.php?id=$id\">edit</a> | ";
      echo "<a href=\"googlecalendar_deleteevent.php?id=$id\">delete</a> <br/>\n";
      echo "</li>\n";
    }
    echo "</ul>";
    ?>
    </ol>
    <p/>
    <a href="googlecalendar_addevent.php">Add a new event</a><p/>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
      Search for events containing:<br/>
      <input type="text" name="q" size="10"/><p/>
      <input type="submit" name="submit" value="Search"/>
    </form>
  </body>
</html>    
