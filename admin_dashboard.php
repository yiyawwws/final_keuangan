<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

// Aksi verifikasi atau hapus peserta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_user'])) {
        $uid = (int)$_POST['user_id'];
        mysqli_query($koneksi, "UPDATE users SET is_verified = 1 WHERE id = $uid AND role = 'peserta'");
    } elseif (isset($_POST['delete_user'])) {
        $uid = (int)$_POST['user_id'];
        mysqli_query($koneksi, "DELETE FROM users WHERE id = $uid AND role = 'peserta'");
    }
}

// Data peserta belum diverifikasi
$unverified = mysqli_query(
    $koneksi,
    "SELECT id, nama, angkatan, jabatan, username, created_at 
     FROM users 
     WHERE role = 'peserta' AND is_verified = 0 
     ORDER BY created_at DESC"
);

// Statistik sederhana
$totalPeserta = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE role='peserta'"))[0];
$totalVerified = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE role='peserta' AND is_verified=1"))[0];
$totalPayments = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM payments"))[0];

// Riwayat pendaftaran semua peserta
$regHistory = mysqli_query(
    $koneksi,
    "SELECT id, nama, angkatan, jabatan, username, is_verified, created_at
     FROM users
     WHERE role = 'peserta'
     ORDER BY created_at DESC"
);

// Riwayat pembayaran
$paymentHistory = mysqli_query(
    $koneksi,
    "SELECT p.id, u.nama, u.angkatan, u.jabatan, p.payment_type, p.amount, p.payment_date, p.status
     FROM payments p
     JOIN users u ON p.user_id = u.id
     ORDER BY p.created_at DESC"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <!-- Ringkasan -->
        <div class="card-box">
            <h2>Ringkasan Sistem</h2>
            <p>Total Peserta: <strong><?= $totalPeserta; ?></strong></p>
            <p>Peserta Terverifikasi: <strong><?= $totalVerified; ?></strong></p>
            <p>Total Data Pembayaran: <strong><?= $totalPayments; ?></strong></p>
        </div>

        <!-- Verifikasi Akun -->
        <div class="card-box">
            <h2>Verifikasi Akun Peserta</h2>
            <p>Peserta yang baru daftar akan muncul di sini sampai diverifikasi.</p>

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Angkatan</th>
                    <th>Jabatan</th>
                    <th>Username</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($unverified) === 0): ?>
                    <tr>
                        <td colspan="7">Tidak ada peserta yang menunggu verifikasi.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($u = mysqli_fetch_assoc($unverified)): ?>
                        <tr>
                            <td><?= $u['id']; ?></td>
                            <td><?= htmlspecialchars($u['nama']); ?></td>
                            <td><?= htmlspecialchars($u['angkatan']); ?></td>
                            <td><?= htmlspecialchars($u['jabatan']); ?></td>
                            <td><?= htmlspecialchars($u['username']); ?></td>
                            <td><?= $u['created_at']; ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                    <button type="submit" name="verify_user" class="btn-verify">Verifikasi</button>
                                </form>
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus akun ini?');">
                                    <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Riwayat Pendaftaran Peserta -->
        <div class="card-box">
            <h2>Riwayat Pendaftaran Peserta</h2>
            <p>Semua akun peserta yang pernah mendaftar, beserta status verifikasinya.</p>

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Angkatan</th>
                    <th>Jabatan</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Tanggal Daftar</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($regHistory) === 0): ?>
                    <tr><td colspan="7">Belum ada peserta yang terdaftar.</td></tr>
                <?php else: ?>
                    <?php while ($r = mysqli_fetch_assoc($regHistory)): ?>
                        <tr>
                            <td><?= $r['id']; ?></td>
                            <td><?= htmlspecialchars($r['nama']); ?></td>
                            <td><?= htmlspecialchars($r['angkatan']); ?></td>
                            <td><?= htmlspecialchars($r['jabatan']); ?></td>
                            <td><?= htmlspecialchars($r['username']); ?></td>
                            <td>
                                <?php if ($r['is_verified']): ?>
                                    <span class="badge paid">Terverifikasi</span>
                                <?php else: ?>
                                    <span class="badge pending">Belum Verifikasi</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $r['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="card-box">
            <h2>Riwayat Pembayaran</h2>
            <p>Riwayat seluruh pembayaran yang tercatat di sistem.</p>

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Peserta</th>
                    <th>Angkatan</th>
                    <th>Jabatan</th>
                    <th>Jenis Pembayaran</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($paymentHistory) === 0): ?>
                    <tr><td colspan="8">Belum ada pembayaran tercatat.</td></tr>
                <?php else: ?>
                    <?php while ($p = mysqli_fetch_assoc($paymentHistory)): ?>
                        <tr>
                            <td><?= $p['id']; ?></td>
                            <td><?= htmlspecialchars($p['nama']); ?></td>
                            <td><?= htmlspecialchars($p['angkatan']); ?></td>
                            <td><?= htmlspecialchars($p['jabatan']); ?></td>
                            <td><?= htmlspecialchars($p['payment_type']); ?></td>
                            <td>Rp <?= number_format($p['amount'], 0, ',', '.'); ?></td>
                            <td><?= $p['payment_date']; ?></td>
                            <td>
                                <span class="badge <?= $p['status']; ?>">
                                    <?= strtoupper($p['status']); ?>
                                </span>
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
