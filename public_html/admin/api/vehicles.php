<?php
/**
 * 管理API: 車両一覧 / 新規作成
 * GET  → 車両一覧取得
 * POST → 新規車両作成
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

initSession();
requireApiLogin();

header('Content-Type: application/json; charset=utf-8');

$method = getRequestMethod();
$db = getDB();

switch ($method) {
    case 'GET':
        try {
            $stmt = $db->query('SELECT * FROM vehicles ORDER BY created_at DESC');
            $vehicles = $stmt->fetchAll();
            jsonResponse($vehicles);
        } catch (PDOException $e) {
            jsonResponse(['error' => '車両一覧の取得に失敗しました'], 500);
        }
        break;

    case 'POST':
        verifyCsrfApi();
        try {
            $body = getJsonBody();
            $id = generateId();
            $manageNumber = generateManageNumber($db);
            $now = date('Y-m-d H:i:s');

            $stmt = $db->prepare("INSERT INTO vehicles (id, manage_number, title, price, images, basic_info, detailed_info, equipment, description, display_on_lp, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)");
            $stmt->execute([
                $id,
                $manageNumber,
                $body['title'] ?? 'No Title',
                (int) ($body['price'] ?? 0),
                ensureJsonString($body['images'] ?? []),
                ensureJsonString($body['basicInfo'] ?? []),
                ensureJsonString($body['detailedInfo'] ?? []),
                ensureJsonString($body['equipment'] ?? []),
                $body['description'] ?? '',
                $now,
                $now
            ]);

            $stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ?');
            $stmt->execute([$id]);
            $vehicle = $stmt->fetch();

            jsonResponse($vehicle);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'データの保存に失敗しました', 'details' => $e->getMessage()], 500);
        }
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
