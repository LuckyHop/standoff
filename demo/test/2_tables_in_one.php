$stmt = $pdo->prepare("
    UPDATE orders o
    JOIN products p ON o.product_id = p.id
    SET o.price = p.price
    WHERE o.price IS NULL
");
$stmt->execute();