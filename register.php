<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація - Melodica</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container d-flex justify-content-center align-items-center mt-5 mb-5">
        <div class="login-box p-5 rounded-4 text-center" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); width: 100%; max-width: 420px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            
            <h2 class="fw-bold mb-4 text-white">Реєстрація</h2>
            
            <form action="php/auth.php" method="POST">
                <div class="mb-3 text-start">
                    <label class="text-white-50 mb-1" style="font-size: 0.85rem;">Ім'я користувача</label>
                    <input type="text" name="username" class="form-control bg-transparent text-white shadow-none" style="border: 1px solid rgba(255,255,255,0.2);" required>
                </div>
                <div class="mb-3 text-start">
                    <label class="text-white-50 mb-1" style="font-size: 0.85rem;">Email</label>
                    <input type="email" name="email" class="form-control bg-transparent text-white shadow-none" style="border: 1px solid rgba(255,255,255,0.2);" required>
                </div>
                <div class="mb-4 text-start">
                    <label class="text-white-50 mb-1" style="font-size: 0.85rem;">Пароль</label>
                    <input type="password" name="password" class="form-control bg-transparent text-white shadow-none" style="border: 1px solid rgba(255,255,255,0.2);" required>
                </div>
                
                <button type="submit" class="btn rounded-pill w-100 py-2 text-white fw-bold mb-3 btn-gradient">Створити акаунт</button>
            </form>
            
            <p class="text-white-50 mb-0" style="font-size: 0.85rem;">Вже є акаунт? <a href="login.php" style="color: #ff6bc1; text-decoration: none; font-weight: bold;">Увійти</a></p>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>