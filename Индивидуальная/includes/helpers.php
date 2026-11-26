<?php
/**
 * Вспомогательные функции экранирования, валидации и навигации.
 */

declare(strict_types=1);

/**
 * Безопасно экранирует строку для вывода в HTML (защита от XSS).
 *
 * @param string|null $value Исходная строка
 * @return string Экранированная строка
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Выполняет HTTP-редирект и завершает выполнение скрипта.
 *
 * @param string $url Целевой URL
 * @return never
 */
function redirect(string $url): never
{
    header("Location: {$url}");
    exit;
}

/**
 * Проверяет и нормализует данные формы создания/редактирования игры.
 *
 * @param array<string, mixed> $input Входные данные (обычно $_POST)
 * @return array{data: array<string, mixed>, errors: list<string>} Нормализованные данные и список ошибок
 */
function validateGameInput(array $input): array
{
    $allowedGenres = ['RPG', 'Metroidvania', 'Action', 'Indie', 'Strategy'];
    $allowedPlatforms = ['PC', 'Console', 'Cross-platform'];
    $allowedEditions = ['Standard', 'Deluxe', 'Key'];

    $title = trim((string)($input['title'] ?? ''));
    $genre = trim((string)($input['genre'] ?? ''));
    $platform = trim((string)($input['platform'] ?? ''));
    $edition = trim((string)($input['edition'] ?? 'Standard'));
    $priceRaw = str_replace(',', '.', trim((string)($input['price'] ?? '')));
    $isFeatured = isset($input['is_featured']) ? 1 : 0;
    $description = trim((string)($input['description'] ?? ''));

    $errors = [];

    if ($title === '' || mb_strlen($title) < 2 || mb_strlen($title) > 150) {
        $errors[] = 'Название игры должно содержать от 2 до 150 символов.';
    }
    if (!in_array($genre, $allowedGenres, true)) {
        $errors[] = 'Выберите корректный жанр из списка.';
    }
    if (!in_array($platform, $allowedPlatforms, true)) {
        $errors[] = 'Выберите корректную платформу.';
    }
    if (!in_array($edition, $allowedEditions, true)) {
        $errors[] = 'Выберите корректный тип издания.';
    }
    if ($priceRaw === '' || !is_numeric($priceRaw) || (float)$priceRaw < 0 || (float)$priceRaw > 9999.99) {
        $errors[] = 'Цена должна быть числом в диапазоне от 0 до 9999.99.';
    }
    if ($description === '' || mb_strlen($description) < 10 || mb_strlen($description) > 2000) {
        $errors[] = 'Описание должно содержать от 10 до 2000 символов.';
    }

    return [
        'data' => [
            'title' => $title,
            'genre' => $genre,
            'platform' => $platform,
            'edition' => $edition,
            'price' => is_numeric($priceRaw) ? round((float)$priceRaw, 2) : 0.0,
            'is_featured' => $isFeatured,
            'description' => $description,
        ],
        'errors' => $errors,
    ];
}