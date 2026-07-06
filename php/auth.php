<?php
session_start();

// Імітуємо успішний вхід в акаунт
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Софія Богданова';

// Перенаправляємо на головну
header('Location: ../index.php');
exit();
?>