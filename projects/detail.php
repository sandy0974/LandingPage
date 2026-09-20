<?php
require '../config/database.php';
$id=(int)($_GET['id']??0);
$stmt=$pdo->prepare("SELECT p.*,c.name category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=?");
$stmt->execute([$id]); $p=$stmt->fetch();
if(!$p){http_response_code(404);exit('Project not found');}
$page_title=$p['title'];
require '../includes/header.php';
?>
<section class="section">
<?php if($p['thumbnail']): ?><img class="detail-img" src="/uploads/<?=htmlspecialchars($p['thumbnail'])?>" alt=""><?php endif; ?>
<span class="tag"><?=htmlspecialchars($p['category_name']??'Uncategorized')?></span>
<h1><?=htmlspecialchars($p['title'])?></h1>
<p class="muted"><?=nl2br(htmlspecialchars($p['description']))?></p>
<div class="actions">
<?php if($p['github_url']): ?><a class="btn secondary" href="<?=htmlspecialchars($p['github_url'])?>" target="_blank" rel="noopener">GitHub</a><?php endif; ?>
<?php if($p['demo_url']): ?><a class="btn" href="<?=htmlspecialchars($p['demo_url'])?>" target="_blank" rel="noopener">Live Demo</a><?php endif; ?>
</div>
</section>
<?php require '../includes/footer.php'; ?>
