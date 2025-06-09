<?php
session_start();
if (!empty($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
}
include 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Регистрация студента</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="/images/icon.png" type="image/x-icon">
</head>

<body style="background: #eeffff">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #333333;">
            <div class="container-fluid">
                <a class="navbar-brand" href="https://luberteh.ru" target="_blank">УИС Техникума</a>
            <div class="nav-links ms-auto">
                <a href="https://t.me/luberteh"><img src="images/tg.png" class="social-icon"></a>
                <a href="https://vk.com/luberteh"><img src="images/vk.png" class="social-icon"></a>
            </div>
        </div>
    </nav>
    <div class="header-container d-flex align-items-center justify-content-center pt-2 pb-2 mb-0">
        <img src="images/logo.png" alt="Логотип" class="logo-img me-3">
        <div class="header-text">
            <div class="navbar-text">ГБПОУ МО
                <br>Люберецкий техникум
                <br>имени Героя Советского Союза,
                <br>лётчика-космонавта Ю. А. Гагарина
            </div>
        </div>
    </div>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-6"> <!-- Уменьшаем ширину колонки -->
                <div class="card shadow-sm p-4" style="max-width: 700px; margin: 0 auto; background: #faffff; border: none;"> <!-- Устанавливаем max-width -->
                    <h2 class="mb-4 text-center">Регистрация</h2>
                    <form method="post" action="student/register_process.php">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">ФИО</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Дата рождения</label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <!-- Пол -->
                            <div class="col-md-6">
                                <label class="form-label">Пол</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Выберите пол</option>
                                    <option value="мужской">Мужской</option>
                                    <option value="женский">Женский</option>
                                </select>
                            </div>
                            <div class="col-md-6" class="justify-content-center">
                                <label class="form-label">Телефон</label>
                                <input type="tel"
                                    name="phone"
                                    class="form-control"
                                    id="phoneInput"
                                    placeholder="+7 (___) ___-__-__"
                                    pattern="\+7\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}"
                                    required>
                                <div class="invalid-feedback">Введите корректный номер телефона (+7 (XXX) XXX-XX-XX)</div>
                            </div>

                            <script>
                                // Маска для телефона
                                document.getElementById('phoneInput').addEventListener('input', function(e) {
                                    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
                                    e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
                                });
                            </script>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Группа</label>
                                <select name="group_id" class="form-select" required>
                                    <?php foreach ($pdo->query("SELECT * FROM `groups`") as $group): ?>
                                        <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Курс -->
                            <div class="col-md-3">
                                <label class="form-label">Курс</label>
                                <select name="course" class="form-select" required>
                                    <option value="">Выберите курс</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <!-- Корпус -->
                            <div class="col-md-6">
                                <label class="form-label">Корпус</label>
                                <select name="corpus" class="form-select" required>
                                    <option value="">Выберите корпус</option>
                                    <option value="Центральный">Центральный</option>
                                    <option value="Угреша">Угреша</option>
                                    <option value="Красково">Красково</option>
                                    <option value="Гагаринский">Гагаринский</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Гражданство</label>
                                <input type="text" name="citizenship" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ИНН</label>
                                <input type="text" name="inn" class="form-control" placeholder="12 цифр или -">
                            </div>
<!-- Серия и номер паспорта -->
                            <div class="col-md-6">
                                <label class="form-label">Серия и номер паспорта</label>
                                <input type="text" name="passport_series_number" class="form-control" placeholder="ХХ ХХ ХХХХХХ" pattern="\d{2}\s\d{2}\s\d{6}" required>
                            </div>
                            <script>
                                document.querySelector('input[name="passport_series_number"]').addEventListener('input', function(e) {
                                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,2})(\d{0,6})/);
                                e.target.value = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '');
                                });
                            </script>
                            <div class="col-6">
                                <label class="form-label">Место рождения</label>
                                <input type="text" name="place_of_birth" class="form-control" rows="2" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Адрес регистрации</label>
                                <textarea name="registration_address" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Фактический адрес</label>
                                <textarea name="actual_address" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Школа</label>
                                <input type="text" name="school" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Тип школы</label>
                                <select name="school_type" id="schoolTypeSelect" class="form-select">
                                    <option value="общеобразовательная средняя">Общеобразовательная средняя</option>
                                    <option value="с техническим уклоном (лицей)">С техническим уклоном (лицей)</option>
                                    <option value="гимназия (гуманитарная/языковая)">Гимназия (гуманитарная/языковая)</option>
                                    <option value="другое">Другое</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="schoolTypeOtherField" style="display: none;">
                                <label class="form-label">Укажите тип школы</label>
                                <input type="text" name="school_type_other" class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Профиль класса</label>
                                <input type="text" name="school_profile" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Дата окончания школы</label>
                                <input type="date" name="school_end_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">СНИЛС</label>
                                <input type="text"
                                    name="snils"
                                    class="form-control"
                                    id="snilsInput"
                                    placeholder="XXX-XXX-XXX XX"
                                    pattern="\d{3}-\d{3}-\d{3}\s\d{2}"
                                    required>
                                <div class="invalid-feedback">Введите СНИЛС в формате: XXX-XXX-XXX XX</div>
                            </div>
                            <script>
                                // Маска для СНИЛС
                                document.getElementById('snilsInput').addEventListener('input', function(e) {
                                    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})/);
                                    e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? ' ' + x[4] : '');
                                });
                            </script>
                            <div class="col-md-12 form-check">
                                <input type="checkbox" name="has_material_support" id="materialCheck" class="form-check-input">
                                <label for="materialCheck" class="form-check-label">Имею основания для получения материальной помощи</label>
                            </div>
                            <!-- <div id="modalSection" class="col-12 mt-3" style="display:none;">
                    <h5>Основания для материальной помощи</h5>
                    <div class="form-check">
                        <input type="radio" name="reason" value="нет оснований" id="reason1" checked>
                        <label for="reason1" class="form-check-label">Нет оснований</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="reason" value="неполная семья" id="reason2">
                        <label for="reason2" class="form-check-label">Воспитываюсь в неполной семье</label>
                    </div>
                    другие варианты
                </div> -->

                            <!-- Модальное окно -->
                            <div class="modal fade" id="materialSupportModal" tabindex="-1" aria-labelledby="materialSupportModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="materialSupportModalLabel">Заявление на материальную помощь</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Перечень оснований для получения материальной поддержки:</strong></p>


                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="неполная семья" id="reason1" class="form-check-input">
                                                <label for="reason1" class="form-check-label">В связи с тяжелым материальным положением, так как воспитываюсь в неполной семье</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="многодетная семья" id="reason2" class="form-check-input">
                                                <label for="reason2" class="form-check-label">В связи с тяжелым материальным положением, так как воспитываюсь в многодетной семье</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="доход ниже прожиточного минимума" id="reason3" class="form-check-input">
                                                <label for="reason3" class="form-check-label">В связи с тяжелым материальным положением, так как доход семьи ниже прожиточного минимума</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="неработающий родитель" id="reason4" class="form-check-input">
                                                <label for="reason4" class="form-check-label">В связи с тяжелым материальным положением, так как один/оба родителя являются неработающими пенсионерами</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="инвалид 1 группы" id="reason5" class="form-check-input">
                                                <label for="reason5" class="form-check-label">В связи с тяжелым материальным положением, так как один из родителей — инвалид I группы</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="ребенок-инвалид" id="reason6" class="form-check-input">
                                                <label for="reason6" class="form-check-label">В связи с тяжелым материальным положением, так как являюсь ребенком-инвалидом</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="вступление в брак" id="reason7" class="form-check-input">
                                                <label for="reason7" class="form-check-label">В связи с вступлением в брак (если с момента регистрации брака прошло не более 3 месяцев)</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="рождение ребенка" id="reason8" class="form-check-input">
                                                <label for="reason8" class="form-check-label">В связи с рождением ребенка (если с момента рождения прошло не более 3 месяцев)</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="сирота" id="reason9" class="form-check-input">
                                                <label for="reason9" class="form-check-label">В связи с тяжелым материальным положением, так как являюсь студентом из числа детей-сирот и детей, оставшихся без попечения родителей</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="смерть родственника" id="reason10" class="form-check-input">
                                                <label for="reason10" class="form-check-label">В связи со смертью близкого родственника (если с момента смерти прошло не более 3 месяцев)</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="radio" name="reason" value="дорогостоящее лечение" id="reason11" class="form-check-input">
                                                <label for="reason11" class="form-check-label">В связи с дорогостоящим лечением</label>
                                            </div>

                                            <hr>

                                            <div class="form-check mb-3">
                                                <input type="checkbox" name="multiple_categories" id="multipleCategories" class="form-check-input">
                                                <label for="multipleCategories" class="form-check-label">Отношусь к нескольким категориям для выплаты</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="checkbox" name="social_scholarship" id="socialScholarship" class="form-check-input">
                                                <label for="socialScholarship" class="form-check-label">Получаю социальную стипендию (иные выплаты)</label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input type="checkbox" name="documents_provided" id="documentsProvided" class="form-check-input">
                                                <label for="documentsProvided" class="form-check-label">Предоставлю ксерокопии документов: паспорта студента, СНИЛС, ИНН, свидетельства о рождении</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-success w-50 mx-auto d-block">Зарегистрироваться</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- <script>
        document.getElementById('materialCheck').addEventListener('change', function() {
            document.getElementById('modalSection').style.display = this.checked ? 'block' : 'none';
        });
    </script> -->

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



    <script>
        document.getElementById('materialCheck').addEventListener('change', function() {
            if (this.checked) {
                var modal = new bootstrap.Modal(document.getElementById('materialSupportModal'));
                modal.show();
            }
        });
    </script>
</body>

</html>