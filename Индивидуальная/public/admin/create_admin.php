<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';
requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $error = 'Некорректный логин (3–30 символов: латиница, цифры, _).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email.';
    } elseif (strlen($password) < 8) {
        $error = 'Пароль администратора должен быть не короче 8 символов.';
    } else {
        $res = registerUser(getDbConnection(), $username, $email, $password, 'admin');
        if ($res['ok']) {
            $success = "Учётная запись администратора «{$username}» создана.";
        } else {
            $error = $res['error'] ?? 'Ошибка создания администратора.';
        }
    }
}

renderHeader('Новый администратор');
?>
<h1>Создание учётной записи администратора</h1>
<?php if ($error !== ''): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success !== ''): ?><div class="success"><?= e($success) ?></div><?php endif; ?>
<form method="post" action="/admin/create_admin.php">
    <div class="field"><label for="username">Логин</label><input id="username" name="username" type="text" required></div>
    <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
    <div class="field"><label for="password">Пароль (от 8 символов)</label><input id="password" name="password" type="password" required></div>
    <button type="submit">Создать администратора</button>
</form>
<?php renderFooter(); ?>