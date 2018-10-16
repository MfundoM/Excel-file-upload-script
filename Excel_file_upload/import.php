<?php

require 'vendor/autoload.php';

$inputFileName = 'import.xlsx';

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($inputFileName);

$worksheet = $spreadsheet->getActiveSheet();

// We can load entire sheet as an array
// $sheetData = $spreadsheet->getActiveSheet()->toArray();

// We will rather just loop through each row
$highestRow = $worksheet->getHighestRow(); // How many rows do we have

$data = array(); // This will hold our final formatted data

$group = ''; 

for ($row = 1; $row <= $highestRow; ++$row) {

    // The value in the first column of each row will depict what we do with the row
    $value = strtolower($worksheet->getCellByColumnAndRow(1, $row)->getValue());
    //echo $value . '<br/>' . PHP_EOL;

    if( strpos($value, 'investment advisers') !== false ) $group = "Investment Advisers";
    if( strpos($value, 'sponsors') !== false ) $group = "Sponsors";
    if( strpos($value, 'legal advisers') !== false ) $group = "Legal Advisers";
    if( strpos($value, 'reporting accountants') !== false ) $group = "Reporting Accountants";

   // echo "Group:" . $group ."<br/>";

    // If we have a rank in the left table
    if( intval($value) > 0 ) {
        $data[] = [
            'type' => $group, 
            'flag' => 'v',
            'rank' => intval($value), 
            'company' => $worksheet->getCellByColumnAndRow(2, $row)->getValue(), 
            'deals' => 0,
            'value' => round($worksheet->getCellByColumnAndRow(3, $row)->getValue()), 
            'share' => round($worksheet->getCellByColumnAndRow(4, $row)->getValue() * 100, 2)
        ];
    }

    // Check the right table
    $value = strtolower($worksheet->getCellByColumnAndRow(6, $row)->getValue());
    if( intval($value) > 0 ) {
        $data[] = [
            'type' => $group, 
            'flag' => 'f', 
            'rank' => intval($value), 
            'company' => $worksheet->getCellByColumnAndRow(7, $row)->getValue(), 
            'deals' => intval($worksheet->getCellByColumnAndRow(8, $row)->getValue()), 
            'value' => round($worksheet->getCellByColumnAndRow(8, $row)->getValue()), 
            'share' => round($worksheet->getCellByColumnAndRow(9, $row)->getValue() * 100, 2)
        ];
    }

}

var_dump($data);