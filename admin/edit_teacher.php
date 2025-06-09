<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$teacher_id = (int)$_GET['id'];

// Получаем данные преподавателя из users и teachers
$stmt = $pdo->prepare("SELECT u.id, u.login, t.full_name, t.group_id FROM `users` u JOIN `teachers` t ON u.id = t.id WHERE u.id = ?");
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$teacher) {
    die("Преподаватель не найден.");
}

$groups = $pdo->query("SELECT * FROM `groups`")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $full_name = trim($_POST['full_name']);
    $group_id = (int)$_POST['group_id'];
    $password = trim($_POST['password']);

    // Обновляем логин
    $stmt = $pdo->prepare("UPDATE `users` SET login = ? WHERE id = ?");
    $stmt->execute([$login, $teacher_id]);

    // Если введен новый пароль — обновляем его
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE `users` SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $teacher_id]);
    }

    // Обновляем данные преподавателя
    $stmt = $pdo->prepare("UPDATE `teachers` SET full_name = ?, group_id = ? WHERE id = ?");
    $stmt->execute([$full_name, $group_id, $teacher_id]);

    echo "<div class='alert alert-success'>Данные успешно обновлены!</div>";
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать профиль преподавателя</title>
    <link rel="icon" href="/images/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #eeffff">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #333333;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">УИС Техникума</a>
            <span class="text-white ms-auto">Администратор</span>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Редактировать профиль преподавателя</h3>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">ФИО Преподавателя</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Логин</label>
                <input type="text" name="login" value="<?= htmlspecialchars($teacher['login']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль (оставьте пустым, если не хотите менять)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Группа</label>
                <select name="group_id" class="form-select" required>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>" <?= $group['id'] == $teacher['group_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($group['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Обновить</button>
            <a href="edit_student_teacher.php?type=teacher" class="btn btn-secondary">Назад</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>