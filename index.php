<?php
require 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'My Portfolio';

/* =========================
   PROJECTS
========================= */
$stmt = $pdo->query("
    SELECT 
        p.*,
        c.name AS category_name
    FROM projects p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.created_at DESC
    LIMIT 12
");

$projects = $stmt->fetchAll();

/* =========================
   3D MODELS
========================= */
$assetRoot = __DIR__ . '/uploads';
$models = [];

if (is_dir($assetRoot)) {

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $assetRoot,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($it as $file) {

        if (
            !$file->isFile() ||
            !in_array(
                strtolower($file->getExtension()),
                ['glb', 'gltf'],
                true
            )
        ) {
            continue;
        }

        $rel = str_replace(
            '\\',
            '/',
            substr(
                $file->getPathname(),
                strlen($assetRoot) + 1
            )
        );

        $models[] = [
            'name' => pathinfo($rel, PATHINFO_FILENAME),
            'url'  => '/uploads/' . implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode('/', $rel)
                )
            )
        ];
    }
}

usort(
    $models,
    fn($a, $b) => strcasecmp($a['name'], $b['name'])
);

$featured = $models[0] ?? null;

require 'includes/header.php';
?>

<!-- =========================================================
     HERO
========================================================= -->

<section class="hero model-hero">

    <div class="model-copy">

        <div class="eyebrow">
            3D Artist • Game Dev • Portfolio
        </div>

        <h1>
            Build. Create.<br>
            Share.
        </h1>

        <p>
            Portfolio pribadi untuk menampilkan project,
            game, 3D model, asset, desain, dan eksperimen lainnya.
        </p>

        <div class="hero-actions">

            <a class="btn" href="#projects">
                Lihat Projects
            </a>

            <a class="btn btn-secondary" href="#assets">
                Explore 3D Assets
            </a>

        </div>

    </div>

</section>


<!-- =========================================================
     3D MODEL VIEWER
========================================================= -->

<section class="model-section">

    <div class="model-viewer-card">

        <?php if ($featured): ?>

            <model-viewer
                id="mainModel"
                src="<?= htmlspecialchars($featured['url']) ?>"
                alt="<?= htmlspecialchars($featured['name']) ?>"
                camera-controls
                auto-rotate
                shadow-intensity="1"
                exposure="1"
                environment-image="neutral"
                interaction-prompt="none"
                camera-orbit="0deg 75deg 105%"
                field-of-view="auto">
            </model-viewer>

            <div class="model-toolbar">

                <div class="model-info">
                    <span class="model-label">
                        3D MODEL
                    </span>

                    <strong id="modelName">
                        <?= htmlspecialchars($featured['name']) ?>
                    </strong>
                </div>

                <button
                    type="button"
                    id="resetModel">
                    Reset View
                </button>

            </div>

        <?php else: ?>

            <div class="model-empty">

                <div>

                    <strong>
                        3D Asset Viewer
                    </strong>

                    <p>
                        Belum ada model 3D.
                    </p>

                    <small>
                        Upload file
                        <b>.glb</b> atau
                        <b>.gltf</b>
                        ke folder
                        <code>/uploads/</code>.
                    </small>

                </div>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- =========================================================
     PROJECTS
========================================================= -->

<section class="section" id="projects">

    <div class="section-heading">

        <div>

            <span class="eyebrow">
                Selected Work
            </span>

            <h2>
                Projects
            </h2>

        </div>

        <a href="/projects/index.php">
            View all →
        </a>

    </div>


    <?php if ($projects): ?>

        <div class="project-carousel">

            <?php foreach ($projects as $p): ?>

                <article class="project-card">

                    <?php if (!empty($p['thumbnail'])): ?>

                        <img
                            src="/uploads/<?= htmlspecialchars($p['thumbnail']) ?>"
                            alt="<?= htmlspecialchars($p['title']) ?>"
                            loading="lazy">

                    <?php else: ?>

                        <div class="project-placeholder">
                            PROJECT
                        </div>

                    <?php endif; ?>


                    <div class="project-card-body">

                        <span class="tag">
                            <?= htmlspecialchars(
                                $p['category_name'] ?? 'Uncategorized'
                            ) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($p['title']) ?>
                        </h3>

                        <p class="muted">

                            <?= htmlspecialchars(
                                mb_strimwidth(
                                    $p['description'] ?? '',
                                    0,
                                    150,
                                    '...'
                                )
                            ) ?>

                        </p>

                        <a
                            href="/projects/detail.php?id=<?= (int)$p['id'] ?>">
                            View Project →
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty">
            Belum ada project.
        </div>

    <?php endif; ?>

