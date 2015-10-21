<?php
$monthNames = Array("January", "February", "March", "April", "May", "June", "July", 
"August", "September", "October", "November", "December");

/*if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("n");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
*/

$cMonth = isset($_REQUEST["month"]) ? $_REQUEST["month"] : date("n");
$cYear = isset($_REQUEST["year"]) ? $_REQUEST["year"] : date("Y");
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {	//Going backwards.....subtract 1 year and set to December
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {	//January of Next year....add 1 to cYear
    $next_month = 1;
    $next_year = $cYear + 1;
}
?>
<html>
<head>
</head>
<body>
<table width="500">
<tr align="center">
	<td bgcolor="#999999" style="color:#FFFFFF">
		<!-- Previous and Next header -->
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="50%" align="left">  <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". $prev_month . "&year=" . $prev_year; ?>" style="color:#FFFFFF">Previous</a></td>
				<td width="50%" align="right"><a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". $next_month . "&year=" . $next_year; ?>" style="color:#FFFFFF">Next</a>  </td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td align="center">
		<table width="100%" border="0" cellpadding="2" cellspacing="2">
			<!-- Row with Month Year Title -->
			<tr align="center">
				<!-- monthNames is zero-based subtract 1 to get correct month name -->
				<td colspan="7" bgcolor="#999999" style="color:#FFFFFF"><strong><?php echo $monthNames[$cMonth-1].' '.$cYear; ?></strong></td>
			</tr>
			<!-- Row with Day of Week Titles -->
			<tr>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>S</strong></td>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>M</strong></td>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>T</strong></td>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>W</strong></td>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>T</strong></td>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>F</strong></td>
				<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>S</strong></td>
			</tr>

<?php 
$timestamp = mktime(0,0,0,$cMonth,1,$cYear);
$maxday = date("t",$timestamp);
$thismonth = getdate ($timestamp);
$startday = $thismonth['wday'];
for ($i=0; $i<($maxday+$startday); $i++) {
    if(($i % 7) == 0 ) echo "<tr>\n";
    if($i < $startday) {
		echo "<td></td>\n";
	} else {
		echo "<td align='center' valign='middle' height='20px'>". ($i - $startday + 1) . "</td>\n";
	}
    if(($i % 7) == 6 ) echo "</tr>\n";
}
?>

</table>
</td>
</tr>
</table>

</body>
</html>