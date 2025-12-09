<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Anggota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <div class="card-box">
            <h2>Input Data Anggota</h2>

            <form action="anggota_save.php" method="POST" enctype="multipart/form-data" class="form-box">
                <div style="margin-bottom:10px;">
                    <label>Nama</label><br>
                    <input type="text" name="nama" required>
                </div>

                <div style="margin-bottom:10px;">
                    <label>No HP</label><br>
                    <input type="text" name="no_hp" required>
                </div>

                <div style="margin-bottom:10px;">
                    <label>Angkatan</label><br>
                    <input type="text" name="angkatan" required>
                </div>

                <div style="margin-bottom:10px;">
                    <label>Jabatan</label><br>
                    <input type="text" name="jabatan" required>
                </div>

                <div style="margin-bottom:10px;">
                    <label>Divisi</label><br>
                    <input type="text" name="divisi" required>
                </div>

                <div style="margin-bottom:10px;">
                    <label>Foto (opsional)</label><br>
                    <input type="file" name="foto" accept="image/*">
                </div>

                <button type="submit" class="btn-primary">Simpan</button>
                <a href="anggota_list.php" class="btn-secondary" style="margin-left:6px;">Kembali</a>
            </form>
        </div>

        <?php include "footer.php"; ?>
    </div>
</div>

</body>
</html>
