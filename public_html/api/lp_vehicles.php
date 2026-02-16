<?php
/**
 * 公開API: LP用車両一覧
 * GET → display_on_lp = 1 の車両を返す
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM vehicles WHERE display_on_lp = 1 AND deleted_at IS NULL ORDER BY created_at DESC");
    $vehicles = $stmt->fetchAll();
    jsonResponse($vehicles);
} catch (PDOException $e) {
    jsonResponse(['error' => 'Failed to fetch vehicles'], 500);
}
