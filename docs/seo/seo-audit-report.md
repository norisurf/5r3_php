# SEO監査レポート - 5R3.co.jp

監査日: 2026-02-19
対象: カスタムPHPサイト（ワゴン・商用バン専門中古車販売）

---

## エグゼクティブサマリー

5R3のサイトは基本的なHTML構造・モバイル対応・HTTPS・Google Analytics等は実装済みだが、以下の重要なSEO要素が不足していた。優先度の高い問題をすでに修正済み。残課題は「手動対応が必要なもの」として記載。

---

## 修正済み（このセッションで対応）

### ✅ robots.txtの作成
- **場所**: `public_html/robots.txt`
- **内容**: Googlebot許可、/admin/ /includes/ /uploads/ をDisallow、サイトマップURL記載

### ✅ canonical URL・OGP・Twitter Cardタグの追加
- **場所**: `public_html/includes/header.php`
- `<link rel="canonical">` タグを追加（`$pageCanonicalUrl`変数で各ページから上書き可能）
- OGP (og:title, og:description, og:url, og:image, og:type等) を追加
- Twitter Card (summary_large_image) を追加
- 各ページで `$ogImage`, `$ogType`, `$pageCanonicalUrl` を設定して個別制御可能

### ✅ LocalBusiness（AutoDealer）スキーマの追加
- **場所**: `public_html/includes/footer.php`
- `@type: AutoDealer` のJSON-LDを追加
- 会社名・住所・電話・メール・営業時間・URLを記載

### ✅ stock.phpのメタディスクリプション・Vehicle スキーマ追加
- **場所**: `public_html/stock.php`
- 車両名・年式・走行距離・価格から動的にmeta descriptionを生成
- canonical URLを `/stock.php?id=[id]` に設定
- OG imageに車両の最初の画像を設定
- `@type: Car` JSON-LDスキーマを追加（価格・走行距離・販売者情報含む）

### ✅ lp.phpにGoogle Analytics・OGP・canonical追加
- **場所**: `public_html/lp.php`
- lp.phpは独立したHTMLドキュメントだったためGAコードが未設定だった
- Google Analytics (G-KFWPVCJJF6) を追加
- canonical URL (`/lp.php`)、OGP、Twitter Cardを追加

### ✅ sitemapにcompany.phpを追加
- **場所**: `public_html/sitemap.php`
- company.phpが未登録だったため追加（priority 0.6、monthly）

---

## 残課題（手動対応が必要）

### 🔴 HIGH: stock.phpのURLをクリーンURLに変更（タスク#7）

**現状**: `/stock.php?id=123`
**理想**: `/cars/123`

**対応手順**:

1. `.htaccess` に以下を追加:
   ```apache
   # 車両詳細クリーンURL
   RewriteRule ^cars/([0-9]+)/?$ /stock.php?id=$1 [L,QSA]
   ```

2. `sitemap.php` の車両URLを変更:
   ```php
   // 変更前
   <loc><?= $baseUrl ?>/stock.php?id=<?= h($v['id']) ?></loc>
   // 変更後
   <loc><?= $baseUrl ?>/cars/<?= h($v['id']) ?></loc>
   ```

3. `stock.php` の `$pageCanonicalUrl` を変更:
   ```php
   $pageCanonicalUrl = 'https://5r3.co.jp/cars/' . urlencode((string)$vehicle['id']);
   ```

4. `stock.php` 内の `href="/"` のbackリンクを `/sales.php` に変更（在庫一覧が正しい遷移先）

---

### 🟡 MEDIUM: .php拡張子なしのクリーンURL（タスク#6補完）

**現状**: `/sales.php`, `/purchase.php`, `/company.php`
**理想**: `/sales`, `/purchase`, `/company`

**対応手順**:

`.htaccess` に以下を追加:
```apache
# .php拡張子なしURL
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([a-z-]+)$ /$1.php [L]
```

`sitemap.php` のURLも合わせて変更（`/sales` 等）。

---

### 🟡 MEDIUM: config.phpのSNSリンクを修正（タスク#8）

**問題**:
- `LINK_LINE = '#contact'` → 実際のLINE公式アカウントURLに変更
- `LINK_TIKTOK = 'https://twitter.com/5r3inc'` → 正しいTikTok URLに変更
- `LINK_FACEBOOK = ''` → Facebookがなければヘッダーのfb iconsを非表示に変更

**対応**:
`public_html/includes/config.php` の以下を修正:
```php
define('LINK_LINE', 'https://line.me/R/ti/p/@[実際のLINE ID]');
define('LINK_TIKTOK', 'https://www.tiktok.com/@[実際のTikTokアカウント]');
```

---

### 🟡 MEDIUM: index.phpにページ固有のmeta・OG imageを設定

