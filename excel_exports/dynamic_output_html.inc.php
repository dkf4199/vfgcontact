<?php
//Any variables with a name that begins with "$excel" in this script is used for creating Excel file
//Excel data & info (for formating reasons) will be stored as session variables and be used when user chooses to export report output to Excel
//There will not be any more commenting relating to Excel in this script because this is a temporary solution
//The use of sessions for this purpose may have potential problems with memory and/or session variable expiration
//A permanant solution will involve sending the report output from the client directly to excel script using javascript
session_start();
$excelID = uniqid('excel_');
$excelURI = './dynamic_reports/includes/dynamic_output_xlsx.inc.php?excelID=' . $excelID;
$_SESSION[$excelID] = array();
$excelData = array();
$excelInfo = array('reportName' => $reportName);
$excelRow = 1;
$excelCol = 'A';
$excelColLast = chr(ord('A') + $tblCols - 1); //This only works if $tblCols is between 1 and 26

//The elements in the $dataFields array has to be in the keys of this format array
$dataFormat = array(
	'Cases' => array('pre' => '', 'dec' => 0),
	'Tot Service Units' => array('pre' => '', 'dec' => 0),
	'Tot Days' => array('pre' => '', 'dec' => 0),
	'Avg Days' => array('pre' => '', 'dec' => 2),
	'Tot Charges' => array('pre' => '$', 'dec' => 0),
	'Avg Charges' => array('pre' => '$', 'dec' => 0),
	'Avg Daily Charges' => array('pre' => '$', 'dec' => 0)
);

//Print report headers
echo '<div id="rpt_hdr_' . $reportName . '" class="rpt_hdr">';
echo '<h2>' . $title . '</h2>';
echo '<h3>' . $patient . '</h3>';
echo '<h3>' . $period . '</h3>';
echo '<h4>' . $created . '</h4>';
echo '</div>';

