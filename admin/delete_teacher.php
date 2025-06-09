<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$teacher_id = (int)$_GET['id'];

// Удаляем из teachers и users (каскадно, если связи настроены)
try {
    $stmt = $pdo->prepare("DELETE FROM `teachers` WHERE id = ?");
    $stmt->execute([$teacher_id]);

    $stmt = $pdo->prepare("DELETE FROM `users` WHERE id = ?");
    $stmt->execute([$teacher_id]);

    header("Location: edit_student_teacher.php?type=teacher");
    exit;
} catch (PDOException $e) {
    die("Ошибка при удалении: " . $e->getMessage());
}
