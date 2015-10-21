<?php
session_start();
date_default_timezone_set('America/Los_Angeles');
require_once './Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$sheet = $objPHPExcel->getActiveSheet();

// Set document properties
$objPHPExcel->getProperties()->setCreator("CHIA")
							 ->setLastModifiedBy("CHIA")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

//Merge title cells together
$sheet->mergeCells('A1:G1');
$sheet->mergeCells('A2:G2');
$sheet->mergeCells('A3:G3');
$sheet->mergeCells('A4:G4');

$today = date('m/d/Y h:i:s a');

// Add A Report Title
$sheet->setCellValue('A1', 'Facility Report For A DRG');
//MODIFIED BY DANNY - $_SESSION['rptadrgtext'] now already includes drgid so $_SESSION['rptadrg']." - ". is commented out
$sheet->setCellValue('A2', /*$_SESSION['rptadrg']." - ".*/$_SESSION['rptadrgtext']);
$sheet->setCellValue('A3', 'Data Period: '.$_SESSION['rptabgnquarter'].' - '.$_SESSION['rptaendquarter']);
$sheet->setCellValue('A4', 'Created On:  '.$today);

// Style array for titles
//
//Font
$sheet->getStyle('A1')->getFont()->applyFromArray(
        array(
            'name'      => 'Arial',
            'bold'      => true,
			'size'		=> 16));

$sheet->getStyle('A2:A3')->getFont()->applyFromArray(
        array(
            'name'      => 'Arial',
            'bold'      => true,
			'size'		=> 11));

//created on title
$sheet->getStyle('A4')->getFont()->applyFromArray(
        array(
            'name'      => 'Arial',
            'bold'      => false,
			'size'		=> 11));
//Alignment
$sheet->getStyle('A1:A4')->getAlignment()->applyFromArray(
        array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM));
//********************************************************************************


// Add Column Headers
$headerrow = "6";
$sheet->setCellValue("A$headerrow", $_SESSION['facilityheader'])
    ->setCellValue("B$headerrow", 'Cases')
    ->setCellValue("C$headerrow", 'Total LOS')
	->setCellValue("D$headerrow", 'Total Charges')
    ->setCellValue("E$headerrow", 'Avg LOS')
    ->setCellValue("F$headerrow", 'Avg Charges')
    ->setCellValue("G$headerrow", 'Daily Charges');
			



/*$default_border = array(
    'style' => PHPExcel_Style_Border::BORDER_THIN,
    'color' => array('rgb'=>'1006A3')
);*/
$style_datacolumn_header = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'008080'),
    ),
    'font' => array(
		'name' => 'Arial',
        'bold' => true,
		'size' => 10,
		'color' => array('rgb' => 'FFFFFF')
    )
);

//data cell format arrays
$groupedfield = array(
	'font' => array(
		'name' => 'Arial',
        'bold' => true,
		'size' => 10));
$datafield = array(
	'font' => array(
		'name' => 'Arial',
        'bold' => false,
		'size' => 10));


// Alignment Style array for headers
$objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->applyFromArray(
        array(
			'indent'  => 1,
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM));
//********************************************************************************		
// Font Style array for headers
$sheet->getStyle('A6:G6')->applyFromArray( $style_datacolumn_header );

//********************************************************************************
// The Raw Data in the session array.  The $rs from sql query
// datastartrow - this one not for loop
$datastartrow = 7;
$rownum=7;
$fac1 = '';
$fac2 = '';

foreach ($_SESSION['rptresultsa'] as $value){
	$column='A';
	foreach($value as $k=>$v){
		//may want to supress duplicate facility names on breakouts in future....
		//field we are on ($k is key field from sql query)
		//
		if ($_SESSION['breakoutrpta']){
			switch ($k) {
				Case 'facility':
					$fac2 = $v;
					if ($fac1 <> $fac2){
						$fac1 = $v;
						$rownum++;
						$sheet->setCellValue($column.$rownum, $v);
						$sheet->getStyle("$column$rownum")->applyFromArray( $groupedfield );
						$rownum++;
						$column='A';
					} 
					break;
				default:
					$sheet->setCellValue($column.$rownum, trim($v));
					$sheet->getStyle("$column$rownum")->applyFromArray( $datafield );
					$column++;
					break;
			}
		} else {
			switch ($k) {
				Case 'facility':
					$fac2 = $v;
					if ($fac1 <> $fac2){
						$fac1 = $v;
						$sheet->setCellValue($column.$rownum, $v);
						$sheet->getStyle("$column$rownum")->applyFromArray( $datafield );
					} else {
						$sheet->setCellValue($column.$rownum, " ");
					}
					$column++;
					break;
				Case 'breakout':
					break;
				default:
					$sheet->setCellValue($column.$rownum, $v);
					$sheet->getStyle("$column$rownum")->applyFromArray( $datafield );
					$column++;
					break;
			}
		}
		
	}
	$rownum++;
}

// Format the cells with the data here.
// $rownum holds the max #rows we have,
// and we know to start in row 2.
// So, it would be A2:A$rownum.
//*************************************
//cases
$bmaxrow = "B".$rownum;
$objPHPExcel->getActiveSheet()->getStyle("B$datastartrow:$bmaxrow")->getNumberFormat()
	->setFormatCode('#,##0');
//TotalLOS
$cmaxrow = "C".$rownum;
$objPHPExcel->getActiveSheet()->getStyle("C$datastartrow:$cmaxrow")->getNumberFormat()
	->setFormatCode('#,##0');
//TotalCharges
$dmaxrow = "D".$rownum;
$objPHPExcel->getActiveSheet()->getStyle("D$datastartrow:$dmaxrow")->getNumberFormat()
	->setFormatCode('###,###,##0');
//AveLOS
$emaxrow = "E".$rownum;
$objPHPExcel->getActiveSheet()->getStyle("E$datastartrow:$emaxrow")->getNumberFormat()
	->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//Average Charges
//  12/18/2012 dkf - take the .00 off the format code
$fmaxrow = "F".$rownum;
$objPHPExcel->getActiveSheet()->getStyle("F$datastartrow:$fmaxrow")->getNumberFormat()
	->setFormatCode('###,###,##0');
//Daily Charges
//  12/18/2012 dkf - take the .00 off the format code
$gmaxrow = "G".$rownum;
$objPHPExcel->getActiveSheet()->getStyle("G$datastartrow:$gmaxrow")->getNumberFormat()
	->setFormatCode('###,###,##0');

//***************************************************************************************			

// Set autowidths on all columns
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);

	
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Facility Report for a DRG');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
//$objPHPExcel->setActiveSheetIndex(0);

$sheet->setSelectedCell('A1');

// Redirect output to a client?s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="FacilityReportForDRG.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit;
?>