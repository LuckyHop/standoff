<?php
session_start();
require_once 'config.php';

// Проверка админа (упрощённо)
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

// ========== 1. Получаем параметры из GET ==========
$statusFilter = $_GET['status'] ?? '';          // фильтр по статусу (id статуса)
$sortField   = $_GET['sort'] ?? 'created_at';   // поле для сортировки
$sortOrder   = $_GET['order'] ?? 'DESC';        // порядок (ASC / DESC)
$page        = (int)($_GET['page'] ?? 1);       // текущая страница
$limit       = 5;                               // записей на страницу
$offset      = ($page - 1) * $limit;

// ========== 2. Белый список для сортировки (безопасность) ==========
$allowedSort = ['id', 'created_at', 'desired_date', 'status_id'];
if(!in_array($sortField, $allowedSort)) $sortField = 'created_at';
$sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

// ========== 3. Формируем WHERE для фильтра ==========
$where = '';
$params = [];
if(!empty($statusFilter)) {
    $where = " WHERE a.status_id = ? ";
    $params[] = $statusFilter;
}

// ========== 4. Считаем общее количество записей (для пагинации) ==========
$countSql = "SELECT COUNT(*) as total FROM applications a $where";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRows = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $limit);

// ========== 5. Основной запрос с JOIN, фильтром, сортировкой и пагинацией ==========
$sql = "
    SELECT a.*, c.name AS course_name, s.name AS status_name
    FROM applications a
    JOIN courses c ON a.course_id = c.id
    JOIN statuses s ON a.status_id = s.id
    $where
    ORDER BY $sortField $sortOrder
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Админка</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .active-sort { background-color: #ff0; }
        .pagination a { margin: 0 5px; }
    </style>
</head>
<body>
    <h1>Управление заявками</h1>

    <!-- Форма фильтрации -->
    <form method="get">
        <label>Статус:</label>
        <select name="status">
            <option value="">Все</option>
            <option value="1" <?= $statusFilter == '1' ? 'selected' : '' ?>>Новая</option>
            <option value="2" <?= $statusFilter == '2' ? 'selected' : '' ?>>Подтверждена</option>
            <option value="3" <?= $statusFilter == '3' ? 'selected' : '' ?>>Завершена</option>
        </select>
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sortField) ?>">
        <input type="hidden" name="order" value="<?= htmlspecialchars($sortOrder) ?>">
        <button type="submit">Применить фильтр</button>
    </form>

    <!-- Таблица с сортировкой -->
    <table>
        <thead>
            <tr>
                <th><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'id', 'order'=>($sortField=='id' && $sortOrder=='ASC' ? 'DESC' : 'ASC')])) ?>">ID</a></th>
                <th><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'created_at', 'order'=>($sortField=='created_at' && $sortOrder=='ASC' ? 'DESC' : 'ASC')])) ?>">Дата создания</a></th>
                <th>Курс</th>
                <th><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'desired_date', 'order'=>($sortField=='desired_date' && $sortOrder=='ASC' ? 'DESC' : 'ASC')])) ?>">Желаемая дата</a></th>
                <th><a href="?<?= http_build_query(array_merge($_GET, ['sort'=>'status_id', 'order'=>($sortField=='status_id' && $sortOrder=='ASC' ? 'DESC' : 'ASC')])) ?>">Статус</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($applications as $app): ?>
            <tr>
                <td><?= $app['id'] ?></td>
                <td><?= $app['created_at'] ?></td>
                <td><?= htmlspecialchars($app['course_name']) ?></td>
                <td><?= $app['desired_date'] ?></td>
                <td><?= htmlspecialchars($app['status_name']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Пагинация -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page'=>1])) ?>">Первая</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['page'=>($page-1)])) ?>">« Назад</a>
        <?php endif; ?>

        Страница <?= $page ?> из <?= $totalPages ?>

        <?php if($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page'=>($page+1)])) ?>">Вперед »</a>
            <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$totalPages])) ?>">Последняя</a>
        <?php endif; ?>
    </div>
</body>
</html>
