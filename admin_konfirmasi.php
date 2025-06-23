<?php
include 'db.php';

// Tandai hadir manual
if (isset($_GET['mark_attend'])) {
    $id = intval($_GET['mark_attend']);
    $stmt = $conn->prepare("UPDATE registrations SET is_confirmed = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_konfirmasi.php");
    exit;
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=peserta_hadir.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Nama', 'Email', 'HP', 'Perusahaan', 'Waktu Daftar']);

    $result = $conn->query("SELECT id, name, email, phone, company, created_at FROM registrations WHERE is_confirmed = 1 ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['name'], $row['email'], $row['phone'], $row['company'], $row['created_at']]);
    }
    fclose($output);
    exit;
}

// Pencarian
$search = $_GET['search'] ?? '';
$searchSql = '';
if ($search !== '') {
    $searchEscaped = '%' . $conn->real_escape_string($search) . '%';
    $searchSql = " AND (name LIKE '$searchEscaped' OR email LIKE '$searchEscaped' OR company LIKE '$searchEscaped')";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Status Kehadiran Peserta - Event XIXI</title>
</head>
<body>
    <h2>Status Kehadiran Peserta</h2>

    <form method="get" action="">
        <input type="text" name="search" placeholder="Cari nama/email/perusahaan..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Cari</button>
        <a href="admin_konfirmasi.php">Reset</a>
    </form>

    <p><a href="?export=csv">📄 Download CSV Peserta Hadir</a></p>

    <h3>✅ Sudah Konfirmasi Hadir</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>Nama</th><th>Email</th><th>HP</th><th>Perusahaan</th><th>Waktu Daftar</th>
        </tr>
        <?php
        $sqlConfirmed = "SELECT id, name, email, phone, company, created_at FROM registrations WHERE is_confirmed = 1 $searchSql ORDER BY created_at DESC";
        $result = $conn->query($sqlConfirmed);
        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['company']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6">Belum ada yang hadir.</td></tr>
        <?php endif; ?>
    </table>

    <h3>❌ Belum Konfirmasi (Manual)</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>Nama</th><th>Email</th><th>HP</th><th>Perusahaan</th><th>Waktu Daftar</th><th>Aksi</th>
        </tr>
        <?php
        $sqlUnconfirmed = "SELECT id, name, email, phone, company, created_at FROM registrations WHERE is_confirmed = 0 $searchSql ORDER BY created_at DESC";
        $result2 = $conn->query($sqlUnconfirmed);
        if ($result2 && $result2->num_rows > 0):
            while ($row = $result2->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['company']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td><a href="?mark_attend=<?= $row['id'] ?>" onclick="return confirm('Tandai peserta ini sudah hadir?')">✅ Tandai Hadir</a></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="7">Semua peserta sudah konfirmasi atau tidak ditemukan.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
