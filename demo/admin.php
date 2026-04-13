<?php
session_start();
require_once '../config/db.php';

// Проверка прав админа
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../index.php');
    exit;
}

// Получаем всех пользователей (или любые другие данные)
$stmt = $pdo->query("SELECT id, login, email, is_admin FROM users ORDER BY id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
</head>
<body>
    <h1>Админ-панель</h1>
    <p><a href="../index.php">На главную</a> | <a href="../auth/logout.php">Выйти</a></p>
    <h2>Пользователи</h2>
    <table border="1" cellpadding="5">
        <tr><th>ID</th><th>Логин</th><th>Email</th><th>Админ?</th></tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['login']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['is_admin'] ? 'Да' : 'Нет' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <!-- Здесь добавьте таблицу для управления воркшопами, заявками и т.д. -->
</body>
</html>