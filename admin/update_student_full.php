<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$student_id = (int)$_POST['id'];
$login = trim($_POST['login']);
$full_name = trim($_POST['full_name']);
$password = trim($_POST['password']);
$group_id = (int)$_POST['group_id'];

// Дополнительные поля
$phone = trim($_POST['phone']);
$dob = $_POST['dob'];
$place_of_birth = trim($_POST['place_of_birth']);
$citizenship = trim($_POST['citizenship']);
$registration_address = trim($_POST['registration_address']);
$actual_address = trim($_POST['actual_address']);
$school = trim($_POST['school']);
$school_type = trim($_POST['school_type']);
$school_profile = trim($_POST['school_profile']);
$school_end_date = $_POST['school_end_date'];
$email = trim($_POST['email']);
$snils = trim($_POST['snils']);
$inn = trim($_POST['inn']);
$gender = $_POST['gender'];
$passport_series_number = trim($_POST['passport_series_number']);
$course = (int)$_POST['course'];
$corpus = $_POST['corpus'];

try {
    // Обновляем логин
    $stmt = $pdo->prepare("UPDATE `users` SET login = ? WHERE id = ?");
    $stmt->execute([$login, $student_id]);

    // Если указан пароль — обновляем его
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE `users` SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $student_id]);
    }

    // Обновляем основные данные студента
    // Обновляем студента
    $stmt = $pdo->prepare("UPDATE students SET 
    gender = ?, full_name = ?, passport_series_number = ?, phone = ?, dob = ?, place_of_birth = ?, citizenship = ?, 
    registration_address = ?, actual_address = ?, school = ?, school_type = ?, school_profile = ?, 
    school_end_date = ?, email = ?, snils = ?, inn = ?, group_id = ?, course = ?, corpus = ? WHERE id = ?");
    $stmt->execute([
        $gender,
        $full_name,
        $passport_series_number,
        $phone,
        $dob,
        $place_of_birth,
        $citizenship,
        $registration_address,
        $actual_address,
        $school,
        $school_type,
        $school_profile,
        $school_end_date,
        $email,
        $snils,
        $inn,
        $group_id,
        $course,
        $corpus,
        $student_id
    ]);

    header("Location: edit_student_teacher.php?type=student");
    exit;
} catch (PDOException $e) {
    die("Ошибка при обновлении: " . $e->getMessage());
}
