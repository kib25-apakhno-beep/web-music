<?php

    session_start();
    require_once '../db/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Підготовка SQL-запиту для отримання користувача за email
        $stmt = $conn->prepare("SELECT id_user AS id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Перевірка пароля
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../catalog.php");
                exit;
            } else {
                echo "Невірний пароль!";
            }
        } else {
            echo "Користувач з таким email не знайдений!";
        }

        // Закриття підготовленого запиту
        $stmt->close();
    }

?>