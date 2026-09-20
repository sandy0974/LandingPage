<?php
require 'config/database.php';
require 'includes/auth.php';
require_user();

$userId = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$userId]);

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

header('Location: /login.php');
exit;
