<?php
/**
 * Страница добавления новой игры в каталог с клиентской и серверной валидацией.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

$errors = [];
$successMessage = '';
$form = [
    'title' => '',
    'genre' => 'RPG',
    'platform' => 'PC',
    'edition' => 'Standard',
    'price' => '19.99',
    'is_featured' => 0,
    'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checked = validateGameInput($_POST);
    $form = $checked['data'];
    $errors = $checked['errors'];

    if (empty($errors)) {
        $pdo = getDbConnection();
        $user = currentUser();
        $stmt = $pdo->prepare(
            'INSERT INTO games (author_id, title, genre, platform, edition, price, is_featured, description)
             VALUES (:author_id, :title, :genre, :platform, :edition, :price, :is_featured, :description)'
        );
        $stmt->execute([
            'author_id' => $user['id'] ?? null,
            'title' => $form['title'],
            'genre' => $form['genre'],
            'platform' => $form['platform'],
            'edition' => $form['edition'],
            'price' => $form['price'],
            'is_featured' => $form['is_featured'],
            'description' => $form['description'],
        ]);
        $successMessage = 'Игра успешно добавлена в каталог.';
    }
}

renderHeader('Добавить игру');
?>
<h1>Добавить игру</h1>

<?php if ($successMessage !== ''): ?>
    <div class="success"><?= e($successMessage) ?></div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form id="gameForm" method="post" action="/create.php" novalidate>
    <div class="field">
        <label for="title">Название игры (текст)</label>
        <input id="title" name="title" type="text" required minlength="2" maxlength="150" value="<?= e($form['title']) ?>">
    </div>

    <div class="field">
        <label for="genre">Жанр (выпадающий список)</label>
        <select id="genre" name="genre">
            <?php foreach (['RPG', 'Metroidvania', 'Action', 'Indie', 'Strategy'] as $g): ?>
                <option value="<?= e($g) ?>" <?= $form['genre'] === $g ? 'selected' : '' ?>><?= e($g) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field">
        <label for="platform">Платформа (выпадающий список)</label>
        <select id="platform" name="platform">
            <?php foreach (['PC', 'Console', 'Cross-platform'] as $p): ?>
                <option value="<?= e($p) ?>" <?= $form['platform'] === $p ? 'selected' : '' ?>><?= e($p) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field">
        <label>Тип издания (переключатели radio)</label>
        <?php foreach (['Standard', 'Deluxe', 'Key'] as $ed): ?>
            <label style="display:inline-block; margin-right:14px; font-weight:normal;">
                <input type="radio" name="edition" value="<?= e($ed) ?>" <?= $form['edition'] === $ed ? 'checked' : '' ?>>
                <?= e($ed) ?>
            </label>
        <?php endforeach; ?>
    </div>

    <div class="field">
        <label for="price">Цена, € (число)</label>
        <input id="price" name="price" type="number" step="0.01" min="0" max="9999.99" required value="<?= e((string)$form['price']) ?>">
    </div>

    <div class="field">
        <label style="display:inline-block; font-weight:normal;">
            <input type="checkbox" name="is_featured" value="1" <?= (int)$form['is_featured'] === 1 ? 'checked' : '' ?>>
            Отметить как рекомендуемую (флажок)
        </label>
    </div>

    <div class="field">
        <label for="description">Описание (многострочный текст)</label>
        <textarea id="description" name="description" rows="4" required minlength="10" maxlength="2000"><?= e($form['description']) ?></textarea>
    </div>

    <button type="submit">Сохранить игру</button>
</form>

<script>
document.getElementById('gameForm').addEventListener('submit', function (event) {
    const title = document.getElementById('title').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const desc = document.getElementById('description').value.trim();
    const msgs = [];

    if (title.length < 2) msgs.push('Введите название игры (минимум 2 символа).');
    if (Number.isNaN(price) || price < 0) msgs.push('Укажите корректную цену (>= 0).');
    if (desc.length < 10) msgs.push('Добавьте описание (минимум 10 символов).');

    if (msgs.length > 0) {
        event.preventDefault();
        alert(msgs.join('\n'));
    }
});
</script>
<?php
renderFooter();