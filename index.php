<?php
session_start();
require_once 'function/db_helpers.php';

if (isset($_SESSION['user_id'])) {
    header('Location: catalog.php');
    exit();
}

$tracks = getAllSongs();
if (!empty($tracks)) {
    shuffle($tracks);
    $tracks = array_slice($tracks, 0, 10);
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- НОВА НАЗВА САЙТУ НА ВКЛАДЦІ БРАУЗЕРА -->
    <title>Vestra - Твій новий звук</title>
    
    <link rel="icon" type="image/png" href="img/favicon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 position-relative">
        <div class="row">
            
            <div class="col-lg-7 col-xl-6 text-start">
                
                <p class="fw-bold mb-2 text-uppercase" style="color: #d1228f; font-size: 0.75rem; letter-spacing: 2px;">
                    Твоя музика. Твій настрій.
                </p>
                
                <h1 class="display-3 fw-bold text-uppercase mb-4" style="line-height: 1.1;">
                    Відкрий <br> свій <br>
                    <span style="background: linear-gradient(90deg, #ff6bc1, #b273ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0px 4px 15px rgba(178, 115, 255, 0.4)); font-weight: 900;">новий звук</span>
                </h1>
                
                <p class="text-white-50 mb-4" style="max-width: 400px; font-size: 0.95rem;">
                    Слухай добірки, знаходь нових виконавців та створюй власні плейлисти.
                </p>
                
                
                
            </div>
        </div>
    </main>

    <section class="container mt-5 pt-4 mb-5">
        <p class="text-white-50 text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">Популярне зараз</p>
        <h2 class="fw-bold mb-4 text-white">Найчастіше слухають</h2>
        
        <div class="tracks-list" style="max-width: 900px;">
            <?php if (!empty($tracks)): ?>
                <?php foreach ($tracks as $index => $track): ?>
                    <?php $trackData = [
                        'id' => (int)$track['id'],
                        'title' => $track['title'],
                        'artist' => $track['composer'] ?: 'Vestra',
                        'filename' => basename($track['url']),
                        'url' => $track['url']
                    ]; ?>
                    <div class="track-item d-flex align-items-center justify-content-between p-3 mb-2 rounded-3" style="background: rgba(255,255,255,0.04); transition: 0.3s; cursor: pointer;" data-track-index="<?php echo $index; ?>" data-track='<?php echo htmlspecialchars(json_encode($trackData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'>
                        <div class="d-flex align-items-center">
                            <div class="rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #d1228f, #8a43f2); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                <span class="fw-bold text-white fs-5"><?php echo $index + 1; ?></span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-white"><?php echo htmlspecialchars($track['title']); ?></h6>
                                <small class="text-white-50" style="font-size: 0.75rem;"><?php echo htmlspecialchars($track['composer'] ?: 'Vestra'); ?></small>
                            </div>
                        </div>
                        <div class="text-white-50" style="font-size: 0.85rem;">
                            <i class="bi bi-play-fill me-1"></i> Відтворити
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 text-white-50">
                    <p class="mb-0">Поки що немає треків для показу.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'php/footer.php'; ?>

    <?php include 'php/player.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.track-item[data-track]').forEach(function (row) {
                row.addEventListener('click', function () {
                    try {
                        const track = JSON.parse(row.getAttribute('data-track'));
                        if (window.loadAndPlay && track && track.url) {
                            window.loadAndPlay([track], 0);
                        }
                    } catch (e) {}
                });
            });
        });
    </script>

</body>
</html>