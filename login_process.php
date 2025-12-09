<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$username_esc = mysqli_real_escape_string($koneksi, $username);

$data = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username_esc'");
$user = mysqli_fetch_assoc($data);

if ($user) {
    $passwordMatch = false;

    if (!empty($user['password']) && strlen($user['password']) > 20) {
        if (password_verify($password, $user['password'])) {
            $passwordMatch = true;
        }
    }

    if (!$passwordMatch && $password === $user['password']) {
        $passwordMatch = true;
    }

    if ($passwordMatch) {

        if ($user['role'] === 'peserta' && (int)$user['is_verified'] === 0) {
            header("Location: login.php?msg=belum_verifikasi");
            exit();
        }

        $_SESSION['status']  = "login";
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];

        if ($user['role'] === "admin") {
            header("Location: admin_dashboard.php?msg=success_login");
        } else {
            header("Location: participant_dashboard.php?msg=success_login");
        }
        exit();
    }
}

header("Location: login.php?msg=gagal");
exit();
?>
