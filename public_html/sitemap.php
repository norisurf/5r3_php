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
$lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

// 静的ページ
$staticPages = [
    ['url' => '/',             'freq' => 'daily',   'pri' => '1.0', 'video' => 'top'],
    ['url' => '/sales.php',    'freq' => 'weekly',  'pri' => '0.8', 'video' => false],
    ['url' => '/purchase.php', 'freq' => 'monthly', 'pri' => '0.8', 'video' => 'purchase'],
    ['url' => '/company.php',  'freq' => 'monthly', 'pri' => '0.6', 'video' => false],
    ['url' => '/lp.php',       'freq' => 'weekly',  'pri' => '0.7', 'video' => 'top'],
];

foreach ($staticPages as $page) {
    $lines[] = '  <url>';
    $lines[] = '    <loc>' . $baseUrl . $page['url'] . '</loc>';
    $lines[] = '    <lastmod>' . $now . '</lastmod>';
    $lines[] = '    <changefreq>' . $page['freq'] . '</changefreq>';
    $lines[] = '    <priority>' . $page['pri'] . '</priority>';
    
    if ($page['video'] === 'top') {
        $lines[] = '    <video:video>';
        $lines[] = '      <video:thumbnail_loc>' . $baseUrl . '/images/lp/video_thumbnail.jpg</video:thumbnail_loc>';
        $lines[] = '      <video:title>5R3 ワンボックス・軽バン中古専門店 プロモーション</video:title>';
        $lines[] = '      <video:description>練馬・大泉・土支田のワンボックスカー・軽バン中古専門店 5R3 の在庫車両紹介動画です。最短当日納車可能です。</video:description>';
        $lines[] = '      <video:player_loc>' . $baseUrl . '/</video:player_loc>';
        $lines[] = '      <video:publication_date>2024-03-01T08:00:00+09:00</video:publication_date>';
        $lines[] = '      <video:family_friendly>yes</video:family_friendly>';
        $lines[] = '    </video:video>';
    } elseif ($page['video'] === 'purchase') {
        $lines[] = '    <video:video>';
        $lines[] = '      <video:thumbnail_loc>' . $baseUrl . '/images/lp/Image_of_a_car_showroom_top.png</video:thumbnail_loc>';
        $lines[] = '      <video:title>練馬区の車買取・車査定なら5R3 | 中古車・商用車を高価買取</video:title>';
        $lines[] = '      <video:description>練馬区の車買取・車査定プロセスを動画で解説。ハイエース・キャラバンなど中古車・商用車の高額査定に自信があります。</video:description>';
        $lines[] = '      <video:player_loc>' . $baseUrl . '/purchase.php</video:player_loc>';
        $lines[] = '      <video:publication_date>2026-03-13T10:00:00+09:00</video:publication_date>';
        $lines[] = '      <video:family_friendly>yes</video:family_friendly>';
        $lines[] = '    </video:video>';
    }
    
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
