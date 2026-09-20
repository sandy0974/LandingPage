<?php
require 'config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$page_title = 'My Portfolio';

$stmt = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM projects p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.created_at DESC
    LIMIT 12
");

$projects = $stmt->fetchAll();

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
            'url' => '/uploads/' . implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode('/', $rel)
                )
            ),
            'extension' => strtolower($file->getExtension())
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

<main class="portfolio-page">

    <section class="hero">
        <div class="hero-content">
            <div class="eyebrow">
                3D ARTIST • GAME DEV • PORTFOLIO
            </div>

            <h1>
                Build. Create. Share.
            </h1>

            <p>
                Portfolio pribadi untuk menampilkan project,
                game, 3D model, asset, desain, dan eksperimen lainnya.
            </p>

            <div class="hero-actions">
                <a href="#projects" class="btn">
                    View Projects
                </a>

                <a href="#assets" class="btn btn-secondary">
                    Explore 3D Assets
                </a>
            </div>
        </div>
    </section>


    <section class="viewer-section">

        <div class="section-heading">
            <div>
                <span class="eyebrow">
                    3D SHOWCASE
                </span>

                <h2>
                    3D Model Viewer
                </h2>
            </div>

            <div class="viewer-help">
                Rotate • Zoom • Pan
            </div>
        </div>


        <div class="model-viewer-card">

            <?php if ($featured): ?>

                <model-viewer
                    id="mainModel"
                    src="<?= htmlspecialchars($featured['url']) ?>"
                    alt="<?= htmlspecialchars($featured['name']) ?>"
                    camera-controls
                    touch-action="pan-y"
                    auto-rotate
                    auto-rotate-delay="1000"
                    rotation-per-second="20deg"
                    shadow-intensity="1"
                    shadow-softness="1"
                    exposure="1"
                    environment-image="neutral"
                    interaction-prompt="none"
                    loading="eager"
                ></model-viewer>

                <div class="model-toolbar">

                    <div class="model-info">
                        <span>
                            CURRENT MODEL
                        </span>

                        <strong id="modelName">
                            <?= htmlspecialchars($featured['name']) ?>
                        </strong>
                    </div>

                    <div class="model-actions">

                        <button
                            type="button"
                            id="toggleRotation"
                        >
                            Pause Rotation
                        </button>

                        <button
                            type="button"
                            id="resetModel"
                        >
                            Reset View
                        </button>

                    </div>

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
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </section>


    <section
        class="section"
        id="projects"
    >

        <div class="section-heading">

            <div>
                <span class="eyebrow">
                    SELECTED WORK
                </span>

                <h2>
                    Projects
                </h2>
            </div>

            <a
                href="/projects/index.php"
                class="section-link"
            >
                View all →
            </a>

        </div>


        <?php if ($projects): ?>

            <div class="project-carousel">

                <?php foreach ($projects as $p): ?>

                    <article class="project-card">

                        <?php if (!empty($p['thumbnail'])): ?>

                            <div class="project-thumbnail">

                                <img
                                    src="/uploads/<?= htmlspecialchars($p['thumbnail']) ?>"
                                    alt="<?= htmlspecialchars($p['title']) ?>"
                                    loading="lazy"
                                >

                            </div>

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
                                href="/projects/detail.php?id=<?= (int)$p['id'] ?>"
                            >
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


    <section
        class="section"
        id="assets"
    >

        <div class="section-heading">

            <div>
                <span class="eyebrow">
                    3D LIBRARY
                </span>

                <h2>
                    3D Assets
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
                        data-name="<?= htmlspecialchars($m['name']) ?>"
                    >

                        <div class="asset-preview">

                            <model-viewer
                                src="<?= htmlspecialchars($m['url']) ?>"
                                alt="<?= htmlspecialchars($m['name']) ?>"
                                camera-controls
                                disable-zoom
                                interaction-prompt="none"
                                shadow-intensity="1"
                                exposure="1"
                                environment-image="neutral"
                            ></model-viewer>

                        </div>


                        <div class="asset-card-body">

                            <strong>
                                <?= htmlspecialchars($m['name']) ?>
                            </strong>

                            <span>
                                <?= strtoupper($m['extension']) ?> • 3D Model
                            </span>

                        </div>

                    </button>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty">
                Belum ada 3D asset.
            </div>

        <?php endif; ?>

    </section>

