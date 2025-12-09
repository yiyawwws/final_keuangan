<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "peserta") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$user_id = (int)$_SESSION['user_id'];

// cek apakah sudah punya data anggota
$cekAnggota = mysqli_query($koneksi, "SELECT id FROM anggota WHERE user_id = $user_id LIMIT 1");
$punyaProfil = mysqli_num_rows($cekAnggota) > 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Peserta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <?php if (!$punyaProfil): ?>
            <div class="card-box">
                <h2>Lengkapi Data Anggota HMJ</h2>
                <p>
                    Sebelum mengakses menu pembayaran, silakan lengkapi dulu data anggota HMJ kamu.
                </p>
                <a href="lengkapi_profil.php?from=participant_dashboard.php" class="btn-primary">
                    Lengkapi Data Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="card-box">
                <h2>Selamat datang, peserta</h2>
                <p>
                    Kamu bisa melihat riwayat pembayaran yang tercatat oleh admin,
                    serta panduan pembayaran online.
                </p>
                <a href="my_payments.php" class="btn-primary">Lihat Riwayat Pembayaran</a>
                <a href="pembayaran_online.php" class="btn-outline" style="margin-left:6px;">
                    Menu Pembayaran Online
                </a>
            </div>
        <?php endif; ?>

        <?php include "footer.php"; ?>
    </div>
</div>

</body>
</html>
