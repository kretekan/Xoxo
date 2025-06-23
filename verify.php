<?php
include 'db.php';

$id = $_GET['id'] ?? null;
$peserta = null;
$message = '';
$error = false;

function generateSeatNumber($conn, $event) {
    $prefix = 'A';

    $stmt = $conn->prepare("
        SELECT seat_number 
        FROM registrations 
        WHERE event = ? AND seat_number LIKE CONCAT(?, '%') 
        ORDER BY LENGTH(seat_number) DESC, seat_number DESC 
        LIMIT 1
    ");
    if (!$stmt) {
        die("Prepare generateSeatNumber gagal: " . $conn->error);
    }

    $stmt->bind_param("ss", $event, $prefix);
    $stmt->execute();
    $res = $stmt->get_result();
    $lastSeat = $res->fetch_assoc()['seat_number'] ?? null;
    $stmt->close();

    if ($lastSeat) {
        $num = intval(substr($lastSeat, strlen($prefix))) + 1;
    } else {
        $num = 1;
    }
    return $prefix . $num;
}

if ($id && is_numeric($id)) {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE id = ?");
    if (!$stmt) {
        die("Prepare gagal: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $peserta = $result->fetch_assoc();
    $stmt->close();

    if ($peserta) {
        if (array_key_exists('is_confirmed', $peserta)) {
            if (!$peserta['is_confirmed']) {
                if (empty($peserta['seat_number'])) {
                    $newSeat = generateSeatNumber($conn, $peserta['event']);

                    $updSeat = $conn->prepare("UPDATE registrations SET seat_number = ? WHERE id = ?");
                    if (!$updSeat) {
                        die("Prepare update seat_number gagal: " . $conn->error);
                    }
                    $updSeat->bind_param("si", $newSeat, $id);
                    $updSeat->execute();
                    $updSeat->close();

                    $peserta['seat_number'] = $newSeat;
                }

                $update = $conn->prepare("UPDATE registrations SET is_confirmed = 1, updated_at = NOW() WHERE id = ?");
                if (!$update) {
                    die("Query update gagal: " . $conn->error);
                }
                $update->bind_param("i", $id);
                $update->execute();
                $update->close();

                $peserta['is_confirmed'] = 1;
                $message = "✅ Kehadiran berhasil dikonfirmasi.";
            } else {
                $message = "ℹ️ Peserta sudah dikonfirmasi hadir sebelumnya.";
            }
        } else {
            $error = true;
            $message = "❌ Kolom 'is_confirmed' tidak ditemukan di tabel.";
        }
    } else {
        $error = true;
        $message = "❌ Peserta tidak ditemukan.";
    }
} else {
    $error = true;
    $message = "❌ ID peserta tidak valid.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Cetak Tiket Peserta</title>
  <style>
    @page {
      size: 90mm 50mm;
      margin: 0;
    }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      width: 90mm;
      height: 50mm;
      background: #fff;
    }

    .ticket {
      width: 100%;
      height: 100%;
      box-sizing: border-box;
      padding: 6mm 8mm;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      text-transform: uppercase;
      border: 1px solid #333;
      border-radius: 6px;
      background: #fff;
    }

    .name {
      font-weight: 900;
      font-size: 28pt;
      line-height: 1.1;
      margin-bottom: 8px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }

    .company {
      font-size: 14pt;
      font-weight: 600;
      margin-bottom: 4px;
      color: #555;
    }

    .event {
      font-size: 12pt;
      font-weight: 600;
      color: #333;
      opacity: 0.85;
      margin-bottom: 8px;
    }

    .seat {
      font-size: 18pt;
      font-weight: 700;
      color: #d9534f;
      padding: 4px 12px;
      border: 2px solid #d9534f;
      border-radius: 6px;
      min-width: 60px;
    }

    @media print {
      body * {
        visibility: hidden;
      }
      .ticket, .ticket * {
        visibility: visible;
      }
      .ticket {
        position: absolute;
        top: 0;
        left: 0;
        border: none;
        border-radius: 0;
        padding: 4mm 6mm;
      }
    }
  </style>
</head>
<body>

<?php if ($error): ?>
  <div class="ticket" style="color: red; font-size: 18pt; text-align: center;">
    <?= htmlspecialchars($message) ?>
    <br />
    <a href="verify.php" style="margin-top: 10px; color: blue; text-decoration: none; display: inline-block;">← Kembali</a>
  </div>
<?php else: ?>
  <div class="ticket" id="print-area">
    <div class="name" id="name-text"><?= strtoupper(htmlspecialchars($peserta['name'])) ?></div>
    <div class="company"><?= strtoupper(htmlspecialchars($peserta['company'] ?: '-')) ?></div>
    <div class="event"><?= strtoupper(htmlspecialchars($peserta['event'])) ?></div>
    <div class="seat"><?= strtoupper(htmlspecialchars($peserta['seat_number'] ?: '-')) ?></div>
  </div>

  <script>
    function autoResizeText(element, maxFontSize = 28, minFontSize = 10) {
      let fontSize = maxFontSize;
      element.style.fontSize = fontSize + 'pt';
      while (element.scrollWidth > element.offsetWidth && fontSize > minFontSize) {
        fontSize--;
        element.style.fontSize = fontSize + 'pt';
      }
    }

    window.onload = function () {
      const nameElem = document.getElementById('name-text');
      autoResizeText(nameElem);

      setTimeout(() => {
        window.print();
        setTimeout(() => {
          window.location.href = 'verify_success.html';
        }, 1000);
      }, 500);
    };
  </script>
<?php endif; ?>

</body>
</html>