</section>


<!-- =========================================================
     3D ASSETS
========================================================= -->

<section class="section" id="assets">

    <div class="section-heading">

        <div>

            <span class="eyebrow">
                3D Library
            </span>

            <h2>
                3D Models
            </h2>

        </div>

        <span class="muted">
            <?= count($models) ?> model
        </span>

    </div>


    <?php if ($models): ?>

        <div class="asset-carousel">

            <?php foreach ($models as $i => $m): ?>

                <button
                    type="button"
                    class="asset-card <?= $i === 0 ? 'active' : '' ?>"
                    data-model="<?= htmlspecialchars($m['url']) ?>"
                    data-name="<?= htmlspecialchars($m['name']) ?>">

                    <div class="asset-preview">

                        <model-viewer
                            src="<?= htmlspecialchars($m['url']) ?>"
                            alt="<?= htmlspecialchars($m['name']) ?>"
                            camera-controls
                            disable-zoom
                            interaction-prompt="none"
                            shadow-intensity=".7">
                        </model-viewer>

                    </div>

                    <div class="asset-card-body">

                        <strong>
                            <?= htmlspecialchars($m['name']) ?>
                        </strong>

                        <span>
                            3D Model
                        </span>

                    </div>

                </button>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty">
            Belum ada model 3D.
        </div>

    <?php endif; ?>

</section>


<!-- =========================================================
     MODEL VIEWER
========================================================= -->

<script
    type="module"
    src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js">
</script>


<!-- =========================================================
     STYLE
========================================================= -->

<style>

/* =========================================================
   HERO
========================================================= */

.model-hero {
    width: 100%;
    padding: 45px 0 25px;
}

.model-copy {
    width: 100%;
    max-width: 850px;
    padding: 5px 0 20px;
}

.model-copy h1 {
    font-size: clamp(2.5rem, 6vw, 4.6rem);
    line-height: 1.03;
    letter-spacing: -0.045em;
    margin: 10px 0 18px;
    max-width: 700px;
}

.model-copy p {
    max-width: 680px;
    font-size: clamp(.95rem, 1.5vw, 1.1rem);
    line-height: 1.7;
}

.hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 24px;
}

.btn-secondary {
    background: transparent;
    border: 1px solid rgba(255,255,255,.18);
}


/* =========================================================
   3D VIEWER SECTION
========================================================= */

.model-section {
    width: 100%;
    margin: 10px 0 70px;
}

.model-viewer-card {
    width: 100%;
    min-height: 560px;
    overflow: hidden;

    border: 1px solid rgba(255,255,255,.1);
    border-radius: 24px;

    background:
        radial-gradient(
            circle at 50% 35%,
            rgba(255,255,255,.08),
            transparent 45%
        ),
        rgba(255,255,255,.025);

    box-shadow:
        0 25px 70px rgba(0,0,0,.28);
}

.model-viewer-card model-viewer {
    display: block;
    width: 100%;
    height: 500px;

    --poster-color: transparent;
}

.model-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 16px;

    padding: 15px 18px;

    border-top:
        1px solid rgba(255,255,255,.08);
}

.model-info {
    display: flex;
    flex-direction: column;
    gap: 3px;

    min-width: 0;
}

