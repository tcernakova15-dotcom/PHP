<?php
/**
 * Общие элементы верстки (шапка и подвал страниц).
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

/**
 * Выводит верхнюю часть HTML-страницы и навигационное меню.
 *
 * @param string $title Заголовок страницы
 * @return void
 */
function renderHeader(string $title): void
{
    $user = currentUser();
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> — GameVault</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 960px; margin: 24px auto; padding: 0 16px; line-height: 1.5; }
            nav { display: flex; flex-wrap: wrap; gap: 12px; padding: 12px 0; border-bottom: 1px solid #ccc; margin-bottom: 20px; align-items: center; }
            nav a { text-decoration: none; color: #1a4b8c; font-weight: 600; }
            .card { border: 1px solid #ddd; border-radius: 6px; padding: 14px; margin-bottom: 12px; }
            .badge { display: inline-block; background: #eef3fa; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
            .error { background: #fdecec; border: 1px solid #e08e8e; color: #8a1c1c; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
            .success { background: #ebf8ee; border: 1px solid #85c796; color: #1b5e20; padding: 10px; border-radius: 4px; margin-bottom: 12px; }
            .field { margin-bottom: 12px; }
            label { display: block; font-weight: 600; margin-bottom: 4px; }
            input[type="text"], input[type="email"], input[type="password"], input[type="number"], select, textarea {
                width: 100%; max-width: 480px; padding: 8px; box-sizing: border-box;
            }
            button, .btn { padding: 8px 14px; cursor: pointer; background: #1a4b8c; color: #fff; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        </style>
    </head>
    <body>
    <nav>
        <a href="/index.php">Каталог</a>
        <a href="/search.php">Поиск</a>
        <a href="/create.php">Добавить игру</a>
        <?php if ($user !== null): ?>
            <?php if ($user['role'] === 'admin'): ?>
                <a href="/admin/index.php">Админ-панель</a>
            <?php endif; ?>
            <span>Пользователь: <strong><?= e($user['username']) ?></strong> (<?= e($user['role']) ?>)</span>
            <a href="/logout.php">Выйти</a>
        <?php else: ?>
            <a href="/login.php">Войти</a>
            <a href="/register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
    <?php
}

/**
 * Выводит закрывающие теги HTML-страницы.
 *
 * @return void
 */
function renderFooter(): void
{
    echo '</body></html>';
}