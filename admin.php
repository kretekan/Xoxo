<?php
include 'auth.php';
include 'db.php';

// Ambil semua jenis event unik
$eventList = [];
$resultEvent = $conn->query("SELECT DISTINCT event FROM registrations ORDER BY event ASC");
while ($row = $resultEvent->fetch_assoc()) {
    $eventList[] = $row['event'];
}

// Tangkap filter event dari URL
$selectedEvent = $_GET['event'] ?? '';

// Query utama peserta
if ($selectedEvent) {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE event = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $selectedEvent);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query("SELECT * FROM registrations ORDER BY created_at DESC");
    if (!$result) {
        die("Query error: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Daftar Peserta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">📋 Daftar Peserta Event XOXO</h2>

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

    <div class="mb-3 text-end">
        <a href="hadir.php" class="btn btn-success">✅ Peserta Hadir</a>
        <a href="export_excel.php" class="btn btn-success">⬇ Export Data (Excel)</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <div class="table-responsive shadow p-3 bg-white rounded">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Perusahaan</th>
                    <th>Event</th>
                    <th>Nomor Kursi</th> <!-- baru -->
                    <th>Waktu Registrasi</th>
                    <th>QR Code</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['company']) ?></td>
                            <td><?= htmlspecialchars($row['event']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['seat_number'] ?: '-') ?></td> <!-- baru -->
                            <td class="text-center"><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                            <td class="text-center">
                                <?php
                                    $qrFile = 'qrcodes/qr_' . $row['id'] . '.png';
                                    if (file_exists($qrFile)) {
                                        echo '<img src="' . $qrFile . '" alt="QR Code" width="80" height="80">';
                                    } else {
                                        echo 'QR Code belum dibuat';
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <!-- tombol edit nomor kursi baru -->
                                <button class="btn btn-sm btn-primary btn-edit-seat" data-id="<?= $row['id'] ?>" data-seat="<?= htmlspecialchars($row['seat_number']) ?>">Edit Kursi</button>
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="text-center">Belum ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="index.php" class="btn btn-primary">+ Tambah Peserta Baru</a>
    </div>
</div>

<!-- Modal Edit Nomor Kursi -->
<div class="modal fade" id="editSeatModal" tabindex="-1" aria-labelledby="editSeatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editSeatForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editSeatModalLabel">Edit Nomor Kursi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="seatEditId">
          <div class="mb-3">
            <label for="seatNumber" class="form-label">Nomor Kursi</label>
            <input type="text" class="form-control" id="seatNumber" name="seat_number" placeholder="Contoh: A5" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Show modal and isi data nomor kursi saat tombol ditekan
document.querySelectorAll('.btn-edit-seat').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const seat = button.getAttribute('data-seat') || '';

        document.getElementById('seatEditId').value = id;
        document.getElementById('seatNumber').value = seat;

        var editModal = new bootstrap.Modal(document.getElementById('editSeatModal'));
        editModal.show();
    });
});

// AJAX submit form update nomor kursi
document.getElementById('editSeatForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('update_seat.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Nomor kursi berhasil diperbarui!');
            location.reload();
        } else {
            alert('Gagal memperbarui: ' + data.message);
        }
    })
    .catch(() => alert('Terjadi kesalahan jaringan.'));
});
</script>

</body>
</html>

<?php
$conn->close();
?>
