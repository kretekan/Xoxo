<?php 
include 'db.php';
include 'phpqrcode/qrlib.php';

// Ambil data peserta lengkap termasuk company
$sql = "SELECT id, name, email, phone, company, created_at FROM registrations ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
</head>
<body>
<h2>Dashboard Admin - Daftar Peserta</h2>
<p>
    <a href="admin_konfirmasi.php">Lihat Status Konfirmasi Kehadiran</a>
</p>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No HP</th>
            <th>Perusahaan</th>
            <th>Waktu Daftar</th>
            <th>QR Code</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['company']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <?php
                        $tempDir = 'temp_qr/';
                        if (!file_exists($tempDir)) {
                            mkdir($tempDir, 0755, true);
                        }

                        $fileName = $tempDir . 'qr_' . $row['id'] . '.png';
                        $qrData = 'http://localhost/xixi/verify.php?id=' . $row['id'];

                        if (!file_exists($fileName)) {
                            QRcode::png($qrData, $fileName, QR_ECLEVEL_L, 3);
                        }
                        ?>
                        <img src="<?= $fileName ?>" alt="QR Code" width="80" height="80" />
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">Belum ada peserta.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
