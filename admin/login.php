<?php
require '../config/database.php';
if(session_status()===PHP_SESSION_NONE)session_start();
if(!empty($_SESSION['user_id'])){header('Location:/');exit;}
$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $action=$_POST['action']??'login';
 if($action==='signup'){
  $username=trim($_POST['username']??'');$password=$_POST['password']??'';$confirm=$_POST['password_confirm']??'';
  if(strlen($username)<3)$error='Username minimal 3 karakter.';
  elseif(strlen($password)<6)$error='Password minimal 6 karakter.';
  elseif($password!==$confirm)$error='Konfirmasi password tidak sama.';
  else{
   $check=$pdo->prepare("SELECT id FROM users WHERE username=? LIMIT 1");$check->execute([$username]);
   if($check->fetch())$error='Username sudah digunakan.';
   else{$stmt=$pdo->prepare("INSERT INTO users (username,password) VALUES (?,?)");$stmt->execute([$username,password_hash($password,PASSWORD_DEFAULT)]);$success='Akun berhasil dibuat. Silakan login.';}
  }
 }else{
  $username=trim($_POST['username']??'');$password=$_POST['password']??'';$stmt=$pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");$stmt->execute([$username]);$u=$stmt->fetch();
  if($u&&password_verify($password,$u['password'])){session_regenerate_id(true);$_SESSION['user_id']=$u['id'];$_SESSION['user_username']=$u['username'];header('Location:/');exit;}
  $error='Username atau password salah.';
 }
}
$page_title='Login';require '../includes/header.php';
?>
<section class="auth-section"><div class="auth-card"><div class="auth-header"><span class="eyebrow">Portfolio Account</span><h1 id="authTitle">Welcome back.</h1><p id="authSubtitle">Login untuk mengakses akun Anda.</p></div>
<?php if($error):?><div class="alert"><?=htmlspecialchars($error)?></div><?php endif;?><?php if($success):?><div class="success"><?=htmlspecialchars($success)?></div><?php endif;?>
<form class="form auth-form" method="post" id="loginForm"><input type="hidden" name="action" value="login"><label>Username</label><input name="username" autocomplete="username" required><label>Password</label><input type="password" name="password" autocomplete="current-password" required><button class="btn" type="submit">Login</button></form>
<form class="form auth-form" method="post" id="signupForm" style="display:none"><input type="hidden" name="action" value="signup"><label>Username</label><input name="username" minlength="3" autocomplete="username" required><label>Password</label><input type="password" name="password" minlength="6" autocomplete="new-password" required><label>Konfirmasi Password</label><input type="password" name="password_confirm" minlength="6" autocomplete="new-password" required><button class="btn" type="submit">Create Account</button></form>
<div class="auth-switch"><span id="switchText">Belum punya akun?</span><button type="button" id="switchAuth">Sign up</button></div></div></section>
<style>.auth-section{min-height:70vh;display:grid;place-items:center;padding:50px 20px}.auth-card{width:min(100%,440px);padding:32px;border:1px solid rgba(255,255,255,.1);border-radius:22px;background:rgba(255,255,255,.035);box-shadow:0 25px 70px rgba(0,0,0,.25)}.auth-header{margin-bottom:24px}.auth-header h1{margin:8px 0}.auth-form{gap:10px}.auth-form label{margin-top:8px}.auth-switch{display:flex;justify-content:center;gap:6px;margin-top:22px;font-size:.92rem}.auth-switch button{padding:0;border:0;background:none;color:inherit;text-decoration:underline;cursor:pointer}.success{margin-bottom:18px;padding:12px 14px;border-radius:10px;background:rgba(80,200,120,.12)}</style>
<script>const lf=document.getElementById('loginForm'),sf=document.getElementById('signupForm'),sw=document.getElementById('switchAuth'),st=document.getElementById('switchText'),at=document.getElementById('authTitle'),as=document.getElementById('authSubtitle');let s=false;sw.addEventListener('click',()=>{s=!s;lf.style.display=s?'none':'';sf.style.display=s?'':'none';at.textContent=s?'Create your account.':'Welcome back.';as.textContent=s?'Buat akun user biasa untuk mengakses portfolio.':'Login untuk mengakses akun Anda.';st.textContent=s?'Sudah punya akun?':'Belum punya akun?';sw.textContent=s?'Login':'Sign up'});</script>
<?php require '../includes/footer.php'; ?>
