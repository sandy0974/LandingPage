<?php
require '../config/database.php';
$page_title='Projects';
require '../includes/header.php';
$projects=$pdo->query("SELECT p.*,c.name category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC")->fetchAll();
?>
<section class="section"><h1>Projects</h1><p class="muted">Daftar semua project.</p>
<div class="grid">
<?php foreach($projects as $p): ?>
<article class="card">
<?php if($p['thumbnail']): ?><img src="/uploads/<?=htmlspecialchars($p['thumbnail'])?>" alt=""><?php endif; ?>
<div class="card-body"><span class="tag"><?=htmlspecialchars($p['category_name']??'Uncategorized')?></span><h3><?=htmlspecialchars($p['title'])?></h3><p class="muted"><?=htmlspecialchars(mb_strimwidth($p['description'],0,180,'...'))?></p><a href="detail.php?id=<?=$p['id']?>">Detail →</a></div>
</article>
<?php endforeach; ?>
</div>
</section>
<?php require '../includes/footer.php'; ?>
