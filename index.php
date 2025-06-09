<?php session_start();
if (!empty($_SESSION['success_message'])) {
    echo '<div class="alert alert-success text-center">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>УИС Техникума</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" href="images/icon.png" type="image/x-icon">
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
            <br>лётчика-космонавта Ю. А. Гагарина</div>
        </div>
    </div>
    <div class="container">
    <div class="card shadow-sm p-3 p-md-4 mx-auto w-100 w-md-auto" style="max-width: 400px;">
            <h3 class="text-center mb-4">Вход</h3>
            <form action="login.php" method="post">
                <div class="mb-3">
                    <label class="form-label">Логин</label>
                    <input type="text" name="login" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="background-color:#26aadb; border:none;">Войти</button>
            </form>
            <p class="mt-3 text-center">
                <a href="recover_password/request.php" class="btn btn-outline-secondary">Забыли пароль?</a>
                <a href="register.php" class="btn btn-outline-secondary">Зарегистрироваться</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>