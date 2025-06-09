<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$type = $_GET['type'] ?? 'student'; // student или teacher

$users = [];
$groups = $pdo->query("SELECT * FROM `groups`")->fetchAll(PDO::FETCH_ASSOC);

if ($type === 'student') {
    $stmt = $pdo->query("
        SELECT s.id, s.full_name, s.phone, s.dob, s.place_of_birth, s.citizenship,
               s.registration_address, s.actual_address, s.school, s.school_type,
               s.school_profile, s.school_end_date, s.email, s.snils, s.inn, g.name AS group_name,
               s.has_material_support, m.reason AS material_reason,s.gender, s.passport_series_number, s.course, s.corpus
        FROM `students` s
        LEFT JOIN `groups` g ON s.group_id = g.id
        LEFT JOIN `material_support_requests` m ON s.id = m.student_id
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("
        SELECT t.id, t.full_name, g.name AS group_name
        FROM `teachers` t
        LEFT JOIN `groups` g ON t.group_id = g.id
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Управление <?= $type === 'student' ? 'студентами' : 'преподавателями' ?></title>
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
        <h3><?= $type === 'student' ? 'Студенты' : 'Преподаватели' ?></h3>

        <?php if ($type === 'student'): ?>
            <p class="text-muted">На этой странице вы можете просматривать, редактировать и удалять данные студентов.</p>
        <?php endif; ?>

        <!-- Таблица -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Группа</th>
                    <th>Пол</th>
                    <?php if ($type === 'student'): ?>
                        <th>Материальная помощь</th>
                    <?php endif; ?>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user):
                    if ($type === 'student'):
                        $materialSupportBadge = $user['has_material_support']
                            ? '<span class="badge bg-warning text-dark">Нуждается</span>'
                            : '<span class="badge bg-secondary">Нет</span>';
                    endif;
                ?>
                    <tr
                        <?= $type === 'student' ? 'style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#studentCardModal"' : '' ?>

                        <?= $type === 'student' ? "data-id=\"{$user['id']}\" 
                    data-full-name=\"" . htmlspecialchars($user['full_name']) . "\" 
                    data-phone=\"" . htmlspecialchars($user['phone']) . "\" 
                    data-dob=\"{$user['dob']}\" 
                    data-gender=\"{$user['gender']}\" 
                    data-passport-series-number=\"{$user['passport_series_number']}\" 
                    data-course=\"{$user['course']}\" 
                    data-corpus=\"{$user['corpus']}\" 
                    data-place-of-birth=\"" . htmlspecialchars($user['place_of_birth']) . "\" 
                    data-citizenship=\"" . htmlspecialchars($user['citizenship']) . "\" 
                    data-registration-address=\"" . htmlspecialchars($user['registration_address']) . "\" 
                    data-actual-address=\"" . htmlspecialchars($user['actual_address']) . "\" 
                    data-school=\"" . htmlspecialchars($user['school']) . "\" 
                    data-school-type=\"" . htmlspecialchars($user['school_type']) . "\" 
                    data-school-profile=\"" . htmlspecialchars($user['school_profile']) . "\" 
                    data-school-end-date=\"{$user['school_end_date']}\" 
                    data-email=\"" . htmlspecialchars($user['email']) . "\" 
                    data-snils=\"" . htmlspecialchars($user['snils']) . "\" 
                    data-inn=\"" . htmlspecialchars($user['inn']) . "\" 
                    data-group=\"" . htmlspecialchars($user['group_name'] ?: '-') . "\" 
                    data-has-material-support=\"" . ($user['has_material_support'] ? 'yes' : 'no') . "\" 
                    data-material-reason=\"" . htmlspecialchars($user['material_reason'] ?: '-') . "\"" : '' ?>>

                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['group_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($user['gender'] ?? '-') ?></td>
                        <?php if ($type === 'student'): ?>
                            <td><?= $materialSupportBadge ?></td>
                        <?php endif; ?>
                        <td>
                            <?php if ($type === 'student'): ?>
                                <a href="edit_student.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                                <a href="delete_student.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                            <?php elseif ($type === 'teacher'): ?>
                                <a href="edit_teacher.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                                <a href="delete_teacher.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">Удалить</a>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="view_material_requests.php" class="btn btn-secondary">Заявки на мат помощь</a>
        <a href="dashboard.php" class="btn btn-secondary">Назад</a>
    </div>

    <!-- Модальное окно только для студентов -->

    <?php if ($type === 'student'): ?>
        <div class="modal fade" id="studentCardModal" tabindex="-1" aria-labelledby="studentCardModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="studentCardModalLabel">Карточка студента</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>ID:</strong> <span id="modal-id"></span></li>
                            <li class="list-group-item"><strong>ФИО:</strong> <span id="modal-full-name"></span></li>
                            <li class="list-group-item"><strong>Пол:</strong> <span id="modal-gender"></span></li>
                            <li class="list-group-item"><strong>Серия и номер паспорта:</strong> <span id="modal-passport-series-number"></span></li>
                            <li class="list-group-item"><strong>Телефон:</strong> <span id="modal-phone"></span></li>
                            <li class="list-group-item"><strong>Дата рождения:</strong> <span id="modal-dob"></span></li>
                            <li class="list-group-item"><strong>Место рождения:</strong> <span id="modal-place-of-birth"></span></li>
                            <li class="list-group-item"><strong>Гражданство:</strong> <span id="modal-citizenship"></span></li>
                            <li class="list-group-item"><strong>Адрес регистрации:</strong> <span id="modal-registration-address"></span></li>
                            <li class="list-group-item"><strong>Фактический адрес:</strong> <span id="modal-actual-address"></span></li>
                            <li class="list-group-item"><strong>Школа:</strong> <span id="modal-school"></span></li>
                            <li class="list-group-item"><strong>Тип школы:</strong> <span id="modal-school-type"></span></li>
                            <li class="list-group-item"><strong>Профиль класса:</strong> <span id="modal-school-profile"></span></li>
                            <li class="list-group-item"><strong>Дата окончания школы:</strong> <span id="modal-school-end-date"></span></li>
                            <li class="list-group-item"><strong>Email:</strong> <span id="modal-email"></span></li>
                            <li class="list-group-item"><strong>СНИЛС:</strong> <span id="modal-snils"></span></li>
                            <li class="list-group-item"><strong>ИНН:</strong> <span id="modal-inn"></span></li>
                            <li class="list-group-item"><strong>Группа:</strong> <span id="modal-group"></span></li>
                            <li class="list-group-item"><strong>Курс:</strong> <span id="modal-course"></span></li>
                            <li class="list-group-item"><strong>Корпус:</strong> <span id="modal-corpus"></span></li>
                            <li class="list-group-item"><strong>Материальная помощь:</strong> <span id="modal-has-material-support"></span></li>
                            <li class="list-group-item"><strong>Основание:</strong> <span id="modal-reason"></span></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($type === 'student'): ?>
        <script>
            document.querySelectorAll('tr[data-bs-toggle="modal"]').forEach(row => {
                row.addEventListener('click', function() {
                    const studentData = {
                        id: this.getAttribute('data-id'),
                        fullName: this.getAttribute('data-full-name'),
                        phone: this.getAttribute('data-phone'),
                        dob: this.getAttribute('data-dob'),
                        placeOfBirth: this.getAttribute('data-place-of-birth'),
                        citizenship: this.getAttribute('data-citizenship'),
                        registrationAddress: this.getAttribute('data-registration-address'),
                        actualAddress: this.getAttribute('data-actual-address'),
                        school: this.getAttribute('data-school'),
                        schoolType: this.getAttribute('data-school-type'),
                        schoolProfile: this.getAttribute('data-school-profile'),
                        schoolEndDate: this.getAttribute('data-school-end-date'),
                        email: this.getAttribute('data-email'),
                        snils: this.getAttribute('data-snils'),
                        inn: this.getAttribute('data-inn'),
                        group: this.getAttribute('data-group'),
                        hasMaterialSupport: this.getAttribute('data-has-material-support') === 'yes' ? 'Нуждается' : 'Нет',
                        reason: this.getAttribute('data-material-reason'),
                        gender: this.getAttribute('data-gender'),
                        passportSeriesNumber: this.getAttribute('data-passport-series-number'),
                        course: this.getAttribute('data-course'),
                        corpus: this.getAttribute('data-corpus')
                    };

                    // Заполняем модальное окно
                    document.getElementById('modal-id').textContent = studentData.id;
                    document.getElementById('modal-full-name').textContent = studentData.fullName;
                    document.getElementById('modal-phone').textContent = studentData.phone;
                    document.getElementById('modal-dob').textContent = studentData.dob;
                    document.getElementById('modal-place-of-birth').textContent = studentData.placeOfBirth;
                    document.getElementById('modal-citizenship').textContent = studentData.citizenship;
                    document.getElementById('modal-registration-address').textContent = studentData.registrationAddress;
                    document.getElementById('modal-actual-address').textContent = studentData.actualAddress;
                    document.getElementById('modal-school').textContent = studentData.school;
                    document.getElementById('modal-school-type').textContent = studentData.schoolType;
                    document.getElementById('modal-school-profile').textContent = studentData.schoolProfile;
                    document.getElementById('modal-school-end-date').textContent = studentData.schoolEndDate;
                    document.getElementById('modal-email').textContent = studentData.email;
                    document.getElementById('modal-snils').textContent = studentData.snils;
                    document.getElementById('modal-inn').textContent = studentData.inn;
                    document.getElementById('modal-group').textContent = studentData.group;
                    document.getElementById('modal-has-material-support').textContent = studentData.hasMaterialSupport;
                    document.getElementById('modal-reason').textContent = studentData.reason;
                    document.getElementById('modal-gender').textContent = studentData.gender || '-';
                    document.getElementById('modal-passport-series-number').textContent = studentData.passportSeriesNumber || '-';
                    document.getElementById('modal-course').textContent = studentData.course || '-';
                    document.getElementById('modal-corpus').textContent = studentData.corpus || '-';
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>