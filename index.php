<?php
require 'config/database.php';
$page_title='My Portfolio';
require 'includes/header.php';
$stmt=$pdo->query("SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC LIMIT 6");
$projects=$stmt->fetchAll();
?>
<section class="hero">
  <div class="eyebrow">Portfolio • Projects • Assets</div>
  <h1>Build. Create.<br>Share.</h1>
  <p>Website portfolio pribadi untuk menampilkan project, game, desain, asset, CNC, dan karya lainnya.</p>
  <a class="btn" href="/projects/index.php">Lihat Projects</a>
</section>
<section class="section">
<h2>Latest Projects</h2>
<div class="grid">
<?php foreach($projects as $p): ?>
<article class="card">
  <?php if($p['thumbnail']): ?><img src="/uploads/<?=htmlspecialchars($p['thumbnail'])?>" alt=""><?php endif; ?>
  <div class="card-body">
    <span class="tag"><?=htmlspecialchars($p['category_name']??'Uncategorized')?></span>
    <h3><?=htmlspecialchars($p['title'])?></h3>
    <p class="muted"><?=htmlspecialchars(mb_strimwidth($p['description'],0,150,'...'))?></p>
    <a href="/projects/detail.php?id=<?=$p['id']?>">View Project →</a>
  </div>
</article>
<?php endforeach; ?>
</div>
<?php if(!$projects): ?><div class="empty">Belum ada project.</div><?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
