<?php
require 'config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$page_title='My Portfolio';
$stmt=$pdo->query("SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC LIMIT 12");
$projects=$stmt->fetchAll();
$assetRoot=__DIR__.'/uploads'; $models=[];
if(is_dir($assetRoot)){
 $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($assetRoot,FilesystemIterator::SKIP_DOTS));
 foreach($it as $file){
  if(!$file->isFile() || !in_array(strtolower($file->getExtension()),['glb','gltf'],true)) continue;
  $rel=str_replace('\\','/',substr($file->getPathname(),strlen($assetRoot)+1));
  $models[]=['name'=>pathinfo($rel,PATHINFO_FILENAME),'url'=>'/uploads/'.implode('/',array_map('rawurlencode',explode('/',$rel)))];
 }
}
usort($models,fn($a,$b)=>strcasecmp($a['name'],$b['name']));
$featured=$models[0]??null;
require 'includes/header.php';
?>
<section class="hero model-hero">
 <div class="model-copy">
  <div class="eyebrow">3D Artist • Game Dev • Portfolio</div>
  <h1>Build. Create.<br>Share.</h1>
  <p>Portfolio pribadi untuk menampilkan project, game, 3D model, asset, desain, dan eksperimen lainnya.</p>
  <div class="hero-actions"><a class="btn" href="#projects">Lihat Projects</a><a class="btn btn-secondary" href="#assets">Explore 3D Assets</a></div>
 </div>
 <div class="model-viewer-card">
 <?php if($featured): ?>
  <model-viewer id="mainModel" src="<?=htmlspecialchars($featured['url'])?>" alt="<?=htmlspecialchars($featured['name'])?>" camera-controls auto-rotate shadow-intensity="1" exposure="1" environment-image="neutral" interaction-prompt="none" style="width:100%;height:520px;background:transparent"></model-viewer>
  <div class="model-toolbar"><span id="modelName"><?=htmlspecialchars($featured['name'])?></span><button type="button" id="resetModel">Reset View</button></div>
 <?php else: ?><div class="model-empty"><div><strong>3D Asset Viewer</strong><p>Belum ada model 3D.</p><small>Upload file <b>.glb</b> atau <b>.gltf</b> ke <code>/uploads/assets/</code>.</small></div></div><?php endif; ?>
 </div>
</section>
<?php if(count($models)>1): ?>
<section class="section" id="assets"><div class="section-heading"><div><span class="eyebrow">3D Library</span><h2>Featured Assets</h2></div><span class="muted"><?=count($models)?> model</span></div>
 <div class="asset-carousel">
 <?php foreach($models as $i=>$m): ?><button type="button" class="asset-card <?=$i===0?'active':''?>" data-model="<?=htmlspecialchars($m['url'])?>" data-name="<?=htmlspecialchars($m['name'])?>"><div class="asset-preview"><model-viewer src="<?=htmlspecialchars($m['url'])?>" alt="<?=htmlspecialchars($m['name'])?>" camera-controls disable-zoom interaction-prompt="none" style="width:100%;height:190px;background:transparent"></model-viewer></div><div class="asset-card-body"><strong><?=htmlspecialchars($m['name'])?></strong><span>3D Model</span></div></button><?php endforeach; ?>
 </div>
</section>
<?php endif; ?>
<section class="section" id="projects"><div class="section-heading"><div><span class="eyebrow">Selected Work</span><h2>Projects</h2></div><a href="/projects/index.php">View all →</a></div>
 <?php if($projects): ?><div class="project-carousel">
 <?php foreach($projects as $p): ?><article class="project-card"><?php if(!empty($p['thumbnail'])): ?><img src="/uploads/<?=htmlspecialchars($p['thumbnail'])?>" alt="<?=htmlspecialchars($p['title'])?>" loading="lazy"><?php else: ?><div class="project-placeholder">PROJECT</div><?php endif; ?><div class="project-card-body"><span class="tag"><?=htmlspecialchars($p['category_name']??'Uncategorized')?></span><h3><?=htmlspecialchars($p['title'])?></h3><p class="muted"><?=htmlspecialchars(mb_strimwidth($p['description']??'',0,150,'...'))?></p><a href="/projects/detail.php?id=<?=(int)$p['id']?>">View Project →</a></div></article><?php endforeach; ?>
 </div><?php else: ?><div class="empty">Belum ada project.</div><?php endif; ?>
</section>
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
<style>
.model-hero{display:grid;grid-template-columns:minmax(0,.9fr) minmax(420px,1.1fr);gap:32px;align-items:center}.model-copy{padding:30px 0}.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:24px}.btn-secondary{background:transparent;border:1px solid rgba(255,255,255,.18)}
.model-viewer-card{min-height:520px;overflow:hidden;border:1px solid rgba(255,255,255,.1);border-radius:24px;background:radial-gradient(circle at 50% 35%,rgba(255,255,255,.08),transparent 45%),rgba(255,255,255,.025);box-shadow:0 25px 70px rgba(0,0,0,.28)}.model-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:13px 17px;border-top:1px solid rgba(255,255,255,.08)}.model-toolbar button{border:0;border-radius:9px;padding:8px 12px;cursor:pointer}.model-empty{min-height:520px;display:grid;place-items:center;text-align:center;padding:30px}
.section-heading{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:22px}.asset-carousel,.project-carousel{display:flex;gap:18px;overflow-x:auto;padding:4px 2px 16px;scroll-snap-type:x mandatory;scrollbar-width:thin}.asset-card,.project-card{flex:0 0 285px;scroll-snap-align:start;overflow:hidden;border:1px solid rgba(255,255,255,.09);border-radius:18px;background:rgba(255,255,255,.035)}.asset-card{padding:0;text-align:left;color:inherit;cursor:pointer;transition:transform .2s,border-color .2s}.asset-card:hover,.asset-card.active{transform:translateY(-4px);border-color:rgba(255,255,255,.28)}.asset-preview{background:radial-gradient(circle at 50% 40%,rgba(255,255,255,.08),transparent 55%)}.asset-card-body,.project-card-body{padding:16px}.asset-card-body strong{display:block;margin-bottom:5px}.asset-card-body span{font-size:.82rem;opacity:.6}.project-card{flex-basis:340px}.project-card img,.project-placeholder{width:100%;height:205px;object-fit:cover;display:block}.project-placeholder{display:grid;place-items:center;background:rgba(255,255,255,.06);font-weight:700;letter-spacing:.12em}.project-card h3{margin:10px 0 8px}.project-card-body>a{display:inline-block;margin-top:12px}
@media(max-width:850px){.model-hero{grid-template-columns:1fr}.model-viewer-card{min-height:420px}.model-viewer-card model-viewer{height:420px!important}}
</style>
<script>document.addEventListener('DOMContentLoaded',()=>{const v=document.getElementById('mainModel'),n=document.getElementById('modelName'),r=document.getElementById('resetModel');document.querySelectorAll('.asset-card').forEach(c=>c.addEventListener('click',()=>{if(!v)return;v.src=c.dataset.model;if(n)n.textContent=c.dataset.name;document.querySelectorAll('.asset-card').forEach(x=>x.classList.remove('active'));c.classList.add('active')}));if(r&&v)r.addEventListener('click',()=>{v.cameraOrbit='auto auto auto';v.fieldOfView='auto'})});</script>
<?php require 'includes/footer.php'; ?>
