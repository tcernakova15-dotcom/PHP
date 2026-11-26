<?php
/**
 * Конфигурация подключения к базе данных и запуск защищённой сессии.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Возвращает активное подключение PDO к локальной базе данных SQLite
 * и автоматически инициализирует таблицы при первом запуске.
 *
 * @return PDO Экземпляр подключения к БД
 */
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbPath = __DIR__ . '/../sql/gamevault.sqlite';
    $isNew = !file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    if ($isNew) {
        $pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'user',
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                author_id INTEGER NULL REFERENCES users(id) ON DELETE SET NULL,
                title TEXT NOT NULL,
                genre TEXT NOT NULL,
                platform TEXT NOT NULL,
                edition TEXT NOT NULL DEFAULT 'Standard',
                price REAL NOT NULL DEFAULT 0.0,
                is_featured INTEGER NOT NULL DEFAULT 0,
                description TEXT NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                game_id INTEGER NOT NULL REFERENCES games(id) ON DELETE CASCADE,
                price_paid REAL NOT NULL,
                purchased_at TEXT DEFAULT CURRENT_TIMESTAMP
            );

            INSERT INTO games (title, genre, platform, edition, price, is_featured, description) VALUES
            ('Hollow Knight', 'Metroidvania', 'PC', 'Standard', 14.99, 1, 'Атмосферное приключение в подземном королевстве Халлоунест.'),
            ('Ori and the Blind Forest', 'Metroidvania', 'Cross-platform', 'Deluxe', 19.99, 1, 'Визуально выразительный платформер с глубоким сюжетом и музыкой.'),
            ('Cyber Quest 2077', 'RPG', 'PC', 'Key', 29.50, 0, 'Ролевой экшен в мире высоких технологий и корпораций.');
        ");
    }

    return $pdo;
}