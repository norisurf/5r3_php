<?php
declare(strict_types=1);
/**
 * サイトマップ XML 生成
 * /sitemap.xml でアクセスできるよう .htaccess でリライト
 */

// PHPエラー・警告をXML出力に混入させない
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// すべての出力をバッファリング（include先の意図しない出力を遮断）
ob_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ob_end_clean();

$baseUrl = 'https://5r3.co.jp';
$now = date('Y-m-d');

// XMLを文字列として組み立てる
$lines = [];
$lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
$lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// 静的ページ
$staticPages = [
    ['url' => '/',             'freq' => 'daily',   'pri' => '1.0'],
    ['url' => '/sales.php',    'freq' => 'weekly',  'pri' => '0.8'],
    ['url' => '/purchase.php', 'freq' => 'monthly', 'pri' => '0.8'],
    ['url' => '/company.php',  'freq' => 'monthly', 'pri' => '0.6'],
    ['url' => '/lp.php',       'freq' => 'weekly',  'pri' => '0.7'],
];

foreach ($staticPages as $page) {
    $lines[] = '  <url>';
    $lines[] = '    <loc>' . $baseUrl . $page['url'] . '</loc>';
    $lines[] = '    <lastmod>' . $now . '</lastmod>';
    $lines[] = '    <changefreq>' . $page['freq'] . '</changefreq>';
    $lines[] = '    <priority>' . $page['pri'] . '</priority>';
    $lines[] = '  </url>';
}

// 動的車両ページ（DBエラーが起きても静的ページ分は出力できるようtry/catchで保護）
try {
    $db = getDB();
    $stmt = $db->query('SELECT id, updated_at FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC');
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($vehicles as $v) {
        $updatedAt = isset($v['updated_at']) && $v['updated_at'] !== '' ? $v['updated_at'] : null;
        $lastmod = $updatedAt !== null ? date('Y-m-d', (int)strtotime($updatedAt)) : $now;
        $lines[] = '  <url>';
        $lines[] = '    <loc>' . $baseUrl . '/stock.php?id=' . rawurlencode((string)$v['id']) . '</loc>';
        $lines[] = '    <lastmod>' . $lastmod . '</lastmod>';
        $lines[] = '    <changefreq>weekly</changefreq>';
        $lines[] = '    <priority>0.6</priority>';
        $lines[] = '  </url>';
    }
} catch (\Throwable $e) {
    // DB接続失敗時は静的ページのみ出力（エラーをXMLに混入させない）
}

$lines[] = '</urlset>';

$xml = implode("\n", $lines) . "\n";

// クリーンなXMLのみ送信
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');
echo $xml;
