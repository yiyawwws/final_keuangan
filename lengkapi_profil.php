<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "peserta") {
    header("Location: login.php");
    exit();
}

include "koneksi.php";

$user_id = (int)$_SESSION['user_id'];

// Cek apakah sudah punya data anggota
$cekAnggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE user_id = $user_id LIMIT 1");
$anggota = mysqli_fetch_assoc($cekAnggota);

// Ambil data dari tabel users untuk prefill
$uRes = mysqli_query($koneksi, "SELECT nama, angkatan, jabatan FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($uRes);
$namaUser = $user['nama'] ?? '';
$angkatanUser = $user['angkatan'] ?? '';
$jabatanUser = $user['jabatan'] ?? '';

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama'] ?? '');
    $angkatan = mysqli_real_escape_string($koneksi, $_POST['angkatan'] ?? '');
    $jabatan  = mysqli_real_escape_string($koneksi, $_POST['jabatan'] ?? '');
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp'] ?? '');
    $divisi   = mysqli_real_escape_string($koneksi, $_POST['divisi'] ?? '');

    // Upload foto kalau ada
    $fotoPath = $anggota['foto'] ?? null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allow)) {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            $namaFile = 'anggota_' . $user_id . '_' . time() . '.' . $ext;
            $target = 'uploads/' . $namaFile;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
                $fotoPath = $target;
            }
        }
    }

    if ($anggota) {
        // UPDATE
        $sql = "
            UPDATE anggota SET
                nama = '$nama',
                no_hp = '$no_hp',
                angkatan = '$angkatan',
                jabatan = '$jabatan',
                divisi = '$divisi',
                foto = " . ($fotoPath ? "'" . mysqli_real_escape_string($koneksi, $fotoPath) . "'" : "foto") . "
            WHERE user_id = $user_id
        ";
    } else {
        // INSERT
        $sql = "
            INSERT INTO anggota (user_id, nama, no_hp, angkatan, jabatan, divisi, foto)
            VALUES (
                $user_id,
                '$nama',
                '$no_hp',
                '$angkatan',
                '$jabatan',
                '$divisi',
                " . ($fotoPath ? "'" . mysqli_real_escape_string($koneksi, $fotoPath) . "'" : "NULL") . "
            )
        ";
    }

    if (mysqli_query($koneksi, $sql)) {
        // Kalau ada parameter from, balikin ke halaman asal, kalau tidak ke dashboard peserta
        $redirect = isset($_GET['from']) ? $_GET['from'] : 'participant_dashboard.php';
        header("Location: " . $redirect);
        exit();
    } else {
        $errorMsg = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lengkapi Data Anggota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <div class="card-box">
            <h2>Lengkapi Data Anggota HMJ</h2>
            <p>Sebelum mengakses menu pembayaran, silakan lengkapi data HMJ kamu terlebih dahulu.</p>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert error"><?= htmlspecialchars($errorMsg); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-box">
                <div style="margin-bottom:10px;">
                    <label>Nama Lengkap</label><br>
                    <input type="text" name="nama" required
                           value="<?= htmlspecialchars($anggota['nama'] ?? $namaUser); ?>">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Angkatan</label><br>
                    <input type="text" name="angkatan" required
                           value="<?= htmlspecialchars($anggota['angkatan'] ?? $angkatanUser); ?>">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Jabatan</label><br>
                    <select name="jabatan" required>
                        <?php
                        $opsiJabatan = [
                            'Ketua Himpunan',
                            'Wakil Ketua Himpunan',
                            'Bendahara',
                            'Sekertaris',
                            'Anggota Kaderisasi',
                            'Angota Kominfo',
                            'Anggota Humas',
                            'Anggota Keilmuan'
                        ];
                        $currentJabatan = $anggota['jabatan'] ?? $jabatanUser;
                        ?>
                        <option value="">Pilih jabatan...</option>
                        <?php foreach ($opsiJabatan as $jb): ?>
                            <option value="<?= $jb; ?>" <?= ($currentJabatan === $jb ? 'selected' : ''); ?>>
                                <?= $jb; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom:10px;">
                    <label>No HP</label><br>
                    <input type="text" name="no_hp" required
                           value="<?= htmlspecialchars($anggota['no_hp'] ?? ''); ?>">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Divisi</label><br>
                    <input type="text" name="divisi" required
                           placeholder="Misal: Kominfo, Kaderisasi, Humas..."
                           value="<?= htmlspecialchars($anggota['divisi'] ?? ''); ?>">
                </div>

                <div style="margin-bottom:10px;">
                    <label>Foto Profil (opsional)</label><br>
                    <input type="file" name="foto" accept="image/*">
                    <?php if (!empty($anggota['foto'])): ?>
                        <div style="margin-top:8px;">
                            <img src="<?= htmlspecialchars($anggota['foto']); ?>"
                                 alt="Foto anggota" style="max-width:120px;border-radius:10px;">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">Simpan</button>
            </form>
        </div>

        <?php include "footer.php"; ?>
    </div>
</div>

</body>
</html>
