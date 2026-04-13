<?php
session_start();
require_once 'config/config.php';

if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}

$errors = [];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $login
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <?php if(!empty($errors)): ?>
        <?php foreach($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <form action="" method="post">
        <p>Логин</p>
        <input type="text" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" name="login" id=""><br>
        <p>Пароль</p>
        <input type="password" name="password" id=""><br>
        <p>Почта</p>
        <input type="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" name="email" id=""><br>
        <p>ФИО</p>
        <input type="text" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" name="full_name" id=""><br>
        <p>Телефон</p>
        <input type="tel" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" name="phone" id=""><br><br>
        <button type="submit">Зарегистрироваться</button><br>
        <a href="log.php">Есть аккаунт? Войти</a>
    </form>
</body>
</html>