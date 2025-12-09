<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <h1>Sistem Pembayaran HMJ</h1>
    <?php if (!empty($_SESSION['nama'])): ?>
        <p>Hai, <?= htmlspecialchars($_SESSION['nama']); ?> 👋</p>
    <?php else: ?>
        <p>Silakan login untuk mengakses sistem.</p>
    <?php endif; ?>
</header>
