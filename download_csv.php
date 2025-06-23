<?php
include 'db.php';

// Query data peserta
$result = $conn->query("SELECT * FROM registrations ORDER BY registered_at DESC");

// Set header untuk download file txt
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="data_peserta.txt"');

// Baris header (kolom)
echo "No\tNama\tEmail\tTelepon\tPerusahaan\tEvent\tWaktu Registrasi\n";

$no = 1;
while ($row = $result->fetch_assoc()) {
    echo $no++ . "\t" .
         $row['name'] . "\t" .
         $row['email'] . "\t" .
         $row['phone'] . "\t" .
         $row['company'] . "\t" .
         $row['event'] . "\t" .
         $row['registered_at'] . "\n";
}

$conn->close();
?>
