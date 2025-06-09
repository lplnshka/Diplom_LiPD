<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../config.php';

// Получаем данные из формы
$full_name = trim($_POST['full_name']);
$phone = preg_replace('/[^\d]/', '', $_POST['phone']);
if (strlen($phone) !== 11 || !preg_match('/^[78]\d{10}$/', $phone)) {
    $errors[] = "Номер телефона должен содержать 11 цифр и начинаться с 7 или 8";
}
$dob = $_POST['dob'] ?? null;
$place_of_birth = trim($_POST['place_of_birth']);
$citizenship = trim($_POST['citizenship']);
$registration_address = trim($_POST['registration_address']);
$actual_address = trim($_POST['actual_address']) ?: $registration_address;
$school = trim($_POST['school']);
$school_type = trim($_POST['school_type'] ?? '');
$school_profile = trim($_POST['school_profile']);
$school_end_date = $_POST['school_end_date'] ?? null;
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$snils = preg_replace('/[^\d]/', '', $_POST['snils']);
if (strlen($snils) !== 11 || !preg_match('/^\d{11}$/', $snils)) {
    $errors[] = "СНИЛС должен содержать 11 цифр";
} else {
    // Проверка контрольной суммы СНИЛС
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $sum += (int)$snils[$i] * (9 - $i);
    }
    $check_digit = ($sum % 101) % 100;
    if ($check_digit !== (int)substr($snils, -2)) {
        $errors[] = "Некорректный СНИЛС (неверная контрольная сумма)";
    }
}
$inn = trim($_POST['inn']) ?: '-';
$group_id = (int)$_POST['group_id'];
$has_material_support = isset($_POST['has_material_support']) ? 1 : 0;

$gender = $_POST['gender'];
$passport_series_number = trim($_POST['passport_series_number']);
$course = (int)$_POST['course'];
$corpus = $_POST['corpus'];

// Валидация обязательных полей
$errors = [];

if (empty($full_name)) $errors[] = "ФИО обязательно";
if (empty($phone)) $errors[] = "Телефон обязателен";
if (empty($place_of_birth)) $errors[] = "Место рождения обязательно";
if (empty($registration_address)) $errors[] = "Адрес регистрации обязателен";
if (empty($school)) $errors[] = "Школа обязательна";
if (empty($school_profile)) $errors[] = "Профиль класса обязателен";
if (empty($school_end_date)) $errors[] = "Дата окончания школы обязательна";
if (!$email) $errors[] = "Введите корректный email";
if (empty($snils)) $errors[] = "СНИЛС обязателен";

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: ../register.php");
    exit;
}

// Генерируем логин и пароль
$login = uniqid('std_');
$password = bin2hex(random_bytes(8));
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Начинаем транзакцию
    $pdo->beginTransaction();

    // 1. Добавляем пользователя
    $stmt = $pdo->prepare("INSERT INTO `users` (login, password, role) VALUES (?, ?, 'student')");
    $stmt->execute([$login, $hashed_password]);
    $student_id = $pdo->lastInsertId();

    // 2. Добавляем студента
    $stmt = $pdo->prepare("INSERT INTO students (
    id, gender, full_name, passport_series_number, phone, dob, place_of_birth, citizenship,
    registration_address, actual_address, school, school_type, school_profile,
    school_end_date, email, snils, inn, group_id, course, corpus, has_material_support
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $student_id,
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
        $has_material_support
    ]);

    // 3. Добавляем заявку на материальную помощь (только если отмечено)
    if ($has_material_support) {
        $reason = $_POST['reason'] ?? 'нет оснований';
        $social_scholarship = isset($_POST['social_scholarship']) ? 1 : 0;
        $documents_provided = isset($_POST['documents_provided']) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO `material_support_requests` (
            student_id, reason, social_scholarship, documents_provided
        ) VALUES (?, ?, ?, ?)");

        $stmt->execute([
            $student_id,
            $reason,
            $social_scholarship,
            $documents_provided
        ]);
    }

    // Фиксируем транзакцию
    $pdo->commit();

    // Сообщение об успехе
    $_SESSION['success_message'] = "Вы успешно зарегистрированы!<br>Логин: <strong>$login</strong><br>Пароль: <strong>$password</strong>";
    header("Location: ../index.php");
    exit;
} catch (Exception $e) {
    // Откатываем транзакцию при ошибке
    $pdo->rollBack();

    error_log($e->getMessage());
    $_SESSION['errors'] = ['Произошла ошибка при регистрации. Пожалуйста, попробуйте еще раз.'];
    $_SESSION['form_data'] = $_POST;
    header("Location: ../register.php");
    echo $e->getMessage();
    exit;
}
