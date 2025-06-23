<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include 'db.php';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'Nama');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'Telepon');
$sheet->setCellValue('E1', 'Perusahaan');
$sheet->setCellValue('F1', 'Event');
$sheet->setCellValue('G1', 'Waktu Registrasi');

$result = $conn->query("SELECT * FROM registrations ORDER BY created_at DESC");

if (!$result) {
    die("Query error: " . $conn->error);
}

$rowNumber = 2;
$no = 1;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $no++);
    $sheet->setCellValue('B' . $rowNumber, $row['name']);
    $sheet->setCellValue('C' . $rowNumber, $row['email']);
    $sheet->setCellValue('D' . $rowNumber, $row['phone']);
    $sheet->setCellValue('E' . $rowNumber, $row['company']);
    $sheet->setCellValue('F' . $rowNumber, $row['event']);
    $sheet->setCellValue('G' . $rowNumber, date('d-m-Y H:i', strtotime($row['created_at'])));
    $rowNumber++;
}

$conn->close();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="data_peserta.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
