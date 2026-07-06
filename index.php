<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melodica</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- МАГІЯ: Цей рядок 100% змусить браузер показати нові анімації та градієнти -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include 'php/header.php'; ?>

    <!-- Звичайний container для ідеального вирівнювання з логотипом -->
    <main class="body container mt-5 pt-3 position-relative">
        <div class="row">
            
            <div class="col-lg-7 col-xl-6 text-start">
                
                <p class="fw-bold mb-2 text-uppercase" style="color: #d1228f; font-size: 0.75rem; letter-spacing: 2px;">
                    Твоя музика. Твій настрій.
                </p>
                
                <h1 class="display-3 fw-bold text-uppercase mb-4" style="line-height: 1.1;">
                    Відкрий <br> свій <br>
                    <!-- ПОВЕРНУТО ЯСКРАВИЙ СУЦІЛЬНИЙ ТЕКСТ -->
                    <span style="background: linear-gradient(90deg, #ff6bc1, #b273ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0px 4px 15px rgba(178, 115, 255, 0.4)); font-weight: 900;">новий звук</span>
                </h1>
                
                <p class="text-white-50 mb-4" style="max-width: 400px; font-size: 0.95rem;">
                    Слухай добірки, знаходь нових виконавців та створюй власні плейлисти.
                </p>
                
                <div class="search-box d-flex align-items-center p-1 rounded-pill mb-4" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.25); max-width: 480px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
                    
                    <i class="bi bi-search text-white ms-3 fs-5"></i>
                    
                    <input type="text" class="form-control bg-transparent border-0 text-white shadow-none ms-2" placeholder="Знайти трек, виконавця або альбом...">
                    
                    <!-- Кнопка з класом для анімації -->
                    <button class="btn rounded-pill px-4 py-2 text-white fw-bold btn-gradient">Знайти</button>
                </div>
                
                <a href="#" class="text-white-50 text-decoration-none" style="font-size: 0.8rem;">
                    <i class="bi bi-grid-fill me-1"></i> Переглянути весь каталог
                </a>
                
            </div>
        </div>
    </main>

    <section class="container mt-5 pt-4 mb-5">
        <p class="text-white-50 text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 1px;">Популярне зараз</p>
        <h2 class="fw-bold mb-4 text-white">Найчастіше слухають</h2>
        
        <div class="tracks-list" style="max-width: 900px;">
            <?php
            $tracks = [
                ['title' => 'Death Punch', 'artist' => 'Five Finger Death Punch', 'time' => '3:05', 'letter' => 'D', 'color' => '#8a43f2'],
                ['title' => 'Маршрутка', 'artist' => 'Скрябін', 'time' => '3:45', 'letter' => 'С', 'color' => '#d1228f'],
                ['title' => 'Welcome To The Circus', 'artist' => 'Five Finger Death Punch', 'time' => '4:16', 'letter' => 'W', 'color' => '#8a43f2'],
                ['title' => 'Chris Daughtry', 'artist' => 'Инаугурация - Сверхестественное', 'time' => '3:20', 'letter' => 'C', 'color' => '#8a43f2'],
                ['title' => 'Thunderstruck', 'artist' => 'AC/DC', 'time' => '4:52', 'letter' => 'A', 'color' => '#d1228f']
            ];

            foreach ($tracks as $track): ?>
                <div class="track-item d-flex align-items-center justify-content-between p-3 mb-2 rounded-3" style="background: rgba(255,255,255,0.04); transition: 0.3s; cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background-color: <?php echo $track['color']; ?>; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            <span class="fw-bold text-white fs-5"><?php echo $track['letter']; ?></span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-white"><?php echo $track['title']; ?></h6>
                            <small class="text-white-50" style="font-size: 0.75rem;"><?php echo $track['artist']; ?></small>
                        </div>
                    </div>
                    <div class="text-white-50" style="font-size: 0.85rem;">
                        <?php echo $track['time']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include 'php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>