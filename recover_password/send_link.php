<?php
session_start();
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_or_email = trim($_POST['login_or_email']);

    // Поиск студента по email или login
    $stmt = $pdo->prepare("SELECT * FROM `users` u JOIN `students` s ON u.id = s.id WHERE u.login = ? OR s.email = ?");
    $stmt->execute([$login_or_email, $login_or_email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Пользователь не найден.");
    }

    // Генерация токена
    $token = bin2hex(random_bytes(50));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Сохраняем токен в БД
    $stmt = $pdo->prepare("UPDATE `users` SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->execute([$token, $expires, $student['id']]);

    // Формируем ссылку
    $reset_link = "http://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/reset.php?token=$token";

    // Имитация отправки письма (замените на реальную отправку)
    echo "<div class='container mt-4'>";
    echo "<h4>Ссылка для восстановления:</h4>";
    echo "<p><a href='$reset_link'>$reset_link</a></p>";
    echo "<p>Эта ссылка действительна 1 час.</p>";
    echo "</div>";

    exit;
}
