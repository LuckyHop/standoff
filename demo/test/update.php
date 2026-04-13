$id = (int)($_POST['id'] ?? 0);
$new_price = (float)($_POST['price'] ?? 0);

if ($id > 0 && $new_price > 0) {
    $stmt = $pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
    $stmt->execute([$new_price, $id]);
    header('Location: products_list.php');
    exit;
}