<?php
include 'auth.php';
include 'db.php';

$id = $_POST['id'] ?? null;
$seat_number = $_POST['seat_number'] ?? '';

header('Content-Type: application/json');

if (!$id || !$seat_number) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$id = intval($id);
$seat_number = strtoupper(trim($conn->real_escape_string($seat_number)));

// Cek apakah nomor kursi sudah dipakai peserta lain
$check = $conn->prepare("SELECT id FROM registrations WHERE seat_number = ? AND id != ?");
$check->bind_param("si", $seat_number, $id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Nomor kursi sudah digunakan peserta lain']);
    exit;
}
$check->close();

// Update nomor kursi
$update = $conn->prepare("UPDATE registrations SET seat_number = ? WHERE id = ?");
if (!$update) {
    echo json_encode(['success' => false, 'message' => 'Gagal membuat query update']);
    exit;
}
$update->bind_param("si", $seat_number, $id);
if ($update->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database']);
}
$update->close();
$conn->close();
