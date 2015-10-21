<?php
include ('includes/selectlists.php');

$tier = '1';
$str = '$str = '.($tier == '2' ? 'checked' : 'not checked');
echo $str;

$thisarray = array();
$thisarray = $tier4steps;

foreach($thisarray as $id=>$name){
	echo $id.' --> '.$name.'<br />';
}
//assign
?>