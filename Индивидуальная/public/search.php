<?php
/**
 * Форма поиска и фильтрации игр по заданным критериям.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

$q = trim((string)($_GET['q'] ?? ''));
$genre = trim((string)($_GET['genre'] ?? ''));
$maxPriceRaw = trim((string)($_GET['max_price'] ?? ''));

$sql = 'SELECT id, title, genre, platform, edition, price, description FROM games WHERE 1=1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (title LIKE :q OR description LIKE :q)';
    $params['q'] = '%' . $q . '%';
}
if ($genre !== '') {
    $sql .= ' AND genre = :genre';
    $params['genre'] = $genre;
}
if ($maxPriceRaw !== '' && is_numeric($maxPriceRaw) && (float)$maxPriceRaw >= 0) {
    $sql .= ' AND price <= :max_price';
    $params['max_price'] = (float)$maxPriceRaw;
}
$sql .= ' ORDER BY title ASC';

$pdo = getDbConnection();
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

renderHeader('Поиск игр');
?>
<h1>Поиск игр</h1>

<form method="get" action="/search.php">
    <div class="field">
        <label for="q">Ключевое слово в названии или описании</label>
        <input id="q" name="q" type="text" value="<?= e($q) ?>" placeholder="Например: Hollow">
    </div>

    <div class="field">
        <label for="genre">Жанр</label>
        <select id="genre" name="genre">
            <option value="">Все жанры</option>
            <?php foreach (['RPG', 'Metroidvania', 'Action', 'Indie', 'Strategy'] as $g): ?>
                <option value="<?= e($g) ?>" <?= $genre === $g ? 'selected' : '' ?>><?= e($g) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field">
        <label for="max_price">Максимальная цена, €</label>
        <input id="max_price" name="max_price" type="number" step="0.01" min="0" value="<?= e($maxPriceRaw) ?>">
    </div>

    <button type="submit">Найти</button>
</form>

<h2>Результаты (<?= count($results) ?>)</h2>
<?php foreach ($results as $game): ?>
    <div class="card">
        <strong><?= e($game['title']) ?></strong> — <?= number_format((float)$game['price'], 2) ?> €
        <div>Жанр: <?= e($game['genre']) ?> | Платформа: <?= e($game['platform']) ?> | Издание: <?= e($game['edition']) ?></div>
        <p><?= e($game['description']) ?></p>
    </div>
<?php endforeach; ?>
<?php
renderFooter();