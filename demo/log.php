<?php
session_start();
require_once 'config/config.php';

if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit;
}


$error = [];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($login) || empty($password)){
        $error[] = 'Заполните логин и пароль';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_fullname'] = $user['full_name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header('Location: index.php');
            exit;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
        <h2>Вход</h2>
        <?php if(!empty($error)): ?>
            <div class="alert-danger">
                <?php foreach($error as $errs): ?>
                    <p><?= htmlspecialchars($errs) ?></p>
                <?php endforeach;?>
            </div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Логин:</label>
                <input type="text" name="login" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Войти</button>
        </form>
        <p>Нет аккаунта? <a href="reg.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>