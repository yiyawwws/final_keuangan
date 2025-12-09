<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

$data = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Anggota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wrapper">
    <?php include "sidebar.php"; ?>
    <div class="content">
        <?php include "header.php"; ?>

        <div class="card-box">
            <h2>Data Anggota HMJ</h2>
            <a href="form_anggota.php" class="btn-primary">Tambah Anggota</a>

            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Angkatan</th>
                    <th>Jabatan</th>
                    <th>Divisi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($data) === 0): ?>
                    <tr><td colspan="6">Belum ada data anggota.</td></tr>
                <?php else: ?>
                    <?php while ($d = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $d['id']; ?></td>
                            <td><?= htmlspecialchars($d['nama']); ?></td>
                            <td><?= htmlspecialchars($d['no_hp']); ?></td>
                            <td><?= htmlspecialchars($d['angkatan']); ?></td>
                            <td><?= htmlspecialchars($d['jabatan']); ?></td>
                            <td><?= htmlspecialchars($d['divisi']); ?></td>
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
