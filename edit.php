<?php
include 'auth.php';
include 'db.php';

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data peserta berdasarkan id
$stmt = $conn->prepare("SELECT * FROM registrations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Data peserta tidak ditemukan.";
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone'];
    $company = $_POST['company'];
    $event   = $_POST['event'];

    $update_stmt = $conn->prepare("UPDATE registrations SET name=?, email=?, phone=?, company=?, event=? WHERE id=?");
    $update_stmt->bind_param("sssssi", $name, $email, $phone, $company, $event, $id);

    if ($update_stmt->execute()) {
        header('Location: admin.php?msg=updated');
        exit;
    } else {
        $error = "Gagal memperbarui data: " . $conn->error;
    }
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Peserta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Edit Data Peserta</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Perusahaan</label>
            <input type="text" name="company" class="form-control" value="<?= htmlspecialchars($row['company']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Event</label>
            <input type="text" name="event" class="form-control" value="<?= htmlspecialchars($row['event']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="admin.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
