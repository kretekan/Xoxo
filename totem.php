<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Totem QR Check-in</title>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #1f1f1f, #111);
      color: #00ff99;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    h1 {
      font-size: 48px;
      margin-bottom: 20px;
      color: #00ffaa;
    }

    p {
      font-size: 24px;
      margin: 10px 0;
      color: #bfbfbf;
    }

    input {
      font-size: 36px;
      width: 80%;
      padding: 20px;
      border: none;
      background: #000;
      color: #00ff99;
      text-align: center;
      outline: none;
      border-radius: 8px;
      letter-spacing: 2px;
    }

    .message {
      font-size: 28px;
      margin-top: 40px;
      min-height: 40px;
      transition: all 0.3s ease;
    }

    .thank-you {
      color: #00e676;
      font-weight: bold;
    }

    .error {
      color: #ff5252;
    }

    .footer {
      position: absolute;
      bottom: 20px;
      font-size: 16px;
      color: #666;
    }
  </style>
</head>
<body onload="focusInput()">

  <h1>📲 Scan QR Anda</h1>
  <p>Silakan arahkan QR ke scanner</p>

  <input type="text" id="qrInput" autofocus autocomplete="off" placeholder="Scan di sini..." />

  <div class="message" id="message"></div>

  <div class="footer">Totem XIXI Event Check-in</div>

  <script>
    const input = document.getElementById('qrInput');
    const message = document.getElementById('message');
    let buffer = "";

    function focusInput() {
      input.focus();
      buffer = "";
    }

    input.addEventListener('input', () => {
      buffer = input.value.trim();

      if (buffer.includes("verify.php?id=")) {
        message.className = "message thank-you";
        message.innerText = "✅ Terima kasih, selamat menikmati acara!";
        setTimeout(() => {
          window.location.href = buffer;
        }, 500);
      } else if (buffer.length > 50) {
        message.className = "message error";
        message.innerText = "❌ QR tidak dikenali. Silakan coba lagi.";
        setTimeout(() => {
          input.value = "";
          buffer = "";
          message.innerText = "";
        }, 2000);
      }
    });

    setInterval(focusInput, 3000);
  </script>

</body>
</html>