</main>


<script
    type="module"
    src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"
></script>


<script>

document.addEventListener('DOMContentLoaded', () => {

    const mainModel =
        document.getElementById('mainModel');

    const modelName =
        document.getElementById('modelName');

    const resetModel =
        document.getElementById('resetModel');

    const toggleRotation =
        document.getElementById('toggleRotation');

    const assetCards =
        document.querySelectorAll('.asset-card');

    let isRotating = true;


    assetCards.forEach(card => {

        card.addEventListener('click', () => {

            if (!mainModel) {
                return;
            }

            mainModel.src =
                card.dataset.model;

            if (modelName) {
                modelName.textContent =
                    card.dataset.name;
            }

            assetCards.forEach(item => {
                item.classList.remove('active');
            });

            card.classList.add('active');

            mainModel.setAttribute(
                'auto-rotate',
                ''
            );

            isRotating = true;

            if (toggleRotation) {
                toggleRotation.textContent =
                    'Pause Rotation';
            }

            document
                .querySelector('.viewer-section')
                ?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

        });

    });


    if (toggleRotation && mainModel) {

        toggleRotation.addEventListener(
            'click',
            () => {

                if (isRotating) {

                    mainModel.removeAttribute(
                        'auto-rotate'
                    );

                    toggleRotation.textContent =
                        'Start Rotation';

                    isRotating = false;

                } else {

                    mainModel.setAttribute(
                        'auto-rotate',
                        ''
                    );

                    toggleRotation.textContent =
                        'Pause Rotation';

                    isRotating = true;

                }

            }
        );

    }


    if (resetModel && mainModel) {

        resetModel.addEventListener(
            'click',
            () => {

                mainModel.cameraOrbit =
                    'auto auto auto';

                mainModel.cameraTarget =
                    'auto auto auto';

                mainModel.fieldOfView =
                    'auto';

            }
        );

    }

});

</script>


<style>

.portfolio-page {
    width: 100%;
}

.hero {
    width: 100%;
    min-height: 560px;
    display: flex;
    align-items: center;
    padding: 80px 0;
}

.hero-content {
    max-width: 850px;
}

.eyebrow {
    display: block;
    margin-bottom: 15px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .15em;
    opacity: .5;
}

.hero h1 {
    margin: 0;
    font-size: clamp(65px, 10vw, 130px);
    line-height: .88;
    letter-spacing: -.065em;
}

.hero p {
    max-width: 620px;
    margin-top: 30px;
    font-size: 18px;
    line-height: 1.7;
    opacity: .6;
}

.hero-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 13px 19px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    background: rgba(255,255,255,.95);
    color: #111;
}

.btn-secondary {
    background: transparent;
    color: inherit;
    border: 1px solid rgba(255,255,255,.15);
}

.viewer-section {
    padding: 60px 0 90px;
}

.section {
    padding: 80px 0;
}

.section-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.section-heading h2 {
    margin: 0;
    font-size: 38px;
    letter-spacing: -.035em;
}

.viewer-help {
    font-size: 12px;
    opacity: .4;
}

.model-viewer-card {
    width: 100%;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 26px;
    background:
        radial-gradient(
            circle at 50% 35%,
            rgba(255,255,255,.08),
            transparent 48%
        ),
        rgba(255,255,255,.025);
    box-shadow:
        0 30px 90px rgba(0,0,0,.25);
}

#mainModel {
    display: block;
    width: 100%;
    height: 650px;
    background: transparent;
}

