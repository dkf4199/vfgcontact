<?php
//echo '<div class="info_msg"><pre>' . print_r($_POST, true) . '</pre></div>'; //return;
if ((include './includes/dynamic_config.inc.php') == false) {
	echo '<div class="error_msg">Failed to initialize, script will end</div>';
	return;
}

/********** Begin Validate Input **********/
$errors = array();

if (empty($_POST['reportName'])) {
	$errors[] = 'Missing Report Name';
} else {
	$reportName = $_POST['reportName'];
}

if (empty($_POST['quarters_text'])) {
	$errors[] = 'Missing Quarters Text';
} else {
	$quarters_text = $_POST['quarters_text'];
}

if (empty($_POST['quarters_beg'])) {
	$errors[] = 'Missing Begin Quarter';
} else {
	$quarters_beg = $_POST['quarters_beg'];
}

if (empty($_POST['quarters_end'])) {
	$errors[] = 'Missing End Quarter';
} else {
	$quarters_end = $_POST['quarters_end'];
}

if (isset($quarters_beg) && isset($quarters_end) && ($quarters_beg > $quarters_end)) {
	$errors[] = 'Begin Quarter Is After End Quarter';
}

if (empty($_POST['facilities_text'])) {
	$errors[] = 'Missing Facility Text';
} else {
	$facilities_text = $_POST['facilities_text'];
}

if (empty($_POST['facilities'])) {
	$errors[] = 'Missing Facility';
} else {
	$facilities = $_POST['facilities'];
}

if (empty($_POST['drg'])) {
	$drg = 'all';
} else {
	$drg = $_POST['drg'];
}

if (empty($_POST['breakout'])) {
	$breakout = 'none';
} else {
	$breakout = $_POST['breakout'];
}

if (!empty($errors)) {
	echo '<div class="error_msg">Errors:<br />- ' . implode('<br />- ', $errors) . '</div>';
	return;
}
/********** End Validate Input **********/

/********** Begin Set Breakout Variables **********/
switch ($breakout) {
	case 'none':
		$breakoutID = "";
		$breakoutField = "";
		$breakoutJoin = "";
		$breakoutHeader = "DRG";
		break;
	case 'gender':
		$breakoutID = ", Gender";
		$breakoutField = ", Case Gender When 'M' Then 'Male' When 'F' Then 'Female' Else 'Unknown' End";
		$breakoutJoin = "";
		$breakoutHeader = "DRG / Gender";
		break;
	case 'agegroup':
		$breakoutID = ", AgeGroupID";
		$breakoutField = ", CONCAT('Ages ', bo.AgeGroup)";
		$breakoutJoin = " INNER JOIN dcr_refagegroups AS bo On d.AgeGroupID = bo.AgeGroupID";
		$breakoutHeader = "DRG / AgeGroup";
		break;
	case 'payer':
		$breakoutID = ", PayerID";
		$breakoutField = ", CONCAT(bo.PayerID, ' - ', bo.Payer)";
		$breakoutJoin = " INNER JOIN dcr_refpayers AS bo ON d.PayerID = bo.PayerID";
		$breakoutHeader = "DRG / Payer";
		break;
	default:
		$breakoutID = "";
		$breakoutField = "";
		$breakoutJoin = "";
		$breakoutHeader = "DRG";
		break;
}
/********** End Set Breakout Variables **********/

/********** Begin Create SQL String **********/
$query  =	" SELECT CONCAT(r.DRGID, ' - ', SUBSTRING_INDEX(GROUP_CONCAT(r.DRG ORDER BY r.High DESC SEPARATOR '|'), '|', 1))" . $breakoutField;
$query .=	", d.Cases, d.LOS, d.LOS / d.Cases, d.Charges, d.Charges / d.Cases, d.Charges / d.LOS";
$query .=	" FROM";
$query .=		" (SELECT DRGID" . $breakoutID . ", SUM(Cases) AS Cases, SUM(LOS) AS LOS, SUM(Charges) AS Charges FROM dcr_data_ab";
$query .=		" WHERE FacilityID = '$facilities' AND YearQuarterPeriod BETWEEN '$quarters_beg' AND '$quarters_end'";
$query .=		($drg == "all") ? "" : " AND DRGID IN ($drg)";
$query .=		" GROUP BY DRGID" . $breakoutID . " HAVING SUM(Cases) >= " . SAMPLE_SIZE . ") AS d";
$query .=	" INNER JOIN dcr_refdrgs AS r ON d.DRGID = r.DRGID AND r.Low <= '$quarters_end' AND r.High >= '$quarters_beg'";
$query .=	$breakoutJoin;
$query .=	" GROUP BY r.DRGID, d.Cases, d.LOS, d.Charges" . $breakoutField;
$query .=	" ORDER BY r.DRGID" . $breakoutField;
//echo $query; //return;
/********** End Create SQL String **********/

/********** Begin Set Output Variables **********/
//$reportName was assigned from POST array

//Report header variables
$title = 'DRG Report for a Facility';
$subject = $facilities_text;
$patient = 'Patient Type: ' . 'Hospital Inpatient';
$period = 'Discharge Period: ' . $quarters_text;
$created = 'Created On: ' . date('n/j/Y g:i a');

//Output table variables
//Output table consists of one breakout column plus data/numeric columns
//The breakout column is the first column of the output table and will be the only description/label column of the row
//Each breakout will be printed below the parent breakout and only the last breakout row will have numbers for the data/numeric columns

//$breakoutHeader is the title of the first column in the output table and was assigned above with the other breakout variables

//Each non-numeric field is considered a breakout, therefore the minimum $breakoutLvls is one
$breakoutLvls = ($breakout == 'none') ? 1 : 2;

//Data/Numeric column names to display in output table
//Make sure this array matches the data/numeric columns of the sql statement ($query)
//The elements in this fields array has to be in the keys of the $dataFormat array
//Note: Index/Key starts with $breakoutLvls which should then match the data columns' indices/keys of the sql result rows
$dataFields = array($breakoutLvls => 'Cases', 'Tot Days', 'Avg Days', 'Tot Charges', 'Avg Charges', 'Avg Daily Charges');

//Number of columns in output table (data/numeric columns plus breakout column)
$tblCols = count($dataFields) + 1;
/********** End Set Output Variables **********/

//Include script to print output
if ((include './includes/dynamic_output_html.inc.php') == false) {
	echo '<div class="error_msg">Failed to print report</div>';
}
?>