.model-label {
    font-size: .68rem;
    letter-spacing: .12em;
    opacity: .45;
}

.model-info strong {
    font-size: .95rem;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.model-toolbar button {
    border: 0;
    border-radius: 9px;

    padding: 8px 13px;

    cursor: pointer;
}


/* =========================================================
   EMPTY VIEWER
========================================================= */

.model-empty {
    min-height: 560px;

    display: grid;
    place-items: center;

    text-align: center;

    padding: 30px;
}

.model-empty strong {
    font-size: 1.3rem;
}

.model-empty p {
    margin: 8px 0;
    opacity: .65;
}

.model-empty small {
    opacity: .5;
}


/* =========================================================
   SECTION HEADER
========================================================= */

.section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 22px;
}

.section-heading h2 {
    margin-top: 5px;
}


/* =========================================================
   ASSET CAROUSEL
========================================================= */

.asset-carousel,
.project-carousel {
    display: flex;

    gap: 18px;

    overflow-x: auto;

    padding:
        4px
        2px
        18px;

    scroll-snap-type: x mandatory;

    scrollbar-width: thin;
}

.asset-card,
.project-card {
    flex: 0 0 285px;

    scroll-snap-align: start;

    overflow: hidden;

    border:
        1px solid rgba(255,255,255,.09);

    border-radius: 18px;

    background:
        rgba(255,255,255,.035);
}

.asset-card {
    padding: 0;

    text-align: left;

    color: inherit;

    cursor: pointer;

    transition:
        transform .2s ease,
        border-color .2s ease,
        background .2s ease;
}

.asset-card:hover,
.asset-card.active {
    transform: translateY(-4px);

    border-color:
        rgba(255,255,255,.3);

    background:
        rgba(255,255,255,.055);
}

.asset-preview {
    width: 100%;

    background:
        radial-gradient(
            circle at 50% 40%,
            rgba(255,255,255,.08),
            transparent 55%
        );
}

.asset-preview model-viewer {
    display: block;

    width: 100%;
    height: 190px;

    --poster-color: transparent;
}

.asset-card-body {
    padding: 16px;
}

