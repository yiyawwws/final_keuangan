<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login" || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: form_anggota.php");
    exit();
}

$nama = trim($_POST['nama'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$angkatan = trim($_POST['angkatan'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? '');
$divisi = trim($_POST['divisi'] ?? '');

$fotoNama = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array(strtolower($ext), $allow)) {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        $fotoNama = 'uploads/' . time() . '_' . preg_replace('/[^a-zA-Z0-9\._]/', '', $_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $fotoNama);
    }
}

$sql = "INSERT INTO anggota (nama, no_hp, angkatan, jabatan, divisi, foto) VALUES (
    '" . mysqli_real_escape_string($koneksi, $nama) . "',
    '" . mysqli_real_escape_string($koneksi, $no_hp) . "',
    '" . mysqli_real_escape_string($koneksi, $angkatan) . "',
    '" . mysqli_real_escape_string($koneksi, $jabatan) . "',
    '" . mysqli_real_escape_string($koneksi, $divisi) . "',
    " . ($fotoNama ? "'" . mysqli_real_escape_string($koneksi, $fotoNama) . "'" : "NULL") . "
)";

if (mysqli_query($koneksi, $sql)) {
    header("Location: anggota_list.php");
} else {
    echo "Error: " . mysqli_error($koneksi);
}
exit();
