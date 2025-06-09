<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Поиск пользователя в БД (без проверки роли)
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        // Перенаправление по ролям (роль берется из БД)
        switch ($user['role']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'teacher':
                header("Location: teacher/dashboard.php");
                break;
            case 'student':
                header("Location: student/profile.php");
                break;
            default:
                // Если роль не распознана, перенаправляем с сообщением
                $_SESSION['error_message'] = 'Неизвестная роль пользователя';
                header("Location: index.php");
        }
        exit;
    } else {
        $_SESSION['error_message'] = 'Неверный логин или пароль';
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}