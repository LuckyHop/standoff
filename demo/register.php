<?php
session_start();
require_once 'config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    // Валидация (простейшая)
    if (empty($login)) {
        $errors[] = 'Логин обязателен';
    } elseif (!preg_match('/^(a-zA-Z0-9_)+$/', $login)) {
        $errors[] = 'Логин может содержать только латинские буквы, цифры и _';
    }
    if(empty($phone)){
        $errors[] = 'Введите номер телефона';
    } elseif(!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)){
        $errors[] = 'Введите корректный номер телефона (например: +7(xxx)-xxx-xx-xx )';
    }
    if(empty($full_name)){
        $errors[]='Заполните поле ФИО';
    } elseif(!preg_match('/^[а-яА-ЯёЁ\s\-]+$/u' , $full_name)){
        $errors[] = 'Только кирилица в ФИО';
    }
    if (empty($password) || strlen($password) < 4) {
        $errors[] = 'Пароль должен быть не менее 4 символов';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Укажите корректный email';
    }

    if (empty($full_name)) {
        $errors[] = 'Введите ФИО';
    }

    // Проверка на уникальность логина и email
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
        $stmt->execute([$login, $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Пользователь с таким логином или email уже существует';
        }
    }

    // Если ошибок нет – сохраняем
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (login, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$login, $hashedPassword, $email, $full_name, $phone])) {
            $_SESSION['success'] = 'Регистрация успешна! Теперь войдите.';
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Ошибка при сохранении пользователя';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="container">
        <h2>Регистрация</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Пароль (мин. 4 символа):</label>
                <input type="password" name="password" required minlength="4">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>ФИО:</label>
                <input type="text" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Телефон:</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>