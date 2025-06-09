<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

// Получаем список студентов с заявками на мат. помощь
$query = "SELECT s.id, s.full_name, s.gender, s.phone, s.dob, s.place_of_birth, s.citizenship,
                 s.registration_address, s.actual_address, s.school, s.school_type, 
                 s.school_profile, s.school_end_date, s.email, s.snils, s.inn, g.id AS group_id, g.name AS group_name, 
                 s.has_material_support, s.corpus, m.reason AS material_reason
          FROM `students` s
          LEFT JOIN `groups` g ON s.group_id = g.id
          LEFT JOIN `material_support_requests` m ON s.id = m.student_id";

$stmt = $pdo->query($query);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Панель преподавателя</title>
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" href="../images/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Фиксированная высота и обработка длинного текста */
    .form-select {
        height: 38px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Равномерное распределение ширины */
    @media (min-width: 992px) {
        .row-filter .col {
            flex: 1;
            min-width: 0;
        }
    }
</style>
</head>

<body style="background: #eeffff">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #333333;">
            <div class="container-fluid">
                    <a class="navbar-brand" href="https://luberteh.ru" target="_blank">УИС Техникума</a>
                    <div class="nav-links ms-auto">
                        <a href="https://t.me/luberteh"><img src="../images/tg.png" class="social-icon"></a>
                        <a href="https://vk.com/luberteh"><img src="../images/vk.png" class="social-icon"></a>
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
        <h3>Список студентов</h3>

        <!-- Форма фильтров -->
<div class="row mb-3 g-2">
    <div class="col">
        <select name="gender" class="form-select" onchange="filterStudents(this.value, 'gender')">
            <option value="">По полу</option>
            <option value="мужской">Мужской</option>
            <option value="женский">Женский</option>
        </select>
    </div>
    <div class="col">
        <select name="age_range" class="form-select" onchange="filterStudents(this.value, 'age')">
            <option value="">По возрасту</option>
            <option value="under_18">До 18 лет</option>
            <option value="18_20">18–20 лет</option>
            <option value="over_20">Старше 20</option>
        </select>
    </div>
    <div class="col">
        <select name="material_support" class="form-select" onchange="filterStudents(this.value, 'material')">
            <option value="">Материальная помощь</option>
            <option value="yes">Нуждается</option>
            <option value="no">Не нуждается</option>
        </select>
    </div>
    <div class="col">
        <select name="group_id" class="form-select" onchange="filterStudents(this.value, 'group')">
            <option value="">Выберите группу</option>
            <?php foreach ($pdo->query("SELECT * FROM `groups`") as $group): ?>
                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col">
        <select name="corpus" class="form-select" onchange="filterStudents(this.value, 'corpus')">
            <option value="">По корпусу</option>
            <option value="Центральный">Центральный</option>
            <option value="Угреша">Угреша</option>
            <option value="Красково">Красково</option>
            <option value="Гагаринский">Гагаринский</option>
        </select>
    </div>
</div>

        <!-- Таблица студентов -->
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ФИО</th>
                    <th>Группа</th>
                    <th>Возраст</th>
                    <th>Материальная помощь</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student):
                        // Рассчитываем возраст
                        $dob = new DateTime($student['dob']);
                        $now = new DateTime();
                        $age = $dob->diff($now)->y;

                        // Материальная помощь
                        $material_support_badge = $student['has_material_support']
                            ? '<span class="badge bg-warning text-dark">Нуждается</span>'
                            : '<span class="badge bg-secondary">Нет</span>';
                    ?>
                        <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#studentCardModal"
                            data-id="<?= $student['id'] ?>"
                            data-full-name="<?= htmlspecialchars($student['full_name']) ?>"
                            data-gender="<?= htmlspecialchars($student['gender']) ?>"
                            data-phone="<?= htmlspecialchars($student['phone']) ?>"
                            data-dob="<?= $student['dob'] ?>"
                            data-age="<?= $age ?>"
                            data-place-of-birth="<?= htmlspecialchars($student['place_of_birth']) ?>"
                            data-citizenship="<?= htmlspecialchars($student['citizenship']) ?>"
                            data-registration-address="<?= htmlspecialchars($student['registration_address']) ?>"
                            data-actual-address="<?= htmlspecialchars($student['actual_address']) ?>"
                            data-school="<?= htmlspecialchars($student['school']) ?>"
                            data-school-type="<?= htmlspecialchars($student['school_type']) ?>"
                            data-school-profile="<?= htmlspecialchars($student['school_profile']) ?>"
                            data-school-end-date="<?= $student['school_end_date'] ?>"
                            data-email="<?= htmlspecialchars($student['email']) ?>"
                            data-snils="<?= htmlspecialchars($student['snils']) ?>"
                            data-inn="<?= htmlspecialchars($student['inn']) ?>"
                            data-group="<?= htmlspecialchars($student['group_name']) ?>"
                            data-group-id="<?= $student['group_id'] ?: '' ?>"
                            data-material-reason="<?= htmlspecialchars($student['material_reason'] ?? '-') ?>"
                            data-has-material-support="<?= $student['has_material_support'] ? 'yes' : 'no' ?>"
                            data-corpus="<?= htmlspecialchars($student['corpus']) ?>">
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['group_name'] ?? '-') ?></td>
                            <td><?= $age ?> лет</td>
                            <td><?= $material_support_badge ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">Студенты не найдены</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Модальное окно с деталями студента -->
    <div class="modal fade" id="studentCardModal" tabindex="-1" aria-labelledby="studentCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                        <li class="list-group-item"><strong>Телефон:</strong> <span id="modal-phone"></span></li>
                        <li class="list-group-item"><strong>Дата рождения:</strong> <span id="modal-dob"></span></li>
                        <li class="list-group-item"><strong>Возраст:</strong> <span id="modal-age"></span></li>
                        <li class="list-group-item"><strong>Место рождения:</strong> <span id="modal-place-of-birth"></span></li>
                        <li class="list-group-item"><strong>Гражданство:</strong> <span id="modal-citizenship"></span></li>
                        <li class="list-group-item"><strong>Адрес регистрации:</strong> <span id="modal-registration-address"></span></li>
                        <li class="list-group-item"><strong>Фактический адрес:</strong> <span id="modal-actual-address"></span></li>
                        <li class="list-group-item"><strong>Школа:</strong> <span id="modal-school"></span></li>
                        <li class="list-group-item"><strong>Тип школы:</strong> <span id="modal-school-type"></span></li>
                        <li class="list-group-item"><strong>Профиль класса:</strong> <span id="modal-school-profile"></span></li>
                        <li class="list-group-item"><strong>Дата окончания:</strong> <span id="modal-school-end-date"></span></li>
                        <li class="list-group-item"><strong>Email:</strong> <span id="modal-email"></span></li>
                        <li class="list-group-item"><strong>СНИЛС:</strong> <span id="modal-snils"></span></li>
                        <li class="list-group-item"><strong>ИНН:</strong> <span id="modal-inn"></span></li>
                        <li class="list-group-item"><strong>Группа:</strong> <span id="modal-group"></span></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Заполняем модальное окно данными
        document.querySelectorAll('tr[data-bs-toggle="modal"]').forEach(row => {
            row.addEventListener('click', function() {
                const studentData = {
                    id: this.getAttribute('data-id'),
                    fullName: this.getAttribute('data-full-name'),
                    gender: this.getAttribute('data-gender'),
                    phone: this.getAttribute('data-phone'),
                    dob: this.getAttribute('data-dob'),
                    age: this.getAttribute('data-age'),
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
                    materialReason: this.getAttribute('data-material-reason'),
                };

                document.getElementById('modal-id').textContent = studentData.id;
                document.getElementById('modal-full-name').textContent = studentData.fullName;
                document.getElementById('modal-gender').textContent = studentData.gender === 'мужской' ? 'Мужской' : 'Женский';
                document.getElementById('modal-phone').textContent = studentData.phone;
                document.getElementById('modal-dob').textContent = studentData.dob;
                document.getElementById('modal-age').textContent = studentData.age + ' лет';
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
                document.getElementById('modal-group').textContent = studentData.group || '—';
                document.getElementById('modal-has-material-support').textContent = studentData.hasMaterialSupport;
                document.getElementById('modal-reason').textContent = studentData.materialReason || '—';
            });
        });

        function filterStudents(value, type) {
            const rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                let show = true;
                const gender = row.getAttribute("data-gender");
                const age = parseInt(row.getAttribute("data-age"));
                const matSupport = row.getAttribute("data-has-material-support");
                const groupId = row.getAttribute("data-group-id");
                const currentMatText = row.querySelector("td:nth-child(4) .badge")?.textContent.trim();
                const corpus = row.getAttribute("data-corpus");

                // Фильтры
                if (type === 'gender' && value && gender !== value) {
                    show = false;
                }

                if (type === 'age') {
                    if (value === 'under_18' && age >= 18) show = false;
                    if (value === '18_20' && !(age >= 18 && age <= 20)) show = false;
                    if (value === 'over_20' && age <= 20) show = false;
                }

                if (type === 'material') {
                    if (value === 'yes' && currentMatText !== 'Нуждается') show = false;
                    if (value === 'no' && currentMatText !== 'Нет') show = false;
                }

                if (type === 'group' && value && groupId !== value) {
                    show = false;
                }

                if (type === 'corpus' && value && corpus !== value) {
                    show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        }
    </script>
</body>

</html>