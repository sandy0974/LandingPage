<?php
require '../config/database.php';
if(session_status()===PHP_SESSION_NONE)session_start();
if(!empty($_SESSION['admin_id'])){header('Location:index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $stmt=$pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
 $stmt->execute([trim($_POST['username']??'')]);$u=$stmt->fetch();
 if($u && password_verify($_POST['password']??'',$u['password'])){
   session_regenerate_id(true);$_SESSION['admin_id']=$u['id'];$_SESSION['admin_username']=$u['username'];
   header('Location:index.php');exit;
 }
 $error='Username atau password salah.';
}
$page_title='Admin Login';require '../includes/header.php';
?>
<section class="section"><h1>Admin Login</h1>
<?php if($error):?><div class="alert"><?=$error?></div><?php endif;?>
<form class="form" method="post">
<label>Username</label><input name="username" required>
<label>Password</label><input type="password" name="password" required>
<br><br><button class="btn">Login</button>
</form></section>
<?php require '../includes/footer.php'; ?>