$excelInfo['title'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = $title; $excelRow++;
$excelInfo['patient'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = $patient; $excelRow++;
$excelInfo['period'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = $period; $excelRow++;
$excelInfo['created'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = $created; $excelRow++;

include DB_CONNECTION;

if (isset($mysqli)) {
	if (!empty($tempTables)) {
		//Create temporary tables when needed
		foreach ($tempTables as $tmpTbl) {
			if (createTempTable($mysqli, $tmpTbl['tbl'], $tmpTbl['def'], $tmpTbl['ins']) !== true) {
				echo '<div class="error_msg">Report cannot be created</div>';
				$mysqli->close();
				unset($mysqli);
				return;
			}
		}
	}
	
	if ($result = $mysqli->query($query)) {
		echo '<div class="section_rpt_tbl">';
		
		echo '<iframe class="hide" name="rpt_export" src="about:blank"></iframe>';
		echo '<a id="xlsx_export" href="' . $excelURI .'" target="rpt_export">Export To Excel</a>';
		
		//Begin print output table
		echo '<table id="rpt_tbl_' . $reportName . '" class="rpt_tbl">';
		
		//Print caption (subject)
		echo '<caption>' . $subject . '</caption>';
		$excelRow++; $excelInfo['subject'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = $subject;
		
		//Print header row in output table
		echo '<thead><tr><th>' . $breakoutHeader . '</th>';
		$excelRow++; $excelInfo['tblHeader'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = $breakoutHeader;
		foreach ($dataFields as $fld) {
			echo '<th>' . $fld . '</th>';
			$excelCol++; $excelData[$excelRow][$excelCol] = $fld;
		}
		echo '</tr></thead>';
		
		//Print result rows in output table
		if ($result->num_rows > 0) {
			if ($breakoutLvls == 1) {
				//No breakout ($breakoutLvls == 1)
				echo '<tbody>';
				while ($row = $result->fetch_row()) {
					//Print first breakout level description/label
					echo '<tr><td>' . $row[0] . '</td>';
					$excelRow++; $excelCol = 'A';
					$excelData[$excelRow][$excelCol] = $row[0];
					//Print data/numeric coumns by looping through the $dataFields array
					//Make sure $dataFields matches data/numeric columns of $row, including the indices/keys
					foreach ($dataFields as $key => $fld) {
						echo '<td>' . $dataFormat[$fld]['pre'] . number_format($row[$key], $dataFormat[$fld]['dec']) . '</td>';
						$excelCol++; $excelData[$excelRow][$excelCol] = $row[$key];
					}
					echo '</tr>';
				}
				echo '</tbody>';
			} elseif ($breakoutLvls == 2) {
				//One breakout ($breakoutLvls == 2)
				//The rows of each breakout will be in their breakout group tbody
				$prev = ''; //Stores the first breakout level text of previous row
				$excelInfo['breakout'] = '';
				while ($row = $result->fetch_row()) {
					//Print when the first breakout level text changes from previous row (new breakout group)
					if ($row[0] != $prev) {
						//Print closing tbody tag for previous breakout group
						echo empty($prev) ? '' : '</tbody>';
						
						//Print empty row to separate breakouts with class name of "empty_row"
						echo '<tr class="empty_row"><td colspan="' . $tblCols . '"></td></tr>';
						
						//Print opening tbody tag with class name of breakout_rows
						echo '<tbody class="breakout_rows">';
						
						//Print first breakout level description/label
						echo '<tr><td colspan="' . $tblCols . '">' . $row[0] . '</td></tr>';
						$excelRow += 2; $excelCol = 'A';
						$excelInfo['breakout'] .= ",$excelCol$excelRow"; $excelData[$excelRow][$excelCol] = $row[0];
					}
					
					//Print second breakout level description/label
					echo '<tr><td>' . $row[1] . '</td>';
					$excelRow ++; $excelCol = 'A';
					$excelData[$excelRow][$excelCol] = $row[1];
					//Print data/numeric coumns by looping through the $dataFields array
					//Make sure $dataFields matches data/numeric columns of $row, including the indices/keys
					foreach ($dataFields as $key => $fld) {
						echo '<td>' . $dataFormat[$fld]['pre'] . number_format($row[$key], $dataFormat[$fld]['dec']) . '</td>';
						$excelCol++; $excelData[$excelRow][$excelCol] = $row[$key];
					}
					echo '</tr>';
					
					//Store the first breakout level text for comparison with next row
					$prev = $row[0];
				}
				echo '</tbody>'; //Print last closing tbody tag
			}
		} else {
			//Print message when there are no rows returned
			echo '<tbody><tr><td colspan="' . $tblCols . '">';
			echo 'There are no records that meets the sample size requirement of ';
			echo '<strong>"' . SAMPLE_SIZE . '"</strong> with the chosen breakout.';
			echo '</td></tr></tbody>';
			
			$excelRow++; $excelCol = 'A';
			$excelInfo['no_recs'] = "$excelCol$excelRow:$excelColLast$excelRow";
			$excelData[$excelRow][$excelCol]  = 'There are no records that meets the sample size requirement of ';
			$excelData[$excelRow][$excelCol] .= '<strong>"' . SAMPLE_SIZE . '"</strong> with the chosen breakout.';
		}
		
		//End print output table
		echo '</table>';
		$excelInfo['tblLast'] = $excelColLast.$excelRow;
		
		if (!empty($printAMA_CR)) {
			echo '<p>' . AMA_CR . '</p>';
			$excelRow += 2; $excelCol = 'A';
			$excelInfo['ama_cr'] = "$excelCol$excelRow:$excelColLast$excelRow"; $excelData[$excelRow][$excelCol] = AMA_CR;
		}
		echo '</div>';
		
		$result->free();
		unset($result);
		
		$_SESSION[$excelID] = array('data' => $excelData, 'info' => $excelInfo);
	} else {
		echo '<div class="error_msg">';
		echo 'Failed to run query';
		//echo ($mysqli->errno) ? ": ($mysqli->errno) $mysqli->error" : ': no mysqli error returned';
		echo '</div>';
	}
	
	$mysqli->close();
	unset($mysqli);
} else {
	echo '<div class="error_msg">Cannot retrieve data: no database connection</div>';
}
?>