<?php
include 'db.php';

// Ambil semua jenis event unik dari peserta yang hadir
$eventList = [];
$resultEvent = $conn->query("SELECT DISTINCT event FROM registrations WHERE is_confirmed = 1 ORDER BY event ASC");
while ($row = $resultEvent->fetch_assoc()) {
    $eventList[] = $row['event'];
}

// Tangkap filter event dari URL
$selectedEvent = $_GET['event'] ?? '';

// Query utama berdasarkan filter
if ($selectedEvent) {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE is_confirmed = 1 AND event = ? ORDER BY id DESC");
    $stmt->bind_param("s", $selectedEvent);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $sql = "SELECT * FROM registrations WHERE is_confirmed = 1 ORDER BY id DESC";
    $result = $conn->query($sql);
}

if (!$result) {
    die("Query error: " . $conn->error);
}

$total = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Peserta Hadir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Daftar Peserta yang Sudah Hadir</h2>

    <!-- Filter Event -->
    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <select name="event" class="form-select" onchange="this.form.submit()">
                <option value="">🔍 Semua Jenis Event</option>
                <?php foreach ($eventList as $event): ?>
                    <option value="<?= htmlspecialchars($event) ?>" <?= $selectedEvent == $event ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($total > 0): ?>
        <p class="text-center mb-4">Jumlah peserta yang sudah terverifikasi hadir: <strong><?= $total ?></strong></p>
        <div class="table-responsive shadow p-3 bg-white rounded">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Perusahaan</th>
                        <th>Event</th>
                        <th>Waktu Registrasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['company']) ?></td>
                            <td><?= htmlspecialchars($row['event']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><span class="badge bg-success">Sudah Hadir</span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <h4>Belum ada peserta yang melakukan konfirmasi hadir.</h4>
            <p>Silakan tunggu peserta pertama yang melakukan scan QR pada hari acara.</p>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="admin.php" class="btn btn-primary">← Kembali ke Admin</a>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
