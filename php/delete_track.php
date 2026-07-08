<?php
session_start();

// Захист
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Перевіряємо, чи передали ім'я файлу для видалення
if (isset($_GET['file'])) {
    // Очищаємо ім'я файлу від зайвих символів для безпеки
    $filename = basename($_GET['file']); 
    $filepath = '../uploads/' . $filename;

    // Якщо файл існує і це mp3, видаляємо його (функція unlink)
    if (file_exists($filepath) && strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) == 'mp3') {
        unlink($filepath);
        // Повертаємося на сторінку музики з повідомленням про успіх
        header('Location: ../mymusic.php?deleted=1');
        exit();
    }
}

// Якщо щось пішло не так, просто повертаємо назад
header('Location: ../mymusic.php');
exit();
?>