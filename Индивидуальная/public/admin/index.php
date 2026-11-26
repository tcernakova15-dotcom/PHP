<?php
/**
 * Панель администратора: управление каталогом игр, заказами и учётными записями.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';
requireAdmin();

$pdo = getDbConnection();
$games = $pdo->query('SELECT id, title, genre, platform, edition, price, is_featured FROM games ORDER BY id DESC')->fetchAll();
$orders = $pdo->query(
    'SELECT o.id, u.username, g.title, o.price_paid, o.purchased_at
     FROM orders o
     JOIN users u ON u.id = o.user_id
     JOIN games g ON g.id = o.game_id
     ORDER BY o.id DESC LIMIT 20'
)->fetchAll();

renderHeader('Панель администратора');
?>
<h1>Панель администратора</h1>
<p>
    <a class="btn" href="/create.php">+ Добавить игру</a>
    <a class="btn" href="/admin/create_admin.php">+ Создать администратора</a>
</p>

<h2>Записи в каталоге</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Жанр</th>
            <th>Платформа</th>
            <th>Издание</th>
            <th>Цена</th>
            <th>Хит</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($games as $g): ?>
        <tr>
            <td><?= (int)$g['id'] ?></td>
            <td><?= e($g['title']) ?></td>
            <td><?= e($g['genre']) ?></td>
            <td><?= e($g['platform']) ?></td>
            <td><?= e($g['edition']) ?></td>
            <td><?= number_format((float)$g['price'], 2) ?> €</td>
            <td><?= (int)$g['is_featured'] === 1 ? 'Да' : 'Нет' ?></td>
            <td>
                <a href="/admin/edit.php?id=<?= (int)$g['id'] ?>">Редактировать</a> |
                <a href="/admin/delete.php?id=<?= (int)$g['id'] ?>" onclick="return confirm('Удалить запись?');">Удалить</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Последние покупки</h2>
<table>
    <thead>
        <tr><th>ID</th><th>Пользователь</th><th>Игра</th><th>Сумма</th><th>Дата</th></tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= (int)$o['id'] ?></td>
            <td><?= e($o['username']) ?></td>
            <td><?= e($o['title']) ?></td>
            <td><?= number_format((float)$o['price_paid'], 2) ?> €</td>
            <td><?= e($o['purchased_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php
renderFooter();