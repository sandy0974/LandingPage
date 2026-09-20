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

<style>
/* =========================================================
   RESPONSIVE NAV (scoped to header)
   Catatan: ini ditambahkan di sini karena style.css tidak
   tersedia untuk diedit langsung. Kalau kamu punya style.css,
   lebih rapi kalau blok ini dipindah ke sana.
========================================================= */

.nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 20px;
    position: relative;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 18px;
}

.nav-toggle {
    display: none;
    background: none;
    border: 0;
    cursor: pointer;
    padding: 6px;
    z-index: 20;
}

.nav-toggle span {
    display: block;
    width: 24px;
    height: 2px;
    background: currentColor;
    margin: 5px 0;
    transition: transform .2s ease, opacity .2s ease;
}

@media (max-width: 700px) {

    .nav-toggle {
        display: block;
    }

    .nav-links {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;

        flex-direction: column;
        align-items: flex-start;
        gap: 2px;

        padding: 10px 20px 16px;

        background: inherit;
        border-top: 1px solid rgba(255,255,255,.08);

        max-height: 0;
        overflow: hidden;
        opacity: 0;

        transition: max-height .25s ease, opacity .2s ease;
    }

    .nav-links.open {
        max-height: 300px;
        opacity: 1;
    }

    .nav-links a {
        width: 100%;
        padding: 10px 0;
    }

    .nav-toggle.open span:nth-child(1) {
        transform: translateY(7px) rotate(45deg);
    }

    .nav-toggle.open span:nth-child(2) {
        opacity: 0;
    }

    .nav-toggle.open span:nth-child(3) {
        transform: translateY(-7px) rotate(-45deg);
    }
}
</style>

</head>
<body>
<nav class="nav">
  <a class="brand" href="/index.php">MY<span>PORTFOLIO</span></a>

  <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
    <span></span>
    <span></span>
    <span></span>
  </button>

  <div class="nav-links" id="navLinks">
    <a href="/index.php">Home</a>
    <a href="/projects/index.php">Projects</a>
    <?php if (!empty($_SESSION['admin_id'])): ?>
      <a href="/admin/index.php">Admin</a>
      <a href="/admin/logout.php">Logout</a>
    <?php else: ?>
      <a href="/admin/login.php">Login</a>
    <?php endif; ?>
  </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('navToggle');
    const links = document.getElementById('navLinks');

    if (!toggle || !links) return;

    toggle.addEventListener('click', function () {
        const isOpen = links.classList.toggle('open');
        toggle.classList.toggle('open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    links.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
            links.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
});
</script>

<main class="container">
