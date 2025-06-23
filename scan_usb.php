<?php
include 'db.php';

$verified = false;
$error = '';
$participant = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['qr_input']);

    if ($input === '') {
        $error = "Masukkan data QR Code.";
    } else {
        if (preg_match('/ID:(\d+)/', $input, $matches)) {
            $id = (int)$matches[1];
            $stmt = $conn->prepare("SELECT * FROM registrations WHERE id = ?");
            $stmt->bind_param("i", $id);
        } else {
            $stmt = $conn->prepare("SELECT * FROM registrations WHERE email = ?");
            $stmt->bind_param("s", $input);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $participant = $result->fetch_assoc();
            $verified = true;
        } else {
            $error = "Data peserta tidak ditemukan.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Verifikasi Pendaftaran dengan Scanner USB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script>
        window.onload = function() {
            document.getElementById('qr_input').focus();
        };
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Verifikasi Pendaftaran Peserta (Scanner USB)</h2>

    <form method="post" class="w-50 mx-auto">
        <input type="text" id="qr_input" name="qr_input" class="form-control form-control-lg" placeholder="Scan QR Code di sini" autocomplete="off" autofocus required>
    </form>

    <?php if ($error): ?>
        <div class="alert alert-danger mt-3 w-50 mx-auto text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($verified && $participant): ?>
        <div class="alert alert-success mt-3 w-50 mx-auto text-center">
            <h4>Peserta Terverifikasi!</h4>
            <p><strong>Nama:</strong> <?= htmlspecialchars($participant['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($participant['email']) ?></p>
            <p><strong>Telepon:</strong> <?= htmlspecialchars($participant['phone']) ?></p>
            <p><strong>Perusahaan:</strong> <?= htmlspecialchars($participant['company']) ?></p>
            <p><strong>Event:</strong> <?= htmlspecialchars($participant['event']) ?></p>
            <p><strong>Waktu Registrasi:</strong> <?= date('d-m-Y H:i', strtotime($participant['created_at'])) ?></p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
