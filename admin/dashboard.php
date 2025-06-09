<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/icon.png" type="image/x-icon">
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
        <img src="../images/logo.png" alt="Логотип" class="logo-img me-3" style="height:96px;">
        <div class="header-text">
            <div class="navbar-text">ГБПОУ МО
            <br>Люберецкий техникум
            <br>имени Героя Советского Союза,
            <br>лётчика-космонавта Ю. А. Гагарина</div>
        </div>
    </div>
    <div class="container mt-4">
        <h3>Панель администратора</h3>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>Преподаватели</h5>
                    <p>Добавление и управление преподавателями.</p>
                    <a href="edit_student_teacher.php?type=teacher" class="btn btn-outline-info">Управление преподавателями</a><br>
                    <a href="add_teacher.php" class="btn btn-outline-success">Добавить преподавателя</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>Группы</h5>
                    <p>Добавляйте и управляйте учебными группами.</p>
                    <a href="add_group.php" class="btn btn-outline-primary">Добавить группу</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>Студенты</h5>
                    <p>Просмотр, редактирование и удаление студентов.</p>
                    <a href="edit_student_teacher.php?type=student" class="btn btn-outline-secondary">Управление студентами</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>