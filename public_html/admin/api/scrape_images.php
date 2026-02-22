<?php
declare(strict_types=1);
/**
 * 管理API: 公開停止車両の画像をスクレイピングしてローカル保存
 * POST { vehicle_id: string }
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

initSession();
requireApiLogin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$body = getJsonBody();
$vehicleId = trim($body['vehicle_id'] ?? '');

if (empty($vehicleId)) {
    jsonResponse(['error' => 'vehicle_id が必要です'], 400);
}

$db = getDB();
$stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ? AND deleted_at IS NOT NULL');
$stmt->execute([$vehicleId]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    jsonResponse(['error' => '公開停止車両が見つかりません'], 404);
}

$images = jsonDecode($vehicle['images'], []);

if (empty($images)) {
    jsonResponse(['error' => '画像データがありません'], 400);
}

// すでにすべてローカル保存済みかチェック（/uploads/ で始まる）
$alreadySaved = array_reduce($images, function (bool $carry, string $url): bool {
    return $carry && str_starts_with($url, '/uploads/');
}, true);

if ($alreadySaved) {
    jsonResponse(['success' => true, 'message' => 'すでにローカル保存済みです', 'skipped' => true]);
}

// 保存先ディレクトリ
$uploadDir = BASE_PATH . '/uploads/vehicles/' . $vehicleId;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$newImages = [];
$errors = [];

foreach ($images as $idx => $url) {
    // すでにローカルパスの場合はそのまま使用
    if (str_starts_with($url, '/uploads/')) {
        $newImages[] = $url;
        continue;
    }

    // 外部URLから画像をダウンロード
    $ctx = stream_context_create([
        'http' => [
            'timeout'    => 15,
            'user_agent' => 'Mozilla/5.0 (compatible; 5r3bot/1.0)',
            'header'     => "Referer: https://auctions.yahoo.co.jp/\r\n",
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);

    $imageData = @file_get_contents($url, false, $ctx);

    if ($imageData === false) {
        $errors[] = "画像 " . ($idx + 1) . " のダウンロードに失敗";
        // 失敗した場合は元のURLを維持
        $newImages[] = $url;
        continue;
    }

    // 拡張子を判定
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->buffer($imageData);
    $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $ext = $extMap[$mime] ?? 'jpg';

    $fileName = sprintf('%02d_%s.%s', $idx + 1, bin2hex(random_bytes(4)), $ext);
    $filePath = $uploadDir . '/' . $fileName;

    if (file_put_contents($filePath, $imageData) === false) {
        $errors[] = "画像 " . ($idx + 1) . " の保存に失敗";
        $newImages[] = $url;
        continue;
    }

    $newImages[] = '/uploads/vehicles/' . $vehicleId . '/' . $fileName;
}

// DBを更新
$stmt = $db->prepare('UPDATE vehicles SET images = ?, updated_at = ? WHERE id = ?');
$stmt->execute([
    json_encode($newImages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    date('Y-m-d H:i:s'),
    $vehicleId,
]);

$savedCount = count(array_filter($newImages, fn($p) => str_starts_with($p, '/uploads/')));

jsonResponse([
    'success'     => true,
    'saved'       => $savedCount,
    'total'       => count($newImages),
    'errors'      => $errors,
    'message'     => "{$savedCount}/" . count($newImages) . " 枚をローカルに保存しました",
]);
