<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$stmt = $pdo->query("
    SELECT s.full_name, m.reason, m.social_scholarship, m.documents_provided, m.created_at 
    FROM material_support_requests m
    JOIN `students` s ON m.student_id = s.id
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Заявки на материальную помощь</title>
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
        <h3>Заявки на материальную помощь</h3>
        <table class="table table-striped table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>ФИО студента</th>
                    <th>Основание</th>
                    <th>Социальная стипендия</th>
                    <th>Документы предоставлены</th>
                    <th>Дата подачи</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['full_name']) ?></td>
                        <td><?= htmlspecialchars($r['reason']) ?></td>
                        <td><?= $r['social_scholarship'] ? 'Да' : 'Нет' ?></td>
                        <td><?= $r['documents_provided'] ? 'Да' : 'Нет' ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="edit_student_teacher.php?type=student" class="btn btn-secondary">Назад</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>