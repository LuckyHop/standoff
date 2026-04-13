<?php
session_start();
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: log.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("DELETE FROM user_items WHERE user_id = ?");
$stmt->execute([$user_id]);

header('Location: shop.php');
exit;