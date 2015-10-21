<?php
include ('./includes/config.inc.php');

//DB Connection
require_once (MYSQL);

//*************************************
// TOTAL REPS
//*************************************
$q = "SELECT count(rep_id) FROM reps";
$r = mysqli_query($dbc, $q);

$row = @mysqli_fetch_array($r, MYSQLI_NUM);
$records = $row[0];
mysqli_free_result($r);
echo 'Total Reps Signed Up:  '.$records.'<br /><br />'."\n";

//*************************************
// TOTAL CONTACTS
//*************************************
$q = "SELECT count(contact_id) FROM contacts";
$r = mysqli_query($dbc, $q);

$row = @mysqli_fetch_array($r, MYSQLI_NUM);
$contacts = $row[0];
mysqli_free_result($r);
echo 'Total Contacts:  '.$contacts.'<br /><br />'."\n";

//*************************************
// DUMPED CONTACTS
//*************************************
$q = "SELECT count(contact_id) FROM dumped_contacts";
$r = mysqli_query($dbc, $q);

$row = @mysqli_fetch_array($r, MYSQLI_NUM);
$dump_contacts = $row[0];
mysqli_free_result($r);
echo 'Dumped Contacts:  '.$dump_contacts.'<br /><br />'."\n";

//*************************************
// FLUSHED CONTACTS
//*************************************
$q = "SELECT count(contact_id) FROM contacts_archive";
$r = mysqli_query($dbc, $q);

$row = @mysqli_fetch_array($r, MYSQLI_NUM);
$flushed_contacts = $row[0];
mysqli_free_result($r);
echo 'Flushed Contacts:  '.$flushed_contacts.'<br /><br />'."\n";

//******************************************
// REP INFO:
//******************************************
$q = "SELECT rep_id, firstname, lastname, email, rep_timezone, signup_date 
      FROM reps
	  ORDER BY signup_date DESC";
$r = mysqli_query($dbc, $q);

if ($r) { 
	// Ran OK
	// Fetch and print all the records:
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<br />REP ID:  '.$row['rep_id']."\n";
		echo '<br />First Name  :  '.$row['firstname']."\n";
		echo '<br />Last Name   :  '.$row['lastname']."\n";
		echo '<br />Email       :  '.$row['email']."\n";
		echo '<br />Time Zone   :  '.$row['rep_timezone']."\n";
		echo '<br />Signup Date :  '.$row['signup_date']."\n";
		echo "<br />************************************<br />\n";
	}
}

mysqli_close($dbc);

?>