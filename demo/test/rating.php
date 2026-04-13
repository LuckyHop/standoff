$user_id = 1;
$product_id = 10;
$rating = 5;

$stmt = $pdo->prepare("
    INSERT INTO ratings (user_id, product_id, rating, voted_at)
    VALUES (?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE rating = ?, voted_at = NOW()
");
$stmt->execute([$user_id, $product_id, $rating, $rating]);