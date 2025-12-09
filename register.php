<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun - Sistem Pembayaran</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

<div class="login-container">
    <div class="login-title">Daftar Akun Peserta</div>
    <div class="login-subtitle">
        Akun akan diverifikasi admin sebelum bisa login.
    </div>

    <form action="register_process.php" method="POST">
        <label>Username</label>
        <input type="text" name="username" placeholder="Misal: nurfadillah23" required>

        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>

        <label>Angkatan</label>
        <input type="text" name="angkatan" placeholder="Misal: 2023" required>

        <label>Jabatan</label>
        <select name="jabatan" required>
            <option value="">Pilih jabatan...</option>
            <option value="Ketua Himpunan">Ketua Himpunan</option>
            <option value="Wakil Ketua Himpunan">Wakil Ketua Himpunan</option>
            <option value="Bendahara">Bendahara</option>
            <option value="Sekertaris">Sekertaris</option>
            <option value="Anggota Kaderisasi">Anggota Kaderisasi</option>
            <option value="Angota Kominfo">Angota Kominfo</option>
            <option value="Anggota Humas">Anggota Humas</option>
            <option value="Anggota Keilmuan">Anggota Keilmuan</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn-primary">Daftar</button>

        <div class="login-small">
            Sudah punya akun?
            <a href="login.php">Login di sini</a>
        </div>
    </form>
</div>

</body>
</html>
