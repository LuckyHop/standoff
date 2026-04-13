<?php
session_start();
require_once 'config/config.php';

$errors = [];
$user_id = $_SESSION['user_id'];
$message = [];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $product_id = $_POST['product_id'];


    if(empty($errors)){
        $stmt = $pdo->prepare("INSERT INTO user_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        if($stmt->execute([$user_id, $product_id, $quantity])){

        }
    }
}


// Получаем список товаров из каталога
$stmt = $pdo->query("SELECT id, name, price FROM products ORDER BY name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем содержимое корзины пользователя
$stmt = $pdo->prepare("SELECT p.id, p.name, p.price, ui.quantity, (p.price * ui.quantity) as total FROM user_items ui JOIN products p ON ui.product_id = p.id WHERE ui.user_id = ? ORDER BY ui.id ");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
    <?php if(!empty($message)): ?>
        <?php foreach($message as $mes): ?>
            <p><?= htmlspecialchars($mes) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <table>
        <?php foreach($products as $pr_name): ?>
            <tr>
                <th>Название</th>
                <th>Цена</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($pr_name['name']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>