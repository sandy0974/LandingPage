<?php
require '../config/database.php';require '../includes/auth.php';require_admin();
$id=(int)($_GET['id']??0);
$p=['title'=>'','description'=>'','category_id'=>'','github_url'=>'','demo_url'=>'','thumbnail'=>''];
if($id){$s=$pdo->prepare("SELECT * FROM projects WHERE id=?");$s->execute([$id]);$p=$s->fetch() ?: $p;}
$cats=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $title=trim($_POST['title']??'');$desc=trim($_POST['description']??'');$cat=(int)($_POST['category_id']??0);
 $github=trim($_POST['github_url']??'');$demo=trim($_POST['demo_url']??'');$thumb=$p['thumbnail'];
 if($title==='')$error='Judul wajib diisi.';
 if(!$error && !empty($_FILES['thumbnail']['name'])){
   $allowed=['jpg','jpeg','png','webp'];$ext=strtolower(pathinfo($_FILES['thumbnail']['name'],PATHINFO_EXTENSION));
   if(!in_array($ext,$allowed,true))$error='Format gambar harus JPG, JPEG, PNG, atau WEBP.';
   elseif($_FILES['thumbnail']['size']>3*1024*1024)$error='Ukuran gambar maksimal 3MB.';
   else{$thumb=bin2hex(random_bytes(8)).'.'.$ext;move_uploaded_file($_FILES['thumbnail']['tmp_name'],__DIR__.'/../uploads/'.$thumb);}
 }
 if(!$error){
   if($id){$q=$pdo->prepare("UPDATE projects SET title=?,description=?,category_id=?,github_url=?,demo_url=?,thumbnail=? WHERE id=?");$q->execute([$title,$desc,$cat?:null,$github,$demo,$thumb,$id]);}
   else{$q=$pdo->prepare("INSERT INTO projects(title,description,category_id,github_url,demo_url,thumbnail) VALUES(?,?,?,?,?,?)");$q->execute([$title,$desc,$cat?:null,$github,$demo,$thumb]);}
   header('Location:projects.php');exit;
 }
}
$page_title=$id?'Edit Project':'New Project';require '../includes/header.php';
?>
<section class="section"><h1><?=$id?'Edit':'New'?> Project</h1>
<?php if($error):?><div class="alert"><?=$error?></div><?php endif;?>
<form class="form" method="post" enctype="multipart/form-data">
<label>Title</label><input name="title" value="<?=htmlspecialchars($p['title'])?>" required>
<label>Category</label><select name="category_id"><option value="">-- None --</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=$p['category_id']==$c['id']?'selected':''?>><?=htmlspecialchars($c['name'])?></option><?php endforeach;?></select>
<label>Description</label><textarea name="description"><?=htmlspecialchars($p['description'])?></textarea>
<label>GitHub URL</label><input type="url" name="github_url" value="<?=htmlspecialchars($p['github_url'])?>">
<label>Demo URL</label><input type="url" name="demo_url" value="<?=htmlspecialchars($p['demo_url'])?>">
<label>Thumbnail</label><input type="file" name="thumbnail" accept=".jpg,.jpeg,.png,.webp">
<br><br><button class="btn">Save Project</button> <a class="btn secondary" href="projects.php">Cancel</a>
</form></section>
<?php require '../includes/footer.php'; ?>
