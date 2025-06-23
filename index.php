<!DOCTYPE html>
<html>
<head>
    <title>Form Registrasi Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Form Registrasi Event</h2>
    <form action="submit.php" method="post" class="mt-4">
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">No. Telepon</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="company" class="form-label">Perusahaan</label>
            <input type="text" name="company" id="company" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="event" class="form-label">Event</label>
            <select name="event" id="event" class="form-select" required>
                <option value="" disabled selected>Pilih Event</option>
                <option value="Seminar Teknologi">Seminar Teknologi</option>
                <option value="Workshop Design">Workshop Design</option>
                <option value="Konferensi Bisnis">Konferensi Bisnis</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Daftar</button>
    </form>
</div>

</body>
</html>
