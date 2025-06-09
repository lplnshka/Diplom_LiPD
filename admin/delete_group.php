<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$group_id = (int)$_GET['id'];

try {
    // Удаляем группу
    $pdo->beginTransaction();
    
    // 1. Отвязываем студентов
    $stmt = $pdo->prepare("UPDATE students SET group_id = NULL WHERE group_id = ?");
    $stmt->execute([$group_id]);
    
    // 2. Удаляем группу
    $stmt = $pdo->prepare("DELETE FROM `groups` WHERE id = ?");
    $stmt->execute([$group_id]);
    
    $pdo->commit();
    $_SESSION['success_message'] = "Группа удалена";

    header("Location: add_group.php");
    exit;
} catch (PDOException $e) {
    die("Ошибка при удалении группы: " . $e->getMessage());
}
