<?php
declare(strict_types=1);
/**
 * 管理API: 外部画像URLをダウンロードしてローカル保存
 * POST { urls: string[] }
 * → { localPaths: string[] }
 *
 * 新規車両登録時に Yahoo 画像 URL をローカルに保存するために使用
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

initSession();
requireApiLogin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

verifyCsrfApi();

$body = getJsonBody();
$urls = $body['urls'] ?? [];

if (!is_array($urls) || empty($urls)) {
    jsonResponse(['error' => 'urls が必要です'], 400);
}

// 保存先: uploads/tmp/年月日/ に一時保存（車両ID確定後に移動不要、そのまま使う）
$dateDir = date('Ymd');
$uploadDir = BASE_PATH . '/uploads/tmp/' . $dateDir;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$localPaths = [];
$errors = [];

foreach (array_slice($urls, 0, 20) as $idx => $url) { // 最大20枚
    $url = (string)$url;

    // すでにローカルパスの場合はそのまま
    if (str_starts_with($url, '/uploads/')) {
        $localPaths[] = $url;
        continue;
    }

    // http/https 以外はスキップ
    if (!preg_match('#^https?://#', $url)) {
        $localPaths[] = $url;
        continue;
    }

    // cURL でダウンロード
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_HTTPHEADER     => [
            'Referer: https://auctions.yahoo.co.jp/',
            'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
        ],
    ]);

    $imageData = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($imageData === false || $httpCode !== 200 || empty($imageData)) {
        $errors[] = "画像 " . ($idx + 1) . " のDL失敗 (HTTP {$httpCode})";
        $localPaths[] = $url; // 失敗時は元のURLを維持
        continue;
    }

    // MIME タイプで拡張子を決定
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->buffer($imageData);
    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $ext = $extMap[$mime] ?? 'jpg';

    $fileName = sprintf('%02d_%s.%s', $idx + 1, bin2hex(random_bytes(6)), $ext);
    $filePath = $uploadDir . '/' . $fileName;

    if (file_put_contents($filePath, $imageData) === false) {
        $errors[] = "画像 " . ($idx + 1) . " の保存失敗";
        $localPaths[] = $url;
        continue;
    }

    $localPaths[] = '/uploads/tmp/' . $dateDir . '/' . $fileName;
}

jsonResponse([
    'localPaths' => $localPaths,
    'errors'     => $errors,
    'saved'      => count(array_filter($localPaths, fn($p) => str_starts_with($p, '/uploads/'))),
]);
