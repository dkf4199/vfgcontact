<?php
session_start();

function printError($error)
{
	echo '<!DOCTYPE html><html><head>';
	echo '<style>';
	echo	'div { text-align:center; color:red; }';
	echo	'button { position:absolute; bottom:.5em; right:.5em; background-color:#d7e7e7; }';
	echo '</style>';
	echo '</head><body onload="window.frameElement.className = \'show\'">';
	echo '<div>' . $error . '</div>';
	echo '<button type="button" onclick="window.frameElement.className = \'hide\'">Close</button>';
	echo '<body></html>';
}

if (empty($_GET['excelID'])) {
	printError('Missing Report ID<br />Cannot Create Excel File');
	return;
} else {
	$excelID = $_GET['excelID'];
	if (empty($_SESSION[$excelID])) {
		printError('Excel file has expired for this report and is no longer availiable<br />Please run the report again to export to Excel');
		return;
	} else {
		//echo '<pre>' . print_r(array_reverse($_SESSION[$excelID]), true) . '</pre>'; return;
		$excelData = $_SESSION[$excelID]['data'];
		$excelInfo = $_SESSION[$excelID]['info'];
	}
}

if ((include '../Classes/PHPExcel.php') == false) {
	printError('Failed to load PHPExcel<br />Cannot Create Excel File');
	return;
}

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

foreach ($excelData as $row => $data) {
	foreach ($data as $col => $value) {
		$sheet->setCellValue($col . $row, html_entity_decode(strip_tags($value)));
	}
}

$objPHPExcel->getDefaultStyle()->applyFromArray(
	array(
		'font' => array(
			'name' => 'Calibri',
			'size' => 11
		),
		'alignment' => array(
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	)
);

$sheet
	->setTitle($excelInfo['reportName'])
	->mergeCells($excelInfo['title'])
	->mergeCells($excelInfo['patient'])
	->mergeCells($excelInfo['period'])
	->mergeCells($excelInfo['created']);

$row = substr(strstr($excelInfo['tblHeader'], ':', true), 1);
foreach ($excelData[$row] as $col => $value) {
	switch ($value) {
		case 'Cases':
		case 'Tot Days':
			$formatCode = '#,##0';
			$width = null;
			break;
		case 'Tot Service Units':
			$formatCode = '#,##0';
			$width = 16;
			break;
		case 'Avg Days':
			$formatCode = '#,##0.00';
			$width = null;
			break;
		case 'Tot Charges':
		case 'Avg Charges':
			$formatCode = '$#,##0';
			$width = 12;
			break;
		case 'Avg Daily Charges':
			$formatCode = '$#,##0';
			$width = 16;
			break;
		default:
			$formatCode = '@';
			$width = 50;
			break;
	}
	
	$range = sprintf('%1$s%2$d:%1$s%3$d', $col, $row + 1, substr($excelInfo['tblLast'], 1));
	$sheet->getStyle($range)->getNumberFormat()->setFormatCode($formatCode);
	
	if (isset($width)) {
		$sheet->getColumnDimension($col)->setWidth($width);
	}
}

$range = strstr($excelInfo['title'], ':', true) . strstr($excelInfo['created'], ':');
$sheet->getStyle($range)->getFont()
	->setBold(true);
$sheet->getStyle($range)->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$sheet->getStyle($excelInfo['title'])->getFont()
	->setSize(16);

$range = strstr($excelInfo['patient'], ':', true) . strstr($excelInfo['period'], ':');
$sheet->getStyle($range)->getFont()
	->setSize(12)
	->setItalic(true);

$sheet->getStyle($excelInfo['created'])->getFont()
	->setSize(11);

$sheet->getStyle($excelInfo['subject'])->getFont()
	->setSize(12)
	->setItalic(true);

$range = strstr($excelInfo['tblHeader'], ':', true) . ':' . $excelInfo['tblLast'];
$sheet->getStyle($range)->getBorders()->applyFromArray(
	array(
		'outline' => array(
			'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
			'color' => array('rgb' => '008080')
		),
		'vertical' => array(
			'style' => PHPExcel_Style_Border::BORDER_HAIR,
			'color' => array('rgb' => '000000')
		)
	)
);

$sheet->getStyle($excelInfo['tblHeader'])->applyFromArray(
	array(
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => '008080')
		),
		'font' => array(
			'color' => array('rgb' => 'FFFFFF')
		),
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		)
	)
);

$sheet->getStyle(strstr($excelInfo['tblHeader'], ':', true))->getAlignment()
	->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

if (empty($excelInfo['breakout'])) {
	$range = sprintf('%s:%s%d', strstr($excelInfo['tblHeader'], ':', true), $excelInfo['tblHeader'][0], substr($excelInfo['tblLast'], 1));
	$sheet->getStyle($range)->getAlignment()
		->setWrapText(true);
} else {
	$breakouts = explode(',', ltrim($excelInfo['breakout'], ','));
	$rows = array_map(function ($range) { return substr($range, 1) - 2; }, array_slice($breakouts, 1));
	$rows[] = substr($excelInfo['tblLast'], 1);
	
	foreach ($breakouts as $key => $lvl1) {
		$col = $lvl1[0];
		$row = substr($lvl1, 1);
		
		$sheet->getStyle($lvl1)->getFont()
			->setBold(true);
		
		$sheet->getStyle($lvl1)->getAlignment()
			->setWrapText(false);
		
		$lvl2 = sprintf('%1$s%2$d:%1$s%3$d', $col, $row + 1, $rows[$key]);
		$sheet->getStyle($lvl2)->getAlignment()
			->setIndent(3)
			->setWrapText(true);
		
		$range = sprintf('%s%d:%s%d', $col, $row - 1, $excelInfo['tblLast'][0], $row);
		$sheet->getStyle($range)->getBorders()->getVertical()
			->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
	}
}

if (isset($excelInfo['no_recs'])) {
	$sheet->mergeCells($excelInfo['no_recs']);
	
	$sheet->getRowDimension(substr(strstr($excelInfo['no_recs'], ':'), 2))
		->setRowHeight(30);
}

/* if (isset($excelInfo['ama_cr'])) {
	$sheet->mergeCells($excelInfo['ama_cr']);
} */

$sheet->setSelectedCell('A1');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $excelInfo['reportName'] . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->setPreCalculateFormulas(false);
$objWriter->save('php://output');
?>