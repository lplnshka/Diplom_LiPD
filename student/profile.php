<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$student_id = $_SESSION['user']['id'];

// Получаем данные студента
$stmt = $pdo->prepare("SELECT * FROM `students` WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Студент не найден.");
}

// Проверяем возраст для обновления паспорта
$dob = new DateTime($student['dob']);
$now = new DateTime();
$age = $dob->diff($now)->y;
$needsPassportUpdate = $age >= 20;

// Получаем заявку на материальную помощь
$stmt = $pdo->prepare("SELECT * FROM `material_support_requests` WHERE student_id = ?");
$stmt->execute([$student_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Личный профиль</title>
    <link rel="icon" href="../images/icon.png" type="image/x-icon">
    <link href="../css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #eeffff">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #333333;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">УИС Техникума</a>
            <div class="d-flex ms-auto">
                <a href="../logout.php" class="btn btn-outline-light">Выйти</a>
            </div>
        </div>
    </nav>
    <div class="header-container d-flex align-items-center justify-content-center pt-2 pb-2 mb-0">
        <img src="../images/logo.png" alt="Логотип" class="logo-img me-3">
        <div class="header-text">
            <div class="navbar-text">ГБПОУ МО
                <br>Люберецкий техникум
                <br>имени Героя Советского Союза,
                <br>лётчика-космонавта Ю. А. Гагарина
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <h3>Личный профиль</h3>

        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item"><strong>ID:</strong> <?= $student['id'] ?></li>
            <li class="list-group-item"><strong>Пол:</strong> <?= $student['gender'] ?></li>
            <li class="list-group-item"><strong>ФИО:</strong> <?= htmlspecialchars($student['full_name']) ?></li>
            <li class="list-group-item"><strong>Телефон:</strong> <?= htmlspecialchars($student['phone']) ?></li>
            <li class="list-group-item"><strong>Дата рождения:</strong> <?= $student['dob'] ?></li>
            <li class="list-group-item"><strong>Место рождения:</strong> <?= htmlspecialchars($student['place_of_birth']) ?></li>
            <li class="list-group-item"><strong>Гражданство:</strong> <?= htmlspecialchars($student['citizenship']) ?></li>
            <li class="list-group-item"><strong>Адрес регистрации:</strong> <?= htmlspecialchars($student['registration_address']) ?></li>
            <li class="list-group-item"><strong>Фактический адрес:</strong> <?= htmlspecialchars($student['actual_address']) ?></li>
            <li class="list-group-item"><strong>Школа:</strong> <?= htmlspecialchars($student['school']) ?></li>
            <li class="list-group-item"><strong>Тип школы:</strong> <?= htmlspecialchars($student['school_type']) ?></li>
            <li class="list-group-item"><strong>Профиль класса:</strong> <?= htmlspecialchars($student['school_profile']) ?></li>
            <li class="list-group-item"><strong>Дата окончания школы:</strong> <?= $student['school_end_date'] ?></li>
            <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></li>
            <li class="list-group-item"><strong>СНИЛС:</strong> <?= htmlspecialchars($student['snils']) ?></li>
            <li class="list-group-item"><strong>Паспорт:</strong> <?= htmlspecialchars($student['passport_series_number']) ?></li>
            <li class="list-group-item"><strong>ИНН:</strong> <?= htmlspecialchars($student['inn']) ?></li>
            <li class="list-group-item"><strong>Группа:</strong>
                <?php
                $group_name = '';
                if ($student['group_id']) {
                    $stmt = $pdo->prepare("SELECT name FROM `groups` WHERE id = ?");
                    $stmt->execute([$student['group_id']]);
                    $group = $stmt->fetch(PDO::FETCH_ASSOC);
                    $group_name = $group ? htmlspecialchars($group['name']) : '-';
                } else {
                    $group_name = '-';
                }
                ?>
                <?= $group_name ?>
            </li>
            <li class="list-group-item"><strong>Курс:</strong> <?= htmlspecialchars($student['course']) ?></li>
            <li class="list-group-item"><strong>Корпус:</strong> <?= htmlspecialchars($student['corpus']) ?></li>
            <li class="list-group-item"><strong>Материальная помощь:</strong>
                <?= $student['has_material_support'] ? 'Да' : 'Нет' ?>
            </li>
        </ul>

        <!-- Заявка на мат. помощь -->
        <?php if ($student['has_material_support'] && $request): ?>
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Заявка на материальную помощь</h5>
                    <p class="card-text"><?= htmlspecialchars($request['reason']) ?></p>
                    <small>Подано: <?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></small>
                </div>
            </div>
        <?php elseif ($student['has_material_support']): ?>
            <div class="alert alert-warning mt-3">
                ⚠️ Вы отметили, что нуждаетесь в материальной помощи, но заявка не найдена.
            </div>
        <?php endif; ?>

        <!-- Уведомление о паспорте -->
        <?php if ($needsPassportUpdate): ?>
            <div class="alert alert-danger mt-4">
                ⚠️ Вам исполнилось 20 лет. Обновите данные паспорта!
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>