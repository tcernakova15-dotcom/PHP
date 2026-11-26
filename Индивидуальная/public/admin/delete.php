<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/layout.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = getDbConnection()->prepare('DELETE FROM games WHERE id = :id');
    $stmt->execute(['id' => $id]);
}
redirect('/admin/index.php');