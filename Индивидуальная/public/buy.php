<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
requireLogin('/login.php');

$gameId = (int)($_POST['game_id'] ?? 0);
$pdo = getDbConnection();
$user = currentUser();

$stmt = $pdo->prepare('SELECT id, title, price FROM games WHERE id = :id');
$stmt->execute(['id' => $gameId]);
$game = $stmt->fetch();

renderHeader('Оформление покупки');
if (!$game) {
    echo '<div class="error">Игра не найдена.</div>';
} else {
    $ins = $pdo->prepare('INSERT INTO orders (user_id, game_id, price_paid) VALUES (:uid, :gid, :price)');
    $ins->execute([
        'uid' => $user['id'],
        'gid' => $game['id'],
        'price' => $game['price'],
    ]);
    echo '<div class="success">Покупка оформлена: <strong>' . e($game['title']) . '</strong> (' . number_format((float)$game['price'], 2) . ' €).</div>';
}
echo '<p><a class="btn" href="/index.php">Вернуться в каталог</a></p>';
renderFooter();