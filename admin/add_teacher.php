<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$groups = $pdo->query("SELECT * FROM `groups`")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $group_id = (int)$_POST['group_id'];

    // Проверка обязательных полей
    if (!$full_name || !$login || !$password || !$group_id) {
        echo "<div class='alert alert-danger'>Все поля обязательны!</div>";
    } else {
        try {
            // Хешируем пароль
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Добавляем пользователя
            $stmt = $pdo->prepare("INSERT INTO `users` (login, password, role) VALUES (?, ?, 'teacher')");
            $stmt->execute([$login, $hashed_password]);

            $teacher_id = $pdo->lastInsertId();

            // Добавляем преподавателя
            $stmt = $pdo->prepare("INSERT INTO `teachers` (id, full_name, group_id) VALUES (?, ?, ?)");
            $stmt->execute([$teacher_id, $full_name, $group_id]);

            echo "<div class='alert alert-success'>
                    Преподаватель успешно добавлен!<br>
                    Логин: <strong>" . htmlspecialchars($login) . "</strong><br>
                  </div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Ошибка при добавлении: " . $e->getMessage() . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Добавить преподавателя</title>
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
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">  <!-- Узкая колонка -->
            <h3 class="text-center mb-4">Добавить преподавателя</h3>
            <form method="post">
            <div class="mb-3">
                <label class="form-label">ФИО Преподавателя</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Логин</label>
                <input type="text" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Пароль</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Группа</label>
                <select name="group_id" class="form-select" required>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Добавить</button>
            <a href="dashboard.php" class="btn btn-secondary">Назад</a>
        </form>
    </div>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>