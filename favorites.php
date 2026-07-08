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
                    <span class="mx-2">•</span> 4 треки <span class="mx-2">•</span> 15 хв 10 сек
                </p>
            </div>
            
            <div class="ms-auto d-none d-md-block">
                <button class="btn play-all-btn rounded-pill px-4 py-2 text-white fw-bold d-flex align-items-center">
                    <i class="bi bi-play-fill fs-4 me-1"></i> Слухати все
                </button>
            </div>
        </div>
        
        <div class="d-md-none mb-4">
            <button class="btn play-all-btn w-100 rounded-pill py-2 text-white fw-bold d-flex justify-content-center align-items-center">
                <i class="bi bi-play-fill fs-4 me-1"></i> Слухати все
            </button>
        </div>

        <div class="tracks-list" style="max-width: 100%;">
            <div class="d-flex text-white-50 px-3 mb-2" style="font-size: 0.85rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                <div style="width: 50px;">#</div>
                <div class="flex-grow-1">НАЗВА</div>
                <div style="width: 100px;" class="text-end pe-3"><i class="bi bi-clock"></i></div>
            </div>

            <?php
            // Імітація улюблених треків
            $favorites = [
                ['id' => 1, 'title' => 'Midnight City', 'artist' => 'M83', 'time' => '4:03', 'letter' => 'M', 'color' => '#d1228f'],
                ['id' => 2, 'title' => 'Starboy', 'artist' => 'The Weeknd', 'time' => '3:50', 'letter' => 'S', 'color' => '#8a43f2'],
                ['id' => 3, 'title' => 'Blinding Lights', 'artist' => 'The Weeknd', 'time' => '3:20', 'letter' => 'B', 'color' => '#ff6bc1'],
                ['id' => 4, 'title' => 'Instant Crush', 'artist' => 'Daft Punk', 'time' => '5:38', 'letter' => 'I', 'color' => '#32c8ff']
            ];

            foreach ($favorites as $index => $track): ?>
                
                <div class="track-item d-flex align-items-center p-2 rounded-3 mb-1" style="background: rgba(255,255,255,0.02); transition: 0.3s; cursor: pointer;">
                    
                    <div class="text-white-50 text-center fw-bold" style="width: 50px; font-size: 0.9rem;">
                        <?php echo $index + 1; ?>
                    </div>
                    
                    <div class="d-flex align-items-center flex-grow-1">
                        <div class="rounded-3 d-flex justify-content-center align-items-center me-3" 
                             style="width: 40px; height: 40px; background-color: <?php echo $track['color']; ?>; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                            <span class="fw-bold text-white" style="font-size: 1.1rem;"><?php echo $track['letter']; ?></span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-white" style="font-size: 0.95rem;"><?php echo $track['title']; ?></h6>
                            <small class="text-white-50" style="font-size: 0.75rem;"><?php echo $track['artist']; ?></small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-end" style="width: 100px;">
                        <i class="bi bi-heart-fill me-3" style="color: #ff6bc1; font-size: 0.9rem;"></i>
                        <span class="text-white-50" style="font-size: 0.85rem;"><?php echo $track['time']; ?></span>
                    </div>
                    
                </div>
                
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>

    <?php include 'php/player.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>