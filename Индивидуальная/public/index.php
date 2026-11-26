<?php
/**
 * Публичная главная страница с динамическим каталогом игр из базы данных.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

$pdo = getDbConnection();
$featuredGames = $pdo->query(
    'SELECT id, title, genre, platform, edition, price, description FROM games WHERE is_featured = 1 ORDER BY id DESC'
)->fetchAll();
$allGames = $pdo->query(
    'SELECT id, title, genre, platform, edition, price, is_featured, description FROM games ORDER BY created_at DESC, id DESC'
)->fetchAll();

renderHeader('Каталог игр');
?>
<h1>Каталог игр</h1>

<h2>Рекомендуемые</h2>
<?php if (empty($featuredGames)): ?>
    <p>Рекомендуемых игр пока нет.</p>
<?php else: ?>
    <?php foreach ($featuredGames as $game): ?>
        <div class="card">
            <span class="badge">Хит</span>
            <strong><?= e($game['title']) ?></strong> — <?= number_format((float)$game['price'], 2) ?> €
            <div>Жанр: <?= e($game['genre']) ?> | Платформа: <?= e($game['platform']) ?> | Издание: <?= e($game['edition']) ?></div>
            <p><?= e($game['description']) ?></p>
            <form method="post" action="/buy.php" style="display:inline;">
                <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
                <button type="submit">Купить</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<h2>Все игры в каталоге</h2>
<?php foreach ($allGames as $game): ?>
    <div class="card">
        <strong><?= e($game['title']) ?></strong> — <?= number_format((float)$game['price'], 2) ?> €
        <div>Жанр: <?= e($game['genre']) ?> | Платформа: <?= e($game['platform']) ?> | Издание: <?= e($game['edition']) ?></div>
        <p><?= e($game['description']) ?></p>
        <form method="post" action="/buy.php" style="display:inline;">
            <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
            <button type="submit">Купить</button>
        </form>
    </div>
<?php endforeach; ?>
<?php
renderFooter();