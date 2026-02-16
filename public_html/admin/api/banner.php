<?php
/**
 * 管理API: バナー設定
 * GET    → アクティブなバナー取得
 * POST   → バナー作成/更新
 * DELETE → バナー非アクティブ化
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
            $stmt = $db->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
            $banner = $stmt->fetch();

            if ($banner && $banner['mode'] === 'auto') {
                $stmt2 = $db->query("SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 1");
                $latestVehicle = $stmt2->fetch();

                if ($latestVehicle) {
                    $images = jsonDecode($latestVehicle['images']);
                    $banner['image_url'] = !empty($images) ? $images[0] : '';
                    $banner['link_url'] = '/stock.php?id=' . $latestVehicle['id'];
                    $banner['vehicle_title'] = $latestVehicle['title'];
                }
            }

            jsonResponse($banner ?: null);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'バナーの取得に失敗しました'], 500);
        }
        break;

    case 'POST':
        verifyCsrfApi();
        try {
            $body = getJsonBody();
            $mode = $body['mode'] ?? 'manual';
            $imageUrl = $body['imageUrl'] ?? '';
            $linkUrl = $body['linkUrl'] ?? '';

            if ($mode === 'manual' && empty($imageUrl)) {
                jsonResponse(['error' => '手動モードでは画像は必須です'], 400);
            }

            // 既存のアクティブバナーを非アクティブに
            $db->exec("UPDATE banners SET is_active = 0 WHERE is_active = 1");

            // 新しいバナーを作成
            $id = generateId();
            $stmt = $db->prepare("INSERT INTO banners (id, mode, image_url, link_url, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
            $stmt->execute([$id, $mode, $imageUrl, $linkUrl]);

            $stmt = $db->prepare("SELECT * FROM banners WHERE id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetch();

            jsonResponse($banner);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'バナーの保存に失敗しました', 'details' => $e->getMessage()], 500);
        }
        break;

    case 'DELETE':
        verifyCsrfApi();
        try {
            $db->exec("UPDATE banners SET is_active = 0 WHERE is_active = 1");
            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'バナーの削除に失敗しました'], 500);
        }
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
