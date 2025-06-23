<?php
include 'db.php';
require 'lib/phpqrcode/qrlib.php';

$id = $_GET['id'] ?? null;
$peserta = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $peserta = $result->fetch_assoc();
}

// Generate QR content
$qrText = "http://localhost/xixi/verify.php?id=" . $id;
$qrTempFile = "temp_qr_" . $id . ".png";
QRcode::png($qrText, $qrTempFile, 'L', 4, 2);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrasi Sukses</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #28a745;
            margin-bottom: 10px;
        }

        .qr img {
            width: 220px;
            height: 220px;
            margin: 20px 0;
        }

        p {
            margin: 8px 0;
            color: #333;
        }

        small {
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
  <?php if ($peserta): ?>
  <div class="card">
    <h2>✅ Registrasi Berhasil</h2>
    <p>Terima kasih, <strong><?= htmlspecialchars($peserta['name']) ?></strong>!</p>
    <p>Silakan tunjukkan QR berikut saat verifikasi hadir:</p>
    <div class="qr">
      <img src="<?= $qrTempFile ?>" alt="QR Code">
    </div>
    <small>Atau scan: <br><code><?= $qrText ?></code></small>
  </div>
  <?php else: ?>
    <p style="color:red;">Data peserta tidak ditemukan.</p>
  <?php endif; ?>
</body>

</html>
