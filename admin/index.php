<?php
require '../config/database.php';require '../includes/auth.php';require_admin();
$page_title='Admin Dashboard';require '../includes/header.php';
$count=$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
?>
<section class="section"><div class="admin-head"><div><h1>Dashboard</h1><p class="muted">Login sebagai <?=htmlspecialchars($_SESSION['admin_username'])?></p></div><a class="btn" href="project_form.php">+ New Project</a></div>
<div class="card"><div class="card-body"><h2><?=$count?></h2><p class="muted">Total Projects</p></div></div>
<br><a class="btn secondary" href="projects.php">Manage Projects</a>
</section>
<?php require '../includes/footer.php'; ?>