.model-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 16px 20px;
    border-top: 1px solid rgba(255,255,255,.08);
}

.model-info span {
    display: block;
    margin-bottom: 5px;
    font-size: 9px;
    letter-spacing: .15em;
    opacity: .4;
}

.model-info strong {
    font-size: 17px;
}

.model-actions {
    display: flex;
    gap: 8px;
}

.model-actions button {
    padding: 9px 13px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 9px;
    background: rgba(255,255,255,.04);
    color: inherit;
    cursor: pointer;
}

.model-actions button:hover {
    background: rgba(255,255,255,.09);
}

.model-empty {
    height: 650px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.model-empty p {
    opacity: .5;
}

.section-link {
    font-size: 13px;
    text-decoration: none;
    opacity: .55;
}

.project-carousel,
.asset-carousel {
    display: flex;
    gap: 18px;
    width: 100%;
    overflow-x: auto;
    padding: 5px 3px 20px;
    scroll-snap-type: x mandatory;
    scrollbar-width: thin;
}

.project-card {
    flex: 0 0 340px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 18px;
    background: rgba(255,255,255,.035);
    scroll-snap-align: start;
}

.project-thumbnail {
    width: 100%;
    height: 205px;
    overflow: hidden;
}

.project-thumbnail img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

.project-placeholder {
    width: 100%;
    height: 205px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.05);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .15em;
    opacity: .4;
}

.project-card-body {
    padding: 18px;
}

.project-card-body .tag {
    display: block;
    margin-bottom: 9px;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .12em;
    opacity: .4;
}

.project-card-body h3 {
    margin: 0 0 9px;
    font-size: 20px;
}

.project-card-body p {
    min-height: 45px;
    margin: 0;
    font-size: 13px;
    line-height: 1.6;
}

.project-card-body a {
    display: inline-block;
    margin-top: 16px;
    font-size: 13px;
    text-decoration: none;
}

.asset-card {
    flex: 0 0 280px;
    padding: 0;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 18px;
    background: rgba(255,255,255,.035);
    color: inherit;
    text-align: left;
    cursor: pointer;
    scroll-snap-align: start;
    transition:
        transform .2s ease,
        border-color .2s ease,
        background .2s ease;
}

.asset-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255,255,255,.2);
}

.asset-card.active {
    border-color: rgba(255,255,255,.4);
    background: rgba(255,255,255,.055);
}

.asset-preview {
    width: 100%;
    height: 220px;
    background:
        radial-gradient(
            circle at 50% 40%,
            rgba(255,255,255,.08),
            transparent 60%
        );
}

.asset-preview model-viewer {
    width: 100%;
    height: 100%;
    display: block;
}

.asset-card-body {
    padding: 15px 16px 17px;
    border-top: 1px solid rgba(255,255,255,.07);
}

.asset-card-body strong {
    display: block;
    margin-bottom: 8px;
    font-size: 15px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.asset-card-body span {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .08em;
    opacity: .4;
}

.muted {
    opacity: .45;
}

.empty {
    padding: 45px 30px;
    border: 1px dashed rgba(255,255,255,.12);
    border-radius: 18px;
    text-align: center;
    opacity: .5;
}

@media (max-width: 850px) {

    .hero {
        min-height: 480px;
        padding: 60px 0;
    }

    .hero h1 {
        font-size: 70px;
    }

    .section-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    #mainModel {
        height: 500px;
    }

    .model-empty {
        height: 500px;
    }

}

@media (max-width: 550px) {

    .hero h1 {
        font-size: 58px;
    }

    .hero p {
        font-size: 16px;
    }

    #mainModel {
        height: 400px;
    }

    .model-empty {
        height: 400px;
    }

    .model-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .model-actions {
        width: 100%;
    }

    .model-actions button {
        flex: 1;
    }

    .project-card {
        flex-basis: 285px;
    }

    .asset-card {
        flex-basis: 250px;
    }

}

</style>


<?php
require 'includes/footer.php';
?>
