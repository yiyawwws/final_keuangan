<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

// Handle action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_payment'])) {
        $user_id = (int)$_POST['user_id'];
        $amount = (int)$_POST['amount'];
        $payment_type = mysqli_real_escape_string($koneksi, $_POST['payment_type']);
        $payment_date = mysqli_real_escape_string($koneksi, $_POST['payment_date']);
        mysqli_query(
            $koneksi,
            "INSERT INTO payments (user_id, amount, payment_date, payment_type, status) 
             VALUES ($user_id, $amount, '$payment_date', '$payment_type', 'pending')"
        );
    } elseif (isset($_POST['update_payment'])) {
        $id = (int)$_POST['id'];
        $amount = (int)$_POST['amount'];
        $payment_type = mysqli_real_escape_string($koneksi, $_POST['payment_type']);
        $payment_date = mysqli_real_escape_string($koneksi, $_POST['payment_date']);
        mysqli_query(
            $koneksi,
            "UPDATE payments 
             SET amount = $amount, payment_date = '$payment_date', payment_type = '$payment_type'
             WHERE id = $id"
        );
    } elseif (isset($_POST['delete_payment'])) {
        $id = (int)$_POST['id'];
        mysqli_query($koneksi, "DELETE FROM payments WHERE id = $id");
    } elseif (isset($_POST['change_status'])) {
        $id = (int)$_POST['id'];
        $status = mysqli_real_escape_string($koneksi, $_POST['status']);
        mysqli_query($koneksi, "UPDATE payments SET status = '$status' WHERE id = $id");
    }
}

// Data untuk form select peserta
$pesertaList = mysqli_query($koneksi, "SELECT id, nama FROM users WHERE role='peserta' AND is_verified=1 ORDER BY nama ASC");

// Data pembayaran
$payments = mysqli_query(
    $koneksi,
    "SELECT p.*, u.nama, u.angkatan, u.jabatan 
     FROM payments p 
     JOIN users u ON p.user_id = u.id
     ORDER BY p.created_at DESC"
);

// Data untuk edit jika ada
$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $res = mysqli_query(
        $koneksi,
        "SELECT * FROM payments WHERE id = $editId"
    );
    $editData = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pembayaran - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <div class="card-box">
            <h2><?= $editData ? 'Edit Pembayaran' : 'Tambah Pembayaran Baru (Input Manual)'; ?></h2>
            <p>Form ini digunakan untuk input pembayaran secara manual oleh admin (misal uang cash). Untuk pembayaran online dari peserta, data muncul otomatis dari menu pembayaran online.</p>

            <form method="POST" class="form-box">
                <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?= $editData['id']; ?>">
                <?php endif; ?>

                <div style="margin-bottom:10px;">
                    <label>Peserta</label><br>
                    <select name="user_id" <?= $editData ? 'disabled' : ''; ?> required>
                        <option value="">Pilih peserta...</option>
                        <?php while ($u = mysqli_fetch_assoc($pesertaList)): ?>
                            <option value="<?= $u['id']; ?>"
                                <?php
                                if ($editData && $editData['user_id'] == $u['id']) {
                                    echo 'selected';
                                }
                                ?>
                            >
                                <?= htmlspecialchars($u['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if ($editData): ?>
                        <small style="display:block;margin-top:4px;">Peserta tidak bisa diubah saat edit.</small>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom:10px;">
                    <label>Jumlah (Rp)</label><br>
                    <input type="number" name="amount" required
                           value="<?= $editData ? (int)$editData['amount'] : ''; ?>">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Tanggal Pembayaran</label><br>
                    <input type="date" name="payment_date" required
                           value="<?= $editData ? $editData['payment_date'] : date('Y-m-d'); ?>">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Jenis Pembayaran</label><br>
                    <input type="text" name="payment_type" required
                           value="<?= $editData ? htmlspecialchars($editData['payment_type']) : ''; ?>">
                </div>

                <?php if ($editData): ?>
                    <button type="submit" name="update_payment" class="btn-primary">Simpan Perubahan</button>
                    <a href="payment.php" class="btn-secondary" style="margin-left:6px;">Batal</a>
                <?php else: ?>
                    <button type="submit" name="create_payment" class="btn-primary">Tambah Pembayaran</button>
                <?php endif; ?>
            </form>
        </div>

        <div class="card-box">
            <h2>Daftar Pembayaran (Manual + Online)</h2>
            <p>Jika pembayaran dikirim dari menu pembayaran online peserta, akan ada bukti transfer yang bisa dicek.</p>

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Peserta</th>
                    <th>Angkatan</th>
                    <th>Jabatan</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($payments) === 0): ?>
                    <tr><td colspan="10">Belum ada data pembayaran.</td></tr>
                <?php else: ?>
                    <?php while ($p = mysqli_fetch_assoc($payments)): ?>
                        <tr>
                            <td><?= $p['id']; ?></td>
                            <td><?= htmlspecialchars($p['nama']); ?></td>
                            <td><?= htmlspecialchars($p['angkatan']); ?></td>
                            <td><?= htmlspecialchars($p['jabatan']); ?></td>
                            <td><?= htmlspecialchars($p['payment_type']); ?></td>
                            <td>Rp <?= number_format($p['amount'], 0, ',', '.'); ?></td>
                            <td><?= $p['payment_date']; ?></td>
                            <td>
                                <?php if (!empty($p['bukti_tf'])): ?>
                                    <a href="<?= htmlspecialchars($p['bukti_tf']); ?>" target="_blank">Lihat Bukti</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?= $p['status']; ?>">
                                    <?= strtoupper($p['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="payment.php?edit=<?= $p['id']; ?>" class="btn-edit">Edit</a>

                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus pembayaran ini?');">
                                    <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                    <button type="submit" name="delete_payment" class="btn-delete">Hapus</button>
                                </form>

                                <form method="POST" style="display:inline-block;margin-top:4px;">
                                    <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?= $p['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="paid" <?= $p['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="rejected" <?= $p['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <input type="hidden" name="change_status" value="1">
                                </form>
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
