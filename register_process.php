<?php
session_start();
include "koneksi.php";

// Hanya boleh lewat method POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: register.php");
    exit;
}

// Ambil data dari form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$nama     = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$angkatan = isset($_POST['angkatan']) ? trim($_POST['angkatan']) : '';
$jabatan  = isset($_POST['jabatan']) ? trim($_POST['jabatan']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validasi sederhana
if ($username === '' || $nama === '' || $angkatan === '' || $jabatan === '' || $password === '') {
    echo "<script>alert('Semua field wajib diisi'); window.location='register.php';</script>";
    exit;
}

// Bersihkan data sebelum dipakai di query
$username_esc = mysqli_real_escape_string($koneksi, $username);
$nama_esc     = mysqli_real_escape_string($koneksi, $nama);
$angkatan_esc = mysqli_real_escape_string($koneksi, $angkatan);
$jabatan_esc  = mysqli_real_escape_string($koneksi, $jabatan);

// Cek apakah username sudah terdaftar
$cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username_esc'");
if (!$cek) {
    die("Query cek username error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah dipakai. Silakan gunakan username lain.'); window.location='register.php';</script>";
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);
$hash_esc = mysqli_real_escape_string($koneksi, $hash);

// Kita tidak pakai email lagi, isi kosong saja
$email_esc = "";

// Query insert user baru dengan role peserta, belum diverifikasi
$sql = "
    INSERT INTO users (nama, angkatan, jabatan, username, email, password, role, is_verified)
    VALUES (
        '$nama_esc',
        '$angkatan_esc',
        '$jabatan_esc',
        '$username_esc',
        '$email_esc',
        '$hash_esc',
        'peserta',
        0
    )
";

$result = mysqli_query($koneksi, $sql);

if ($result) {
    // Berhasil, arahkan ke login dengan pesan sukses
    header("Location: login.php?msg=registrasi_berhasil");
    exit;
} else {
    // Kalau ada error di query, tampilkan biar gampang debug
    echo "Error saat menyimpan data: " . mysqli_error($koneksi);
    exit;
}
?>
