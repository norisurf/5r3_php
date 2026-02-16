<?php
/**
 * 管理API: Yahoo!オークションHTML解析
 * POST → HTMLを解析して車両情報を返す
 */
// Yahoo!オークションのHTMLは巨大なため、メモリを拡大
@ini_set('memory_limit', '256M');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/parser.php';

initSession();
requireApiLogin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

verifyCsrfApi();

$body = getJsonBody();

// WAF (SiteGuard) 回避: Base64エンコードされたHTMLをデコード
$html = '';
if (!empty($body['htmlBase64'])) {
    $html = base64_decode($body['htmlBase64']);
    if ($html === false) {
        jsonResponse(['error' => 'Base64デコードに失敗しました'], 400);
    }
} else {
    $html = $body['html'] ?? '';
}

if (empty($html)) {
    jsonResponse(['error' => 'HTMLが指定されていません'], 400);
}

try {
    $data = parseYahooVehicle($html);
    jsonResponse($data);
} catch (\Throwable $e) {
    jsonResponse(['error' => 'HTMLのパースに失敗しました', 'details' => $e->getMessage() . ' (line ' . $e->getLine() . ')'], 400);
}
