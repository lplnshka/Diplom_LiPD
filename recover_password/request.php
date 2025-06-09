<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Восстановление пароля</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="text-center mb-4">Восстановление пароля</h3>
                <form action="send_link.php" method="post">
                    <div class="mb-3">
                        <label class="form-label">Email или Логин</label>
                        <input type="text" name="login_or_email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Продолжить</button>
                </form>
                <p class="mt-3 text-center"><a href="../../index.php">Назад</a></p>
            </div>
        </div>
    </div>
</body>

</html>