<?php
require 'config/database.php';
require 'includes/auth.php';
require_user();

$page_title = 'Akun Saya';
require 'includes/header.php';

$userId = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, username, role, created_at FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /logout.php');
    exit;
}
?>
<section class="section">
    <div class="account-box">
        <h1>Akun Saya</h1>

        <div class="card account-card">
            <div class="card-body">
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($user['role'])) ?></p>
                <p><strong>Bergabung:</strong> <?= htmlspecialchars(date('d M Y', strtotime($user['created_at']))) ?></p>
            </div>
        </div>

        <div class="account-actions">
            <a class="btn secondary" href="/index.php">Kembali ke Home</a>
            <a class="btn danger" href="/delete_account.php" onclick="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak bisa dibatalkan.')">Hapus Akun</a>
            <a class="btn" href="/logout.php">Logout</a>
        </div>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
