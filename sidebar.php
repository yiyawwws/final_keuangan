<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="sidebar">
    <h2>Menu</h2>
    <ul>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
            <li><a href="payment.php">Manajemen Pembayaran</a></li>
            <li><a href="anggota_list.php">Kelola Anggota</a></li>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'peserta'): ?>
            <li><a href="participant_dashboard.php">Dashboard Peserta</a></li>
            <li><a href="my_payments.php">Riwayat Pembayaran</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
