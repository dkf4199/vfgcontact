<?php
session_start();
	
	$repid = $_SESSION['rep_id'];
	$currcid = $_GET['cid'];
	
	include ('includes/config.inc.php');
	
	//DB Connection
	require_once (MYSQL);
	
	
	//COMM MATRIX AND NOTES - TEMP TABLE CHRONOLOGICAL EXTRACT
	$comm_notes = '';
	$msg_type = '';
	/*$commnotes_sql = "SELECT rep_id, contact_id,' ' as 'repfirst', ' ' as 'replast', 
									 comm_type as 'type', comm_note as 'commnote', comm_date as 'entrydate'
						FROM communication_matrix
						WHERE rep_id = '$repid'
						AND contact_id = '$currcid'
						union
						SELECT rep_id, contact_id, rep_first as 'repfirst', rep_last as 'rep_last',
							  'NOTE' as 'type', note as 'commnote', note_date as 'entrydate'
						FROM notes
						WHERE rep_id = '$repid'
						AND contact_id = '$currcid'

						ORDER BY entrydate DESC ";*/
	$commnotes_sql = "SELECT rep_id, contact_id,' ' as 'repfirst', ' ' as 'replast', 
									 comm_type as 'type', comm_note as 'commnote', comm_date as 'entrydate'
								FROM communication_matrix
								WHERE contact_id = '$currcid'
								union
								SELECT rep_id, contact_id, rep_first as 'repfirst', rep_last as 'rep_last',
									  'NOTE' as 'type', note as 'commnote', note_date as 'entrydate'
								FROM notes
								WHERE contact_id = '$currcid'

								ORDER BY entrydate DESC ";
	$cn = mysqli_query($dbc, $commnotes_sql);
	if ($cn){
		if (mysqli_num_rows($cn) > 0){
			while ($row = mysqli_fetch_array($cn, MYSQLI_ASSOC)) {
				
				switch ($row['type']){
					case 'EM':
						$type = 'EMAIL';
						break;
					case 'PC':
						$type = 'PHONE CALL';
						break;
					case 'TM':
						$type = 'TEXT MSG';
						break;
					case 'NOTE':
						$type = 'NOTE ENTRY';
						break;
				}
				//Format note_date
				$commnotedt = strtotime( $row['entrydate'] );
				$formatted_commnotedt = date( 'm-d-Y h:i:s a', $commnotedt );
				$comm_notes .= $formatted_commnotedt.' '.$type."\n";
				if ( $row['repfirst'] != ' ' ){
					$comm_notes .= 'By: '.$row['repfirst'].' '.$row['replast']."\n";
				}
				$comm_notes .= $row['commnote']."\n\n";
			}
		}
		mysqli_free_result($cn);
	}
	
	echo stripslashes($comm_notes);
	
	mysqli_close($dbc);
	
?>