<?php
require '../config/database.php';require '../includes/auth.php';require_admin();
if(isset($_GET['delete'])){
 $id=(int)$_GET['delete'];$s=$pdo->prepare("SELECT thumbnail FROM projects WHERE id=?");$s->execute([$id]);$p=$s->fetch();
 if($p && $p['thumbnail']) @unlink(__DIR__.'/../uploads/'.$p['thumbnail']);
 $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
 header('Location:projects.php');exit;
}
$projects=$pdo->query("SELECT p.*,c.name category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
$page_title='Manage Projects';require '../includes/header.php';
?>
<section class="section"><div class="admin-head"><h1>Projects</h1><a class="btn" href="project_form.php">+ New Project</a></div>
<div class="table-wrap"><table class="table"><tr><th>Title</th><th>Category</th><th>Actions</th></tr>
<?php foreach($projects as $p): ?><tr><td><?=htmlspecialchars($p['title'])?></td><td><?=htmlspecialchars($p['category_name']??'-')?></td><td class="actions"><a class="btn secondary" href="project_form.php?id=<?=$p['id']?>">Edit</a><a class="btn danger" onclick="return confirm('Hapus project ini?')" href="?delete=<?=$p['id']?>">Delete</a></td></tr><?php endforeach;?>
</table></div></section>
<?php require '../includes/footer.php'; ?>
