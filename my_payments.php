<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "peserta") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$user_id = (int)$_SESSION['user_id'];

// cek profil anggota
$cekAnggota = mysqli_query($koneksi, "SELECT id FROM anggota WHERE user_id = $user_id LIMIT 1");
if (mysqli_num_rows($cekAnggota) === 0) {
    header("Location: lengkapi_profil.php?from=my_payments.php");
    exit();
}

$payments = mysqli_query(
    $koneksi,
    "SELECT * FROM payments WHERE user_id = $user_id ORDER BY created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembayaran Saya</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <div class="card-box">
            <h2>Riwayat Pembayaran Saya</h2>
            <p>Data ini diinput oleh admin atau dikirim dari pembayaran online. Kamu bisa melihat status dan bukti pembayaran.</p>

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Pembayaran</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Bukti</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($payments) === 0): ?>
                    <tr><td colspan="6">Belum ada pembayaran tercatat atas nama kamu.</td></tr>
                <?php else: ?>
                    <?php while ($p = mysqli_fetch_assoc($payments)): ?>
                        <tr>
                            <td><?= $p['id']; ?></td>
                            <td><?= htmlspecialchars($p['payment_type']); ?></td>
                            <td>Rp <?= number_format($p['amount'], 0, ',', '.'); ?></td>
                            <td><?= $p['payment_date']; ?></td>
                            <td>
                                <span class="badge <?= $p['status']; ?>">
                                    <?= strtoupper($p['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($p['bukti_tf'])): ?>
                                    <a href="<?= htmlspecialchars($p['bukti_tf']); ?>" target="_blank">Lihat</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php include "footer.php"; ?>
    </div>
</div>

</body>
</html>
