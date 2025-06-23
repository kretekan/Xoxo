<?php
session_start();

// Username dan password hash untuk password baru: xoxo2025
$ADMIN_USERNAME = 'fajar';
$ADMIN_PASSWORD_HASH = '$2y$10$3EZQmJGLrPzJDiS1cDkV2uIu/jMss6iYjTxvRvdEKI/j2NccTbZfu';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if ($user === $ADMIN_USERNAME && $pass === 'xoxo2025') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 400px;">
        <div class="card-body">
            <h4 class="text-center mb-4">Login Admin</h4>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
