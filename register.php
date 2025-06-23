<?php
include 'db.php';

// Include library PHP QR Code
include 'phpqrcode/qrlib.php';  // Pastikan path ini sesuai lokasi library

$name     = $_POST['name'];
$email    = $_POST['email'];
$phone    = $_POST['phone'];
$company  = $_POST['company'];
$event    = $_POST['event'];

$stmt = $conn->prepare("INSERT INTO registrations (name, email, phone, company, event) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $company, $event);

$success = false;
$qrcode_path = '';

if ($stmt->execute()) {
    $success = true;
    $last_id = $stmt->insert_id;

    // Data yang akan diencode ke QR Code, bisa custom sesuai kebutuhan
    $qr_data = "ID: $last_id\nName: $name\nEmail: $email\nPhone: $phone\nCompany: $company\nEvent: $event";

    // Folder untuk simpan QR Code (pastikan folder ini sudah ada dan writable)
    $folder = 'qrcodes/';
    if (!file_exists($folder)) {
        mkdir($folder, 0755, true);
    }

    // Nama file QR Code
    $filename = $folder . 'qr_' . $last_id . '.png';

    // Generate QR Code
    QRcode::png($qr_data, $filename, QR_ECLEVEL_L, 4, 2);

    $qrcode_path = $filename;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hasil Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?> shadow">
        <?php
        if ($success) {
            echo "🎉 Registrasi berhasil! Kami akan segera menghubungi Anda.";
        } else {
            echo "❌ Maaf, registrasi gagal. Silakan coba lagi.";
        }
        ?>
    </div>

    <?php if ($success && file_exists($qrcode_path)) : ?>
        <h5>QR Code Registrasi Anda:</h5>
        <img src="<?php echo $qrcode_path; ?>" alt="QR Code Registrasi" class="img-thumbnail" style="max-width:200px;">
        <p>Silakan simpan atau screenshot QR Code ini untuk keperluan event.</p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary">← Kembali ke Form</a>
</div>

</body>
</html>