.asset-card-body strong {
    display: block;

    margin-bottom: 5px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.asset-card-body span {
    font-size: .82rem;
    opacity: .6;
}


/* =========================================================
   PROJECT CAROUSEL
========================================================= */

.project-card {
    flex-basis: 340px;
}

.project-card img,
.project-placeholder {
    width: 100%;
    height: 205px;

    object-fit: cover;

    display: block;
}

.project-placeholder {
    display: grid;
    place-items: center;

    background:
        rgba(255,255,255,.06);

    font-weight: 700;

    letter-spacing: .12em;
}

.project-card-body {
    padding: 16px;
}

.project-card h3 {
    margin: 10px 0 8px;
}

.project-card-body > a {
    display: inline-block;

    margin-top: 12px;
}

.tag {
    display: inline-block;

    font-size: .75rem;

    padding: 4px 8px;

    border-radius: 6px;

    background:
        rgba(255,255,255,.08);
}


/* =========================================================
   EMPTY
========================================================= */

.empty {
    padding: 40px;

    text-align: center;

    border:
        1px solid rgba(255,255,255,.08);

    border-radius: 16px;

    opacity: .6;
}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 850px) {

    .model-hero {
        padding:
            30px
            0
            20px;
    }

    .model-copy {
        padding:
            0
            0
            22px;
    }

    .model-copy h1 {
        font-size:
            clamp(
                2.3rem,
                9vw,
                3.7rem
            );

        line-height: 1.04;

        max-width: 600px;
    }

    .model-copy p {
        font-size: 1rem;

        max-width: 600px;
    }

    .model-section {
        margin-bottom: 55px;
    }

    .model-viewer-card {
        min-height: 460px;

        border-radius: 20px;
    }

    .model-viewer-card model-viewer {
        height: 410px;
    }

    .model-empty {
        min-height: 460px;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 600px) {

    .model-hero {
        padding:
            20px
            0
            15px;
    }

    .model-copy {
        padding-bottom: 18px;
    }

    .model-copy h1 {
        font-size:
            clamp(
                2.15rem,
                12vw,
                3rem
            );

        line-height: 1.05;

        letter-spacing: -.035em;

        margin:
            8px
            0
            14px;
    }

    .model-copy p {
        font-size: .92rem;

        line-height: 1.6;
    }

    .hero-actions {
        width: 100%;

        gap: 9px;

        margin-top: 18px;
    }

    .hero-actions .btn {
        flex: 1 1 auto;

        text-align: center;

        padding:
            11px
            14px;

        font-size: .9rem;
    }

    .model-section {
        margin-bottom: 45px;
    }

    .model-viewer-card {
        min-height: 370px;

        border-radius: 18px;
    }

    .model-viewer-card model-viewer {
        height: 320px;
    }

    .model-empty {
        min-height: 370px;
    }

    .model-toolbar {
        padding:
            11px
            13px;

        font-size: .85rem;
    }

    .model-toolbar button {
        padding:
            7px
            10px;

        font-size: .8rem;
    }

    .section-heading {
        align-items: flex-start;

        margin-bottom: 17px;
    }

    .section-heading h2 {
        font-size: 1.5rem;
    }

    .asset-card {
        flex-basis: 250px;
    }

    .asset-preview model-viewer {
        height: 170px;
    }

    .project-card {
        flex-basis: 285px;
    }

    .project-card img,
    .project-placeholder {
        height: 175px;
    }
}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 400px) {

    .model-copy h1 {
        font-size: 2.1rem;
    }

    .model-copy p {
        font-size: .88rem;
    }

    .hero-actions {
        flex-direction: column;
    }

    .hero-actions .btn {
        width: 100%;
    }

    .model-viewer-card {
        min-height: 330px;
    }

    .model-viewer-card model-viewer {
        height: 280px;
    }

    .model-empty {
        min-height: 330px;
    }

    .asset-card {
        flex-basis: 230px;
    }

}


/* =========================================================
   TOUCH / MOBILE CAROUSEL
========================================================= */

@media (hover: none) {

    .asset-card:hover {
        transform: none;
    }

}


/* =========================================================
   SCROLLBAR
========================================================= */

.asset-carousel::-webkit-scrollbar,
.project-carousel::-webkit-scrollbar {
    height: 6px;
}

.asset-carousel::-webkit-scrollbar-thumb,
.project-carousel::-webkit-scrollbar-thumb {
    border-radius: 10px;

    background:
        rgba(255,255,255,.18);
}

</style>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const viewer = document.getElementById('mainModel');
    const modelName = document.getElementById('modelName');
    const resetButton = document.getElementById('resetModel');

    const assetCards =
        document.querySelectorAll('.asset-card');


    /* =========================
       CHANGE MAIN MODEL
    ========================= */

    assetCards.forEach(function (card) {

        card.addEventListener('click', function () {

            if (!viewer) {
                return;
            }

            const modelUrl =
                card.dataset.model;

            const name =
                card.dataset.name;


            viewer.src = modelUrl;

            viewer.alt = name;


            if (modelName) {
                modelName.textContent = name;
            }


            assetCards.forEach(function (item) {

                item.classList.remove('active');

            });


            card.classList.add('active');


            /* Scroll viewer into view on mobile */

            if (window.innerWidth <= 600) {

                const viewerSection =
                    document.querySelector('.model-section');

                if (viewerSection) {

                    viewerSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                }

            }

        });

    });


    /* =========================
       RESET VIEW
    ========================= */

    if (resetButton && viewer) {

        resetButton.addEventListener(
            'click',
            function () {

                viewer.cameraOrbit =
                    '0deg 75deg 105%';

                viewer.fieldOfView =
                    'auto';

            }
        );

    }

});

</script>


<?php require 'includes/footer.php'; ?>
