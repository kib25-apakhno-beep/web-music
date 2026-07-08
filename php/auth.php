<?php
session_start();

if (isset($_POST['username']) && !empty(trim($_POST['username']))) {
    $_SESSION['user_name'] = htmlspecialchars(trim($_POST['username']));
} 
else if (isset($_POST['email']) && !empty(trim($_POST['email']))) {
    $email_parts = explode('@', $_POST['email']);
    $_SESSION['user_name'] = htmlspecialchars(ucfirst($email_parts[0]));
} else {
    $_SESSION['user_name'] = 'Гість';
}

$_SESSION['user_id'] = 1;

header('Location: ../index.php');
exit();
?>