**現状**: index.phpが`$pageTitle`・`$pageDescription`を設定せずSITE_TITLE/DESCRIPTIONにフォールバック
**推奨**: トップページ専用のOG image（主力在庫の画像）を設定

```php
// index.phpの先頭に追加
$pageTitle = '5R3 CARS | ワゴン・商用バン専門中古車 | 東京・即日納車';
$pageDescription = 'キャラバン・バネット等のワゴン・商用バン専門中古車販売。最短当日車検・納車対応。東京都練馬区。在庫20台以上。';
$ogImage = 'https://5r3.co.jp/images/[トップ用OG画像].jpg'; // 1200×630px推奨
```

---

### 🟢 LOW: Tailwind CSS CDNを本番ビルドに変更

**現状**: `<script src="https://cdn.tailwindcss.com">` を使用（header.php・lp.php）
**問題**: CDN版はすべてのクラスを含み重い（~3MB+）、本番では使用非推奨
**推奨**: `npm init` → `tailwindcss` を導入し `/css/style.css` にビルド済みCSSを含める

---

### 🟢 LOW: 車両詳細ページのパンくずリスト（BreadcrumbList）追加

stock.phpにパンくずスキーマを追加すると検索結果にパンくずが表示される:

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "ホーム", "item": "https://5r3.co.jp/"},
    {"@type": "ListItem", "position": 2, "name": "中古車在庫一覧", "item": "https://5r3.co.jp/sales.php"},
    {"@type": "ListItem", "position": 3, "name": "[車両名]", "item": "https://5r3.co.jp/stock.php?id=[id]"}
  ]
}
```

---

### 🟢 LOW: 各ページのH1タグ確認

| ページ | H1内容 | 評価 |
|--------|--------|------|
| index.php | 最短即日在庫案内できます | ✅ 良好 |
| sales.php | 確認要 | 要確認 |
| purchase.php | その価値、プロの目で正当に評価。 | △ キーワード含有少 |
| company.php | 会社概要 | △ シンプルすぎ |
| lp.php | 最短即日在庫案内できます | ✅ 良好 |

---

## ページ別SEO設定まとめ

| ページ | title | description | canonical | OGP | Schema |
|--------|-------|-------------|-----------|-----|--------|
| index.php | ✅ SITE_TITLE | ✅ SITE_DESC | ✅ 追加済 | ✅ 追加済 | LocalBusiness✅ |
| sales.php | ✅ 設定済 | ✅ 設定済 | ✅ 追加済 | ✅ 追加済 | - |
| purchase.php | ✅ 設定済 | ✅ 設定済 | ✅ 追加済 | ✅ 追加済 | - |
| company.php | ✅ 設定済 | ✅ 設定済 | ✅ 追加済 | ✅ 追加済 | - |
| stock.php | ✅ 動的生成 | ✅ 動的生成 | ✅ 追加済 | ✅ 車両画像付 | Car✅ |
| lp.php | ✅ 設定済 | ✅ 設定済 | ✅ 追加済 | ✅ 追加済 | - |

---

## Technical SEO チェックリスト

| 項目 | 状態 | 備考 |
|------|------|------|
| HTTPS強制リダイレクト | ✅ | .htaccessで設定済 |
| robots.txt | ✅ 修正済 | 今回作成 |
| XML Sitemap | ✅ | /sitemap.xml (動的生成) |
| Canonical tags | ✅ 修正済 | 今回追加 |
| Open Graph tags | ✅ 修正済 | 今回追加 |
| Twitter Card | ✅ 修正済 | 今回追加 |
| LocalBusiness Schema | ✅ 修正済 | 今回追加 |
| Vehicle Schema | ✅ 修正済 | stock.phpに追加 |
| Google Analytics | ✅ | G-KFWPVCJJF6 (lp.phpも追加) |
| モバイル対応 | ✅ | レスポンシブ設計 |
| 画像alt属性 | △ | 一部改善余地あり |
| クリーンURL | ⚠️ 残課題 | .php拡張子・パラメータURL |
| Core Web Vitals | 要計測 | PageSpeed Insightsで確認 |
| Google Search Console | 要確認 | サイトマップ提出状況確認 |

---

## 優先対応アクション

1. **今すぐ**: Google Search Consoleでサイトマップ(https://5r3.co.jp/sitemap.xml)を再提出
2. **今すぐ**: config.phpのLINK_LINE・LINK_TIKTOKを正しいURLに修正
3. **今週中**: stock.phpのクリーンURL対応（/cars/[id]）
4. **今月中**: PageSpeed InsightsでCore Web Vitalsを計測・改善
5. **今後**: Tailwind CSSをCDNからビルド済みCSSに移行
