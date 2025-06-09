<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../config.php';

$student_id = (int)$_GET['id'];

// Получаем данные студента из таблиц
$stmt = $pdo->prepare("SELECT s.*, u.login FROM `students` s JOIN `users` u ON s.id = u.id WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Студент не найден.");
}

$groups = $pdo->query("SELECT * FROM `groups`")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать профиль студента</title>
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
        <h3>Редактировать профиль студента</h3>
        <form method="post" action="update_student_full.php">
            <input type="hidden" name="id" value="<?= $student_id ?>">

            <div class="row g-3">
                <!-- ФИО -->
                <div class="col-md-6">
                    <label class="form-label">ФИО</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" class="form-control" required>
                </div>

                <!-- Логин -->
                <div class="col-md-6">
                    <label class="form-label">Логин</label>
                    <input type="text" name="login" value="<?= htmlspecialchars($student['login']) ?>" class="form-control" required>
                </div>

                <!-- Пароль -->
                <div class="col-md-6">
                    <label class="form-label">Пароль (оставьте пустым, если не хотите менять)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <!-- Группа -->
                <div class="col-md-6">
                    <label class="form-label">Группа</label>
                    <select name="group_id" class="form-select" required>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= $group['id'] ?>" <?= $group['id'] == $student['group_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($group['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Пол -->
                <div class="col-md-6">
                    <label class="form-label">Пол</label>
                    <select name="gender" class="form-select" required>
                        <option value="мужской" <?= $student['gender'] === 'мужской' ? 'selected' : '' ?>>Мужской</option>
                        <option value="женский" <?= $student['gender'] === 'женский' ? 'selected' : '' ?>>Женский</option>
                    </select>
                </div>

                <!-- Серия и номер паспорта -->
                <div class="col-md-6">
                    <label class="form-label">Серия и номер паспорта</label>
                    <input type="text" name="passport_series_number" value="<?= htmlspecialchars($student['passport_series_number']) ?>" class="form-control" required>
                </div>

                <!-- Телефон -->
                <div class="col-md-6">
                    <label class="form-label">Телефон</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($student['phone']) ?>" class="form-control">
                </div>

                <!-- Дата рождения -->
                <div class="col-md-6">
                    <label class="form-label">Дата рождения</label>
                    <input type="date" name="dob" value="<?= $student['dob'] ?>" class="form-control">
                </div>

                <!-- Место рождения -->
                <div class="col-md-6">
                    <label class="form-label">Место рождения</label>
                    <input type="text" name="place_of_birth" value="<?= htmlspecialchars($student['place_of_birth']) ?>" class="form-control" required>
                </div>

                <!-- Гражданство -->
                <div class="col-md-6">
                    <label class="form-label">Гражданство</label>
                    <input type="text" name="citizenship" value="<?= htmlspecialchars($student['citizenship']) ?>" class="form-control">
                </div>

                <!-- Адрес регистрации -->
                <div class="col-12">
                    <label class="form-label">Адрес регистрации</label>
                    <textarea name="registration_address" class="form-control" rows="2"><?= htmlspecialchars($student['registration_address']) ?></textarea>
                </div>

                <!-- Фактический адрес -->
                <div class="col-12">
                    <label class="form-label">Фактический адрес</label>
                    <textarea name="actual_address" class="form-control" rows="2"><?= htmlspecialchars($student['actual_address']) ?></textarea>
                </div>

                <!-- Школа -->
                <div class="col-md-6">
                    <label class="form-label">Школа</label>
                    <input type="text" name="school" value="<?= htmlspecialchars($student['school']) ?>" class="form-control">
                </div>

                <!-- Тип школы -->
                <div class="col-md-6">
                    <label class="form-label">Тип школы</label>
                    <select name="school_type" id="schoolTypeSelect" class="form-select">
                        <option value="общеобразовательная средняя" <?= $student['school_type'] === 'общеобразовательная средняя' ? 'selected' : '' ?>>Общеобразовательная средняя</option>
                        <option value="с техническим уклоном (лицей)" <?= $student['school_type'] === 'с техническим уклоном (лицей)' ? 'selected' : '' ?>>С техническим уклоном (лицей)</option>
                        <option value="гимназия (гуманитарная/языковая)" <?= $student['school_type'] === 'гимназия (гуманитарная/языковая)' ? 'selected' : '' ?>>Гимназия (гуманитарная/языковая)</option>
                        <option value="другое" <?= $student['school_type'] === 'другое' ? 'selected' : '' ?>>Другое</option>
                    </select>
                </div>

                <!-- Укажите тип школы -->
                <div class="col-md-6" id="schoolTypeOtherField" style="display: none;">
                    <label class="form-label">Укажите тип школы</label>
                    <input type="text" name="school_type_other" class="form-control" value="<?= htmlspecialchars($student['school_type'] === 'другое' ? $student['school_type_other'] : '') ?>">
                </div>

                <!-- Профиль класса -->
                <div class="col-md-6">
                    <label class="form-label">Профиль класса</label>
                    <input type="text" name="school_profile" value="<?= htmlspecialchars($student['school_profile']) ?>" class="form-control" required>
                </div>

                <!-- Дата окончания школы -->
                <div class="col-md-6">
                    <label class="form-label">Дата окончания школы</label>
                    <input type="date" name="school_end_date" value="<?= $student['school_end_date'] ?>" class="form-control" required>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="form-control">
                </div>

                <!-- СНИЛС -->
                <div class="col-md-6">
                    <label class="form-label">СНИЛС</label>
                    <input type="text" name="snils" value="<?= htmlspecialchars($student['snils']) ?>" class="form-control">
                </div>

                <!-- ИНН -->
                <div class="col-md-6">
                    <label class="form-label">ИНН</label>
                    <input type="text" name="inn" value="<?= htmlspecialchars($student['inn']) ?>" class="form-control">
                </div>

                <!-- Курс -->
                <div class="col-md-6">
                    <label class="form-label">Курс</label>
                    <select name="course" class="form-select" required>
                        <option value="">Выберите курс</option>
                        <option value="1" <?= $student['course'] == 1 ? 'selected' : '' ?>>1</option>
                        <option value="2" <?= $student['course'] == 2 ? 'selected' : '' ?>>2</option>
                        <option value="3" <?= $student['course'] == 3 ? 'selected' : '' ?>>3</option>
                        <option value="4" <?= $student['course'] == 4 ? 'selected' : '' ?>>4</option>
                        <option value="5" <?= $student['course'] == 5 ? 'selected' : '' ?>>5</option>
                    </select>
                </div>

                <!-- Корпус -->
                <div class="col-md-6">
                    <label class="form-label">Корпус</label>
                    <select name="corpus" class="form-select" required>
                        <option value="Центральный" <?= $student['corpus'] === 'Центральный' ? 'selected' : '' ?>>Центральный</option>
                        <option value="Угреша" <?= $student['corpus'] === 'Угреша' ? 'selected' : '' ?>>Угреша</option>
                        <option value="Красково" <?= $student['corpus'] === 'Красково' ? 'selected' : '' ?>>Красково</option>
                        <option value="Гагаринский" <?= $student['corpus'] === 'Гагаринский' ? 'selected' : '' ?>>Гагаринский</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <a href="edit_student_teacher.php?type=student" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('schoolTypeSelect').addEventListener('change', function() {
            var otherField = document.getElementById('schoolTypeOtherField');
            if (this.value === 'другое') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
            }
        });

        // Проверка при загрузке страницы
        window.addEventListener('DOMContentLoaded', function() {
            var select = document.getElementById('schoolTypeSelect');
            var otherField = document.getElementById('schoolTypeOtherField');
            if (select.value === 'другое') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
            }
        });
    </script>
</body>

</html>