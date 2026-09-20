<?php
require 'config/database.php';
require 'includes/auth.php';

if (!empty($_SESSION['user_id'])) {
    redirect_after_login();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        redirect_after_login();
    }

    $error = 'Username atau password salah.';
}

$page_title = 'Login';
require 'includes/header.php';
?>
<section class="section">
    <div class="auth-wrap">
        <h1>Login</h1>
        <p class="muted">Masuk untuk mengakses akun Anda.</p>

        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form class="form" method="post">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <div class="auth-actions">
                <button class="btn" type="submit">Masuk</button>
                <a class="btn secondary" href="/admin/login.php">Login Admin</a>
            </div>
        </form>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
