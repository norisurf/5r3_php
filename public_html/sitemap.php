<?php
declare(strict_types=1);
/**
 * サイトマップ XML 生成
 * /sitemap.xml でアクセスできるよう .htaccess でリライト
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://5r3.co.jp';
$now = date('Y-m-d');

// 車両一覧を取得
$db = getDB();
$stmt = $db->query('SELECT id, updated_at FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC');
$vehicles = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <!-- トップページ（在庫一覧） -->
  <url>
    <loc><?= $baseUrl ?>/</loc>
    <lastmod><?= $now ?></lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>

  <!-- 中古車販売ページ -->
  <url>
    <loc><?= $baseUrl ?>/sales.php</loc>
    <lastmod><?= $now ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>

  <!-- 買取ページ -->
  <url>
    <loc><?= $baseUrl ?>/purchase.php</loc>
    <lastmod><?= $now ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>

  <!-- 会社概要ページ -->
  <url>
    <loc><?= $baseUrl ?>/company.php</loc>
    <lastmod><?= $now ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>

  <!-- ランディングページ -->
  <url>
    <loc><?= $baseUrl ?>/lp.php</loc>
    <lastmod><?= $now ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>

  <!-- 車両詳細ページ（動的） -->
<?php
declare(strict_types=1); foreach ($vehicles as $v):
    $lastmod = date('Y-m-d', strtotime($v['updated_at']));
?>
  <url>
    <loc><?= $baseUrl ?>/stock.php?id=<?= h($v['id']) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
<?php
declare(strict_types=1); endforeach; ?>
</urlset>
