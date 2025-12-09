<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "peserta") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$user_id = (int)$_SESSION['user_id'];

// pastikan sudah punya data anggota
$cekAnggota = mysqli_query($koneksi, "SELECT id FROM anggota WHERE user_id = $user_id LIMIT 1");
if (mysqli_num_rows($cekAnggota) === 0) {
    header("Location: lengkapi_profil.php?from=pembayaran_online.php");
    exit();
}

$opsi = [
    'iuran' => 'Uang Iuran',
    'pdh'   => 'Uang PDH',
    'dll'   => 'DLL'
];

$successMsg = '';
$errorMsg   = '';

// proses submit pembayaran online
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenisKey = $_POST['jenis_pembayaran'] ?? '';
    $payment_type = $opsi[$jenisKey] ?? null;
    $amount = (int)($_POST['amount'] ?? 0);
    $payment_date = date('Y-m-d'); // bisa diganti input manual kalau mau

    if (!$payment_type || $amount <= 0) {
        $errorMsg = "Jenis pembayaran dan nominal wajib diisi dengan benar.";
    } else {
        // upload bukti tf
        $buktiPath = null;
        if (isset($_FILES['bukti_tf']) && $_FILES['bukti_tf']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['bukti_tf']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
            if (in_array($ext, $allow)) {
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }
                $namaFile = 'buktitf_' . $user_id . '_' . time() . '.' . $ext;
                $target = 'uploads/' . $namaFile;
                if (move_uploaded_file($_FILES['bukti_tf']['tmp_name'], $target)) {
                    $buktiPath = $target;
                } else {
                    $errorMsg = "Gagal mengupload bukti transfer.";
                }
            } else {
                $errorMsg = "Format file tidak didukung. Gunakan jpg, png, gif, webp, atau pdf.";
            }
        } else {
            $errorMsg = "Bukti transfer wajib diupload.";
        }

        if ($errorMsg === '') {
            $payment_type_esc = mysqli_real_escape_string($koneksi, $payment_type);
            $bukti_esc        = $buktiPath ? mysqli_real_escape_string($koneksi, $buktiPath) : null;

            $sql = "
                INSERT INTO payments (user_id, amount, payment_date, payment_type, bukti_tf, status)
                VALUES (
                    $user_id,
                    $amount,
                    '$payment_date',
                    '$payment_type_esc',
                    " . ($bukti_esc ? "'$bukti_esc'" : "NULL") . ",
                    'pending'
                )
            ";

            if (mysqli_query($koneksi, $sql)) {
                $successMsg = "Pembayaran berhasil dikirim. Tunggu konfirmasi dari bendahara.";
            } else {
                $errorMsg = "Gagal menyimpan data: " . mysqli_error($koneksi);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu Pembayaran Online</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <div class="card-box">
            <h2>Menu Pembayaran Online</h2>
            <p>Kamu bisa mengirim bukti transfer untuk jenis pembayaran tertentu. Admin atau bendahara akan mengecek dan mengubah status pembayaranmu.</p>

            <?php if ($successMsg): ?>
                <div class="alert success"><?= htmlspecialchars($successMsg); ?></div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div class="alert error"><?= htmlspecialchars($errorMsg); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-box">
                <div style="margin-bottom:10px;">
                    <label>Jenis Pembayaran</label><br>
                    <select name="jenis_pembayaran" required>
                        <option value="">Pilih jenis pembayaran...</option>
                        <option value="iuran">Uang Iuran</option>
                        <option value="pdh">Uang PDH</option>
                        <option value="dll">DLL</option>
                    </select>
                </div>

                <div style="margin-bottom:10px;">
                    <label>Nominal (Rp)</label><br>
                    <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 50000">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Upload Bukti Transfer</label><br>
                    <input type="file" name="bukti_tf" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" required>
                    <small>Format: JPG, PNG, GIF, WEBP, atau PDF.</small>
                </div>

                <button type="submit" class="btn-primary">Kirim Pembayaran</button>
            </form>
        </div>

        <?php include "footer.php"; ?>
    </div>
</div>

</body>
</html>
