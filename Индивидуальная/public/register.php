<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $error = 'Логин: 3–30 символов (латиница, цифры, подчёркивание).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Укажите корректный email.';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не короче 6 символов.';
    } else {
        $res = registerUser(getDbConnection(), $username, $email, $password, 'user');
        if ($res['ok']) {
            attemptLogin(getDbConnection(), $username, $password);
            redirect('/index.php');
        }
        $error = $res['error'] ?? 'Ошибка регистрации.';
    }
}

renderHeader('Регистрация');
?>
<h1>Регистрация</h1>
<?php if ($error !== ''): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="/register.php">
    <div class="field">
        <label for="username">Логин</label>
        <input id="username" name="username" type="text" required>
    </div>
    <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required>
    </div>
    <div class="field">
        <label for="password">Пароль (от 6 символов)</label>
        <input id="password" name="password" type="password" required>
    </div>
    <button type="submit">Зарегистрироваться</button>
</form>
<?php renderFooter(); ?>