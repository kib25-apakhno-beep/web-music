<?php
session_start();
require_once 'function/db_helpers.php';

$tracks = getAllSongs();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            border-color: rgba(209, 34, 143, 0.5); 
        }
        .genre-box {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        /* --- Магія кнопки Play --- */
        .album-cover {
            position: relative;
            overflow: hidden;
        }
        .play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5); /* Спочатку зменшена */
            background: #d1228f;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0; /* Невидима */
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(209, 34, 143, 0.6);
        }
        /* При наведенні на всю картку кнопка плавно з'являється */
        .glass-card:hover .play-btn {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    </style>
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 mb-5 position-relative">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-white mb-0">Каталог</h2>
            
            <div class="search-box d-flex align-items-center p-1 rounded-pill" style="background: rgba(255, 255, 255, 0.08); width: 300px;">
                <i class="bi bi-search text-white ms-3"></i>
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none ms-2" style="font-size: 0.85rem;" placeholder="Пошук у каталозі...">
            </div>
        </div>
        
        <h5 class="text-white-50 mb-3 mt-5">Популярні жанри</h5>
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3">
                <div class="genre-box glass-card" style="background: linear-gradient(135deg, rgba(209, 34, 143, 0.2), rgba(0,0,0,0));">
                    <span class="text-white">Rock</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="genre-box glass-card" style="background: linear-gradient(135deg, rgba(138, 67, 242, 0.2), rgba(0,0,0,0));">
                    <span class="text-white">Pop</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="genre-box glass-card" style="background: linear-gradient(135deg, rgba(255, 107, 193, 0.2), rgba(0,0,0,0));">
                    <span class="text-white">Hip-Hop</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="genre-box glass-card" style="background: linear-gradient(135deg, rgba(50, 200, 255, 0.2), rgba(0,0,0,0));">
                    <span class="text-white">Electronic</span>
                </div>
            </div>
        </div>

        <h5 class="text-white-50 mb-3">Вся музика з бази</h5>
        <div class="row g-4">
            <?php if (!empty($tracks)): ?>
                <?php foreach ($tracks as $index => $track): ?>
                    <?php $trackData = [
                        'id' => (int)$track['id'],
                        'title' => $track['title'],
                        'artist' => $track['composer'] ?: 'Vestra',
                        'filename' => basename($track['url']),
                        'url' => $track['url']
                    ]; ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="glass-card p-3 rounded-4 text-center h-100" data-track-index="<?php echo $index; ?>" data-track='<?php echo htmlspecialchars(json_encode($trackData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'>
                            <div class="album-cover rounded-3 mb-3" style="width: 100%; aspect-ratio: 1; background: linear-gradient(135deg, #d1228f, #8a43f2); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-music-note-beamed fs-1 text-white-50"></i>
                                <div class="play-btn">
                                    <i class="bi bi-play-fill fs-3 ms-1"></i>
                                </div>
                            </div>
                            <h6 class="text-white mb-1 text-truncate"><?php echo htmlspecialchars($track['title']); ?></h6>
                            <p class="text-white-50 mb-0" style="font-size: 0.75rem;"><?php echo htmlspecialchars($track['composer'] ?: 'Vestra'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 text-white-50">
                        <i class="bi bi-music-note-list fs-1 mb-3"></i>
                        <p class="mb-0">У базі поки немає треків.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
    
    <?php include 'php/player.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.glass-card[data-track]').forEach(function (card) {
                card.addEventListener('click', function () {
                    try {
                        const track = JSON.parse(card.getAttribute('data-track'));
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