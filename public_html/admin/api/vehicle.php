<?php
declare(strict_types=1);
/**
 * 管理API: 車両個別操作
 * GET    ?id=xxx → 車両詳細取得
 * PUT    ?id=xxx → 車両更新
 * DELETE ?id=xxx → 車両削除
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
$id = $_GET['id'] ?? '';

if (empty($id)) {
    jsonResponse(['error' => 'IDが指定されていません'], 400);
}

switch ($method) {
    case 'GET':
        try {
            $stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ?');
            $stmt->execute([$id]);
            $vehicle = $stmt->fetch();
            if (!$vehicle) {
                jsonResponse(['error' => 'Not found'], 404);
            }
            jsonResponse($vehicle);
        } catch (PDOException $e) {
            jsonResponse(['error' => '車両の取得に失敗しました'], 500);
        }
        break;

    case 'PUT':
        verifyCsrfApi();
        try {
            $body = getJsonBody();
            $now = date('Y-m-d H:i:s');

            // 復元処理
            if (!empty($body['restore'])) {
                $stmt = $db->prepare("UPDATE vehicles SET deleted_at = NULL, updated_at = ? WHERE id = ?");
                $stmt->execute([$now, $id]);
                $stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ?');
                $stmt->execute([$id]);
                jsonResponse($stmt->fetch());
            }

            // 送信されたフィールドのみ更新（LP表示トグル等の部分更新に対応）
            $sets = [];
            $params = [];

            if (array_key_exists('title', $body)) {
                $sets[] = 'title = ?';
                $params[] = $body['title'];
            }
            if (array_key_exists('price', $body)) {
                $sets[] = 'price = ?';
                $params[] = (int) $body['price'];
            }
            if (array_key_exists('images', $body)) {
                $sets[] = 'images = ?';
                $params[] = ensureJsonString($body['images']);
            }
            if (array_key_exists('basicInfo', $body)) {
                $sets[] = 'basic_info = ?';
                $params[] = ensureJsonString($body['basicInfo']);
            }
            if (array_key_exists('detailedInfo', $body)) {
                $sets[] = 'detailed_info = ?';
                $params[] = ensureJsonString($body['detailedInfo']);
            }
            if (array_key_exists('equipment', $body)) {
                $sets[] = 'equipment = ?';
                $params[] = ensureJsonString($body['equipment']);
            }
            if (array_key_exists('description', $body)) {
                $sets[] = 'description = ?';
                $params[] = $body['description'];
            }
            if (array_key_exists('displayOnLP', $body)) {
                $sets[] = 'display_on_lp = ?';
                $params[] = $body['displayOnLP'] ? 1 : 0;
            }

            if (empty($sets)) {
                jsonResponse(['error' => '更新するフィールドがありません'], 400);
            }

            $sets[] = "updated_at = ?";
            $params[] = $now;
            $params[] = $id;

            $sql = 'UPDATE vehicles SET ' . implode(', ', $sets) . ' WHERE id = ?';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ?');
            $stmt->execute([$id]);
            $vehicle = $stmt->fetch();
            jsonResponse($vehicle);
        } catch (PDOException $e) {
            error_log('vehicle.php PUT: ' . $e->getMessage());
            jsonResponse(['error' => 'データの更新に失敗しました'], 500);
        }
        break;

    case 'DELETE':
        verifyCsrfApi();
        try {
            $now = date('Y-m-d H:i:s');
            $stmt = $db->prepare("UPDATE vehicles SET deleted_at = ? WHERE id = ?");
            $stmt->execute([$now, $id]);
            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            error_log('vehicle.php DELETE: ' . $e->getMessage());
            jsonResponse(['error' => '車両の削除に失敗しました'], 500);
        }
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
