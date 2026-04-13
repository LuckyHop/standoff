$user_id = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];
$quantity = (int)$_POST['quantity'];

// В таблице cart должен быть уникальный ключ (user_id, product_id)
$stmt = $pdo->prepare("
    INSERT INTO cart (user_id, product_id, quantity)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE quantity = quantity + ?
");
$stmt->execute([$user_id, $product_id, $quantity, $quantity]);