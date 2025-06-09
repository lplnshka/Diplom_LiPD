<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';
$student_id = $_SESSION['user']['id'];

// Проверяем, есть ли уже заявка
$stmt = $pdo->prepare("SELECT * FROM `material_support_requests` WHERE student_id = ?");
$stmt->execute([$student_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = $_POST['reason'];
    $social_scholarship = isset($_POST['social_scholarship']) ? 1 : 0;
    $documents_provided = isset($_POST['documents_provided']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO `material_support_requests` (
        student_id, reason, social_scholarship, documents_provided, submitted
    ) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([
        $student_id,
        $reason,
        $social_scholarship,
        $documents_provided
    ]);

    echo "<div class='alert alert-success'>Заявка успешно отправлена!</div>";
}

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Материальная помощь</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">УИС Техникума</a>
            <span class="text-white ms-auto">Студент</span>
        </div>
    </nav>

    <div class="container mt-4">
        <h3>Подача заявки на материальную помощь</h3>

        <?php if ($request): ?>
            <div class="alert alert-info">Вы уже подали заявку.</div>
        <?php else: ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Основание для помощи</label>
                    <div class="form-check">
                        <input type="radio" name="reason" value="нет" class="form-check-input" checked>
                        <label class="form-check-label">Нет оснований</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="reason" value="неполная семья" class="form-check-input">
                        <label class="form-check-label">Воспитываюсь в неполной семье</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="reason" value="многодетная семья" class="form-check-input">
                        <label class="form-check-label">Воспитываюсь в многодетной семье</label>
                    </div>
                    <!-- добавь остальные варианты по аналогии -->
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="social_scholarship" id="scholarshipCheck" class="form-check-input">
                    <label for="scholarshipCheck" class="form-check-label">Получаю социальную стипендию</label>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="documents_provided" id="docsCheck" class="form-check-input" required>
                    <label for="docsCheck" class="form-check-label">Предоставлю документы (паспорт, СНИЛС и т.д.)</label>
                </div>

                <button type="submit" class="btn btn-success">Отправить заявку</button>
                <a href="profile.php" class="btn btn-secondary">Назад</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>