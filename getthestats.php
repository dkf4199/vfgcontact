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
echo 'Rep Info:  '.$contacts.'<br /><br />'."\n";

//******************************************
// REP INFO:
//******************************************
$q = "SELECT a.rep_id, a.firstname, a.lastname, a.vfgrepid, b.password 
      FROM reps a INNER JOIN rep_login_id b ON a.rep_id = b.rep_id
	  ORDER BY a.lastname DESC";
$r = mysqli_query($dbc, $q);

if ($r) { 
	echo '<table width="1000px">
		<tr>
		 <th>Rep ID</th>
		 <th>VFG ID</th>
		 <th>Lastname</th>
		 <th>Firstname</th>
		 <th>Password</th>
		</tr>';
		 
	// Ran OK
	// Fetch and print all the records:
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr>';
		echo '<td>'.$row['rep_id'].'</td>';
		echo '<td>'.$row['vfgrepid'].'</td>';
		echo '<td>'.$row['lastname'].'</td>';
		echo '<td>'.$row['firstname'].'</td>';
		echo '<td>'.$row['password'].'</td>';
		echo '</tr>';
	}
}

mysqli_close($dbc);

?>