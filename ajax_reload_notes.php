<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	$currcid = $_GET['cid'];
	
	include ('includes/config.inc.php');
	
	//DB Connection
	require_once (MYSQL);
	
	
	// CHRONOLOGICAL NOTES FOR CONTACT from notes table
	$note_arr = array();
	$note_str = '';
	$notes_sql = "SELECT rep_first, rep_last, note, note_date
		  FROM notes
		  WHERE contact_id='$currcid'
		  ORDER BY note_date DESC ";
	$nr = mysqli_query($dbc, $notes_sql);
	if ($nr){
		if (mysqli_num_rows($nr) > 0){
			while ($row = mysqli_fetch_array($nr, MYSQLI_ASSOC)) {
				//Format note_date
				$notedt = strtotime( $row['note_date'] );
				$formatted_notedt = date( 'm-d-Y h:i:s a', $notedt );
				$note_str .= $formatted_notedt."\n".'By: '.$row['rep_first'].' '.$row['rep_last']."\n".$row['note']."\n\n";
			}
		}
		mysqli_free_result($nr);
	}
	
	echo stripslashes($note_str);
	
	mysqli_close($dbc);
	
?>