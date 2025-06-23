<?php
include 'auth.php';
include 'db.php';

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: admin.php?msg=deleted');
    exit;
} else {
    echo "Gagal menghapus data: " . $conn->error;
}
?>
