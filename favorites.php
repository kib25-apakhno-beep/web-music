<?php 
session_start(); 

// ЗАХИСТ СТОРІНКИ: Якщо користувач не увійшов, перекидаємо його на login.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Улюблене - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    
    <style>
        /* Стиль для великої шапки плейлиста */
        .playlist-header {
            background: linear-gradient(135deg, rgba(209, 34, 143, 0.15), rgba(138, 67, 242, 0.05));
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        /* Кнопка Play для всього плейлиста */
        .play-all-btn {
            background: linear-gradient(90deg, #d1228f, #8a43f2);
            border: none;
            transition: all 0.3s ease;
        }
        .play-all-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(209, 34, 143, 0.6);
        }
    </style>
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 mb-5 position-relative">
        
        <div class="playlist-header p-4 rounded-4 mb-5 d-flex align-items-center">
            <div class="rounded-4 d-flex justify-content-center align-items-center me-4" 
                 style="width: 120px; height: 120px; background: linear-gradient(135deg, #ff6bc1, #d1228f); box-shadow: 0 0 20px rgba(209, 34, 143, 0.5);">
                <i class="bi bi-heart-fill text-white" style="font-size: 3rem;"></i>
            </div>
            
            <div class="flex-grow-1">
                <p class="text-white-50 text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 2px;">Плейлист</p>
                <h1 class="display-5 fw-bold text-white mb-2">Улюблене</h1>
                <p class="text-white-50 mb-0">
                    <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?> 
                    <span class="mx-2">•</span> <span id="favorites-count">0 треків</span> <span class="mx-2">•</span> <span id="favorites-duration">0 хв 0 сек</span>
                </p>
            </div>
            
            <div class="ms-auto d-none d-md-block">
                <button class="btn play-all-btn rounded-pill px-4 py-2 text-white fw-bold d-flex align-items-center" onclick="if (window.loadAndPlay) { const favorites = window.getFavorites(); if (favorites.length) window.loadAndPlay(favorites, 0); }">
                    <i class="bi bi-play-fill fs-4 me-1"></i> Слухати все
                </button>
            </div>
        </div>
        
        <div class="d-md-none mb-4">
            <button class="btn play-all-btn w-100 rounded-pill py-2 text-white fw-bold d-flex justify-content-center align-items-center" onclick="if (window.loadAndPlay) { const favorites = window.getFavorites(); if (favorites.length) window.loadAndPlay(favorites, 0); }">
                <i class="bi bi-play-fill fs-4 me-1"></i> Слухати все
            </button>
        </div>

        <div class="tracks-list" id="favorites-tracks-list" style="max-width: 100%;"></div>
    </main>

    <?php include 'php/footer.php'; ?>

    <?php include 'php/player.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>