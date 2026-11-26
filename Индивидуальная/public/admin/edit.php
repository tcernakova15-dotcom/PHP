<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';
requireAdmin();

$pdo = getDbConnection();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM games WHERE id = :id');
$stmt->execute(['id' => $id]);
$game = $stmt->fetch();

if (!$game) {
    redirect('/admin/index.php');
}

$errors = [];
$form = $game;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checked = validateGameInput($_POST);
    $form = array_merge($form, $checked['data']);
    $errors = $checked['errors'];

    if (empty($errors)) {
        $upd = $pdo->prepare(
            'UPDATE games
             SET title = :title, genre = :genre, platform = :platform, edition = :edition,
                 price = :price, is_featured = :is_featured, description = :description
             WHERE id = :id'
        );
        $upd->execute([
            'title' => $form['title'],
            'genre' => $form['genre'],
            'platform' => $form['platform'],
            'edition' => $form['edition'],
            'price' => $form['price'],
            'is_featured' => $form['is_featured'],
            'description' => $form['description'],
            'id' => $id,
        ]);
        redirect('/admin/index.php');
    }
}

renderHeader('Редактирование игры');
?>
<h1>Редактировать игру #<?= (int)$id ?></h1>
<?php if (!empty($errors)): ?>
    <div class="error"><ul><?php foreach ($errors as $eMsg): ?><li><?= e($eMsg) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="post" action="/admin/edit.php?id=<?= (int)$id ?>">
    <input type="hidden" name="id" value="<?= (int)$id ?>">
    <div class="field"><label>Название</label><input name="title" type="text" value="<?= e($form['title']) ?>"></div>
    <div class="field"><label>Жанр</label><input name="genre" type="text" value="<?= e($form['genre']) ?>"></div>
    <div class="field"><label>Платформа</label><input name="platform" type="text" value="<?= e($form['platform']) ?>"></div>
    <div class="field"><label>Издание</label><input name="edition" type="text" value="<?= e($form['edition']) ?>"></div>
    <div class="field"><label>Цена</label><input name="price" type="number" step="0.01" value="<?= e((string)$form['price']) ?>"></div>
    <div class="field"><label><input type="checkbox" name="is_featured" value="1" <?= (int)$form['is_featured'] === 1 ? 'checked' : '' ?>> Хит</label></div>
    <div class="field"><label>Описание</label><textarea name="description" rows="4"><?= e($form['description']) ?></textarea></div>
    <button type="submit">Сохранить изменения</button>
</form>
<?php renderFooter(); ?>