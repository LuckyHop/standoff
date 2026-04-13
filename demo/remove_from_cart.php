<?php
session_start();
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: log.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$_GET['product_id'] ?? 0;

if ($product_id) {
    $stmt = $pdo->prepare("DELETE FROM user_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

header('Location: shop.php');
exit;