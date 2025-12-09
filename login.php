<?php
session_start();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<?php if ($msg === 'success_logout'): ?>
<script>
    alert("Berhasil logout!");
</script>
<?php endif; ?>

<div class="login-container">
    <div class="login-title">Login</div>
    <div class="login-subtitle">Masuk menggunakan username dan password</div>

    <?php if ($msg === 'registrasi_berhasil'): ?>
        <div class="alert success">Registrasi berhasil. Tunggu verifikasi admin.</div>
    <?php elseif ($msg === 'belum_verifikasi'): ?>
        <div class="alert error">Akun kamu belum diverifikasi admin.</div>
    <?php elseif ($msg === 'gagal'): ?>
        <div class="alert error">Username atau password salah.</div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn-primary">Login</button>

        <div class="login-small">
            Belum punya akun?
            <a href="register.php">Daftar di sini</a>
        </div>
    </form>
</div>

</body>
</html>
