<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

// Добавление новой группы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_name = trim($_POST['group_name']);
    if (!empty($group_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO `groups` (name) VALUES (?)");
            $stmt->execute([$group_name]);
            echo "<div class='alert alert-success'>Группа добавлена!</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Ошибка при добавлении группы: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Название группы не может быть пустым.</div>";
    }
}

// Получаем список всех групп
$stmt = $pdo->query("SELECT * FROM `groups` ORDER BY id ASC");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Добавить группу</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/icon.png" type="image/x-icon">
</head>

<body style="background: #eeffff">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #333333;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">УИС Техникума</a>
            <span class="text-white ms-auto">Администратор</span>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Добавить и просмотреть группы</h3>
        <div class="row">
            <!-- Форма добавления -->
            <div class="col-md-5">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Добавить группу</h5>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Название группы</label>
                                <input type="text" name="group_name" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить</button>
                            <a href="dashboard.php" class="btn btn-secondary">Назад</a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Таблица с группами -->
            <div class="col-md-7" style="max-height: 80vh; overflow-y: auto;">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Список групп</h5>

                        <?php if ($groups): ?>

                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Название</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groups as $group): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($group['id']) ?></td>
                                            <td><?= htmlspecialchars($group['name']) ?></td>
                                            <td>
                                                <a href="delete_group.php?id=<?= $group['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Вы уверены, что хотите удалить эту группу?');">
                                                    Удалить
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">Групп пока нет.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>