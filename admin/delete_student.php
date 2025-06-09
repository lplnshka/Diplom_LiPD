<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$student_id = (int)$_GET['id'];

try {
    // Удаляем из таблицы students (каскадно удалятся заявки)
    $stmt = $pdo->prepare("DELETE FROM `students` WHERE id = ?");
    $stmt->execute([$student_id]);

    // Удаляем из users (там тоже может быть запись)
    $stmt = $pdo->prepare("DELETE FROM `users` WHERE id = ?");
    $stmt->execute([$student_id]);

    header("Location: edit_student_teacher.php?type=student");
    exit;
} catch (PDOException $e) {
    die("Ошибка при удалении: " . $e->getMessage());
}
