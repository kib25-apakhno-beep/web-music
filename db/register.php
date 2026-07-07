<?php
    session_start();
    require_once '../db/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Перевірка, чи паролі збігаються
        if ($password !== $confirm_password) {
            echo "Паролі не збігаються!";
            exit;
        }

        // Хешування пароля
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Підготовка SQL-запиту для вставки даних користувача
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        // Виконання запиту
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            header("Location: ../index.php");
            exit;
        } else {
            echo "Помилка реєстрації: " . $stmt->error;
        }

        // Закриття підготовленого запиту
        $stmt->close();
    }
?>