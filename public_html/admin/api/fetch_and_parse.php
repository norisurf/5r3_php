<?php
declare(strict_types=1);
/**
 * 管理API: Yahoo!オークションURLからHTML取得＆解析
 * POST → URLを受け取り、HTMLを取得して車両情報を返す
 */
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
$url = trim($body['url'] ?? '');

if (empty($url)) {
    jsonResponse(['error' => 'URLが指定されていません'], 400);
}

// Yahoo!オークションのURLかチェック
if (!preg_match('#^https?://(page\.|auctions\.)?yahoo\.co\.jp/#', $url)
    && !preg_match('#^https?://auctions\.yahoo\.co\.jp/#', $url)) {
    jsonResponse(['error' => 'Yahoo!オークションのURLを入力してください'], 400);
}

// cURLでHTMLを取得
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    CURLOPT_HTTPHEADER => [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: ja,en-US;q=0.7,en;q=0.3',
    ],
    CURLOPT_ENCODING => '', // gzip等の自動解凍
]);

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($html === false || !empty($curlError)) {
    jsonResponse(['error' => 'HTMLの取得に失敗しました', 'details' => $curlError], 500);
}

if ($httpCode !== 200) {
    jsonResponse(['error' => "HTMLの取得に失敗しました (HTTP {$httpCode})"], 500);
}

if (empty($html)) {
    jsonResponse(['error' => '取得したHTMLが空です'], 500);
}

try {
    $data = parseYahooVehicle($html);
    jsonResponse($data);
} catch (\Throwable $e) {
    error_log('fetch_and_parse.php: ' . $e->getMessage() . ' (line ' . $e->getLine() . ')');
    jsonResponse(['error' => 'HTMLのパースに失敗しました'], 400);
}
