<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        die("Пароли не совпадают.");
    }

    // Проверяем токен
    $stmt = $pdo->prepare("SELECT id FROM `users` WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Неверный или истёкший токен.");
    }

    // Хешируем пароль
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Обновляем пароль и очищаем токены
    $stmt = $pdo->prepare("UPDATE `users` SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->execute([$hashed_password, $user['id']]);

    echo "<div class='container mt-4'><h4>Пароль успешно изменён!</h4><a href='../../index.php' class='btn btn-primary'>Войти</a></div>";
}
