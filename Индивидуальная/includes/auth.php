<?php
/**
 * Функции регистрации, входа, выхода и проверки прав доступа.
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

/**
 * Возвращает данные текущего авторизованного пользователя или null.
 *
 * @return array{id: int, username: string, email: string, role: string}|null
 */
function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => (int)$_SESSION['user_id'],
        'username' => (string)($_SESSION['username'] ?? ''),
        'email' => (string)($_SESSION['email'] ?? ''),
        'role' => (string)($_SESSION['role'] ?? 'user'),
    ];
}

/**
 * Проверяет, авторизован ли пользователь; при необходимости перенаправляет на вход.
 *
 * @param string $loginUrl Путь к странице входа
 * @return void
 */
function requireLogin(string $loginUrl = '/login.php'): void
{
    if (currentUser() === null) {
        redirect($loginUrl);
    }
}

/**
 * Проверяет наличие роли администратора; при отсутствии возвращает 403.
 *
 * @return void
 */
function requireAdmin(): void
{
    $user = currentUser();
    if ($user === null) {
        redirect('/login.php');
    }
    if ($user['role'] !== 'admin') {
        http_response_code(403);
        echo '<h1>403 — Доступ запрещён</h1><p>Требуются права администратора.</p>';
        exit;
    }
}

/**
 * Регистрирует нового пользователя с безопасным хешированием пароля.
 *
 * @param PDO $pdo Подключение к БД
 * @param string $username Логин
 * @param string $email Электронная почта
 * @param string $password Пароль в открытом виде
 * @param string $role Роль ('user' или 'admin')
 * @return array{ok: bool, error: ?string, user_id: ?int}
 */
function registerUser(PDO $pdo, string $username, string $email, string $password, string $role = 'user'): array
{
    $role = in_array($role, ['user', 'admin'], true) ? $role : 'user';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :hash, :role)'
        );
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'hash' => $hash,
            'role' => $role,
        ]);

        return ['ok' => true, 'error' => null, 'user_id' => (int)$pdo->lastInsertId()];
    } catch (PDOException $e) {
        if ((int)$e->getCode() === 23000) {
            return ['ok' => false, 'error' => 'Пользователь с таким логином или email уже существует.', 'user_id' => null];
        }
        return ['ok' => false, 'error' => 'Ошибка при сохранении пользователя в базе данных.', 'user_id' => null];
    }
}

/**
 * Выполняет вход пользователя по логину или email и паролю.
 *
 * @param PDO $pdo Подключение к БД
 * @param string $login Логин или email
 * @param string $password Пароль
 * @return bool True при успешной аутентификации
 */
function attemptLogin(PDO $pdo, string $login, string $password): bool
{
    $stmt = $pdo->prepare(
        'SELECT id, username, email, password_hash, role FROM users WHERE username = :u OR email = :e LIMIT 1'
    );
    $stmt->execute(['u' => $login, 'e' => $login]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, (string)$user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = (string)$user['username'];
    $_SESSION['email'] = (string)$user['email'];
    $_SESSION['role'] = (string)$user['role'];

    return true;
}