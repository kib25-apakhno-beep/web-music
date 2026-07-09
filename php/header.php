<?php
$isLoggedIn = isset($_SESSION['user_id']); 
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark pt-4 pb-3" style="background-color: transparent;">
        <div class="container">
            
            <a class="navbar-brand d-flex align-items-center" href="index.php" style="transition: 0.3s;">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 38px; height: 38px; background: linear-gradient(135deg, #ff6bc1, #8a43f2); box-shadow: 0 0 15px rgba(255, 107, 193, 0.6);">
                    <i class="bi bi-soundwave text-white" style="font-size: 1.3rem;"></i>
                </div>
                <span class="fw-bold text-uppercase" style="font-size: 1.25rem; letter-spacing: 3px; text-shadow: 0 0 10px rgba(255,255,255,0.3);">Vestra</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <?php if ($isLoggedIn): ?>
                    <ul class="navbar-nav ms-auto" style="font-size: 0.85rem;">
                        <li class="nav-item px-2"><a class="nav-link text-white" href="index.php">Головна</a></li>
                        <li class="nav-item px-2"><a class="nav-link text-white-50" href="playlists.php">Плейлисти</a></li>
                    </ul>
                    
                    <div class="d-flex align-items-center mt-3 mt-lg-0 ms-lg-4">
                        <a href="upload.php" class="text-white-50 text-decoration-none me-4" style="font-size: 0.85rem;">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Додати MP3
                        </a>
                        <a href="profile.php" class="text-white text-decoration-none d-flex align-items-center me-4" style="font-size: 0.85rem;">
                            <i class="bi bi-person-circle fs-5 me-2 text-secondary"></i> 
                            <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Користувач'; ?>
                        </a>
                        <a href="db/logout.php" class="btn btn-outline-light btn-sm rounded-pill px-4 py-1" style="font-size: 0.8rem; border-color: rgba(255,255,255,0.2);">Вийти</a>
                    </div>

                <?php else: ?>
                    <div class="d-flex align-items-center mt-3 mt-lg-0 ms-lg-4" id="auth-buttons">
                        <a href="../php/login.php" class="text-white text-decoration-none me-4 fw-bold" style="font-size: 0.9rem;">Увійти</a>
                        <a href="../php/register.php" class="btn rounded-pill px-4 py-2 text-white fw-bold btn-gradient" style="font-size: 0.9rem;">Реєстрація</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </nav>
</header>