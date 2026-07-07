<?php
session_start();

// Захист: чи авторизований користувач
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Перевіряємо чи файл взагалі був завантажений і чи немає базових системних помилок
    if (isset($_FILES['mp3_file']) && $_FILES['mp3_file']['error'] === UPLOAD_ERR_OK) {
        
        $file = $_FILES['mp3_file'];
        $errors = [];
        
        // 1. Перевірка розширення файлу
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'mp3') {
            $errors[] = 'Дозволені лише файли з розширенням .mp3.';
        }
        
        // 2. Перевірка розміру (максимум 20 МБ)
        if ($file['size'] > 20 * 1024 * 1024) {
            $errors[] = 'Розмір MP3 не повинен перевищувати 20 МБ.';
        }
        
        // 3. Глибока перевірка MIME-типу (щоб під виглядом mp3 не залили вірус)
        if (class_exists('finfo')) {
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($file['tmp_name']);
            // Іноді сервери визначають mp3 як octet-stream, тому його теж дозволяємо
            $allowedTypes = array('audio/mpeg', 'audio/mp3', 'application/octet-stream'); 
            
            if (!in_array($mimeType, $allowedTypes, true)) {
                $errors[] = 'Файл не схожий на справжнє MP3-аудіо. Спрацював захист сервера!';
            }
        }

        // Якщо є хоча б одна помилка - виводимо її і зупиняємо скрипт
        if (!empty($errors)) {
            die("<div style='background: #060410; color: white; padding: 20px; font-family: sans-serif;'>
                    <h3 style='color: #ff6bc1;'>Помилка завантаження:</h3>
                    <p>" . implode("<br>", $errors) . "</p>
                    <a href='../upload.php' style='color: #32c8ff;'>Повернутися назад</a>
                 </div>");
        }
        
        // ==========================================
        // БЕЗПЕЧНЕ ЗБЕРЕЖЕННЯ ФАЙЛУ
        // ==========================================
        $target_dir = "../uploads/";
        
        // Створюємо папку, якщо її раптом хтось видалив
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Беремо оригінальну назву без розширення
        $original_name = pathinfo($file["name"], PATHINFO_FILENAME);
        
        // Очищаємо назву: залишаємо тільки букви, цифри, тире та підкреслення. 
        // Це захищає від ін'єкцій та проблем з кодуванням на різних ОС.
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
        
        // Генеруємо фінальне унікальне ім'я (безпечна назва + поточний час у секундах)
        $final_filename = $safe_name . "_" . time() . ".mp3"; 
        $target_file = $target_dir . $final_filename;
        
        // Фінальне переміщення файлу з тимчасової папки сервера у нашу
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            header('Location: ../mymusic.php?success=1');
            exit();
        } else {
            die("Помилка при збереженні файлу. Перевірте права доступу до папки uploads.");
        }
        
    } else {
        // Помилка завантаження (наприклад, файл занадто великий для налаштувань самого PHP)
        die("<div style='background: #060410; color: white; padding: 20px; font-family: sans-serif;'>
                <h3 style='color: #ff6bc1;'>Системна помилка передачі файлу</h3>
                <p>Можливо, файл перевищує ліміти налаштувань сервера (upload_max_filesize).</p>
                <a href='../upload.php' style='color: #32c8ff;'>Повернутися назад</a>
             </div>");
    }
} else {
    // Якщо сюди потрапили не через форму (наприклад, просто ввели адресу в браузері)
    header('Location: ../upload.php');
    exit();
}
?>