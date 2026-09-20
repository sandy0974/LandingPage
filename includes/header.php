<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($page_title ?? 'My Portfolio') ?></title>
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="/index.php">MY<span>PORTFOLIO</span></a>
  <div class="nav-links">
    <a href="/index.php">Home</a>
    <a href="/projects/index.php">Projects</a>
    <?php if (!empty($_SESSION['admin_id'])): ?>
      <a href="/admin/index.php">Admin</a>
      <a href="/admin/logout.php">Logout</a>
    <?php else: ?>
      <a href="/admin/login.php">Admin Login</a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
