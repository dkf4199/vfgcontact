<?php
date_default_timezone_set ('America/Los_Angeles');
$pacific = date("Y-m-d H:i:s"); 

date_default_timezone_set ('America/Chicago');
$central = date("Y-m-d H:i:s");

echo $pacific."\n";
echo $central."\n";    

?>