<?php
include 'db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="data_peserta.csv"');

$output = fopen('php://output', 'w');

// Header kolom
fputcsv($output, ['No', 'Nama', 'Email', 'Telepon', 'Perusahaan', 'Event', 'Waktu Registrasi']);

$result = $conn->query("SELECT * FROM registrations ORDER BY created_at DESC");
$no = 1;
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $no++,
        $row['name'],
        $row['email'],
        $row['phone'],
        $row['company'],
        $row['event'],
        date('d-m-Y H:i', strtotime($row['created_at']))
    ]);
}

fclose($output);
$conn->close();
exit;
?>
