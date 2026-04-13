<?php
session_start();
require_once 'config/config.php';

// Доступ только авторизованным
if (!isset($_SESSION['user_id'])) {
    header('Location: log.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Добавление товара в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;
    // Проверка существования товара
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if ($stmt->rowCount() == 0) {
        $message = 'Товар не найден';
    } else {
        // Добавляем или увеличиваем количество
        $stmt = $pdo->prepare("INSERT INTO user_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        if ($stmt->execute([$user_id, $product_id, $quantity, $quantity])) {
            $message = 'Товар добавлен в корзину';
        } else {
            $message = 'Ошибка добавления';
        }
    }
    // Редирект, чтобы избежать повторной отправки формы
    header('Location: shop.php');
    exit;
}

// Получаем список товаров из каталога
$stmt = $pdo->query("SELECT id, name, price FROM products ORDER BY name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем содержимое корзины пользователя
$stmt = $pdo->prepare("SELECT p.id, p.name, p.price, ui.quantity, (p.price * ui.quantity) as total FROM user_items ui JOIN products p ON ui.product_id = p.id WHERE ui.user_id = ? ORDER BY ui.id ");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Общая стоимость корзины
$grand_total = 0;
foreach ($cart_items as $item) {
    $grand_total += $item['total'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Магазин</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="container">
        <h2>Каталог товаров</h2>
        <?php if ($message): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <table border="1" cellpadding="8">
            <thead>
                <tr><th>Название</th><th>Цена</th><th>Количество</th><th>Действие</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= number_format($product['price'], 2) ?> руб.</td>
                        <td>
                            <input type="number" name="quantity" value="1" min="1" style="width:60px">
                        </td>
                        <td>
                            <button type="submit">Добавить</button>
                        </td>
                    </tr>
                </form>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2>Моя корзина</h2>
        <?php if (empty($cart_items)): ?>
            <p>Корзина пуста.</p>
        <?php else: ?>
            <table border="1" cellpadding="8">
                <thead>
                    <tr><th>Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th><th>Удалить</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2) ?> руб.</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['total'], 2) ?> руб.</td>
                        <td>
                            <a href="remove_from_cart.php?product_id=<?= $item['id'] ?>" onclick="return confirm('Удалить товар?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight: bold;">
                        <td colspan="3">Итого</td>
                        <td><?= number_format($grand_total, 2) ?> руб.</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <p><a href="clear_cart.php">Очистить корзину</a></p>
        <?php endif; ?>
        <p><a href="index.php">На главную</a> | <a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>