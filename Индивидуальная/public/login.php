<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string)($_POST['login'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($login === '' || $password === '') {
        $error = 'Введите логин/email и пароль.';
    } elseif (attemptLogin(getDbConnection(), $login, $password)) {
        redirect('/index.php');
    } else {
        $error = 'Неверный логин или пароль.';
    }
}

renderHeader('Вход');
?>
<h1>Вход в систему</h1>
<?php if ($error !== ''): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="/login.php">
    <div class="field">
        <label for="login">Логин или Email</label>
        <input id="login" name="login" type="text" required>
    </div>
    <div class="field">
        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" required>
    </div>
    <button type="submit">Войти</button>
</form>
<?php renderFooter(); ?>