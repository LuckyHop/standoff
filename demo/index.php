<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
        <div class="nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Привет, <?= htmlspecialchars($_SESSION['user_fullname']) ?>!</span>
                <a href="logout.php">Выйти</a>
                <a href="shop.php">Магазин</a>
                <a href="market.php">Маркет</a>
            <?php else: ?>
                <a href="log.php">Войти</a>
                <a href="reg.php">Регистрация</a>
            <?php endif; ?>
        </div>
        <h1>Добро пожаловать!</h1>
        <?php if($_SESSION['is_admin']== 0): ?>
            <p>Вы обычный пользователь</p>
        <?php endif; ?>
        <?php if($_SESSION['is_admin']== 1): ?>
            <p>Вы администратор</p>
        <?php endif; ?>
        <p>Это главная страница нашего сайта.</p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['is_admin'] == 1): ?>
            <div class="admin-panel">
                <strong>Вы вошли как администратор!</strong>
                <p>Здесь можно разместить ссылки на управление сайтом, статистику и т.д.</p>
                <!-- Пример ссылки: <a href="admin.php">Панель управления</a> -->
            </div>
        <?php endif; ?>
    </div>
</body>
</html>