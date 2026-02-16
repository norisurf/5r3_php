# 5R3 PHPサイト デプロイメント技術書

お名前.com RSサーバーへのPHPサイト設置・WordPress配置変更・運用ガイド

**作成日**: 2025年2月12日
**対象サーバー**: お名前.com RSサーバー
**ドメイン**: 5r3.co.jp
**PHPバージョン**: 8.4（2025年2月時点）

---

## 目次

1. [サーバー環境の概要](#1-サーバー環境の概要)
2. [事前準備](#2-事前準備)
3. [既存WordPressサイトのバックアップと移動](#3-既存wordpressサイトのバックアップと移動)
4. [PHPサイトのデプロイ手順](#4-phpサイトのデプロイ手順)
5. [データベースのセットアップ](#5-データベースのセットアップ)
6. [SSL証明書とHTTPS設定](#6-ssl証明書とhttps設定)
7. [WordPressをブログとして再配置](#7-wordpressをブログとして再配置)
8. [管理者アカウントの作成](#8-管理者アカウントの作成)
9. [エラー対応・トラブルシューティング](#9-エラー対応トラブルシューティング)
10. [PHP更新時の注意事項](#10-php更新時の注意事項)
11. [ファイル構成リファレンス](#11-ファイル構成リファレンス)
12. [よく使うコマンド・SQL集](#12-よく使うコマンドsql集)

---

## 1. サーバー環境の概要

### RSサーバーのアーキテクチャ

お名前.com RSサーバーは **nginx → Apache** の二層構成。

```
[クライアント] → [nginx (SSL終端・リバースプロキシ)] → [Apache (PHP実行)]
```

**重要な特徴:**
- SSLはnginxが処理するため、ApacheからはHTTPに見える
- `%{HTTPS}` 変数は常に `off` になる
- SSL判定には `X-Forwarded-Proto` ヘッダーを使う
- WAF（SiteGuard Lite）が搭載されており、HTMLタグを含むPOSTリクエストをブロックする

### ディレクトリ構成

```
/home/r8861968/
  └── public_html/
        └── 5r3.co.jp/          ← ドキュメントルート（この中にのみ書き込み可能）
              ├── index.php       ← PHPサイト トップページ
              ├── admin/          ← 管理画面
              ├── includes/       ← PHP共通ファイル
              ├── js/             ← JavaScript
              ├── css/            ← スタイルシート
              ├── images/         ← 画像
              ├── uploads/        ← アップロードファイル
              ├── api/            ← 公開API
              ├── .htaccess       ← Apache設定
              └── blog/           ← WordPress（旧トップページから移動）
```

**注意:** `/public_html/` 直下にはディレクトリ作成権限がない。
`5r3.co.jp/` 内でのみ操作可能。

### データベース情報

| 項目 | 値 |
|------|------|
| ホスト | mysql17.onamae.ne.jp |
| DB名 | bycm2_7a54v886 |
| PHPサイト用ユーザー | bycm2_5r3_cars |
| WordPress用ユーザー | bycm2_3e8hsm7c |
| テーブルプレフィックス（WP） | wp_ |

---

## 2. 事前準備

### 必要なツール

- **FileZilla** （FTPクライアント）
- **ブラウザ** （Chrome推奨、開発者ツール使用）
- **お名前.com コントロールパネル** へのアクセス

### FTP接続情報

お名前.comのコントロールパネル → サーバー管理 → FTP情報から確認。
FileZillaで接続後、`/public_html/5r3.co.jp/` に移動。

### ローカル開発環境

```
D:\5r3\5r3_php\
  ├── public_html/    ← サーバーにアップするファイル群
  ├── sql/            ← データベーススキーマ・初期データ
  │     ├── schema.sql
  │     └── insert_vehicles.sql
  └── DEPLOYMENT_GUIDE.md  ← この文書
```

ローカルでは `config.php` の `USE_SQLITE` を `true` にすると、SQLiteで動作テスト可能。

---

## 3. 既存WordPressサイトのバックアップと移動

### 手順

1. **FileZillaで `5r3.co.jp/` 内に `wp_backup` フォルダを作成**
   - ※ `public_html/` 直下ではなく `5r3.co.jp/` 内に作成すること

2. **WordPressファイルを `wp_backup` に移動**
   - 移動対象: `wp-admin/`, `wp-content/`, `wp-includes/`, `wp-*.php`, `xmlrpc.php`, `license.txt`, `readme.html`
   - `.htaccess` は一旦リネーム（`_htaccess_wp_bak`）して保管

3. **移動後、ドキュメントルートが空になったことを確認**

### 注意事項

- WordPressのファイルが残っていると、PHPサイトと競合してnginxエラーが発生する
- 特に `wp-config.php` や `.htaccess` が残っていないか確認
- 移動後にnginxが「An error occurred」を返す場合は、[PHPバージョン変更トリック](#91-nginx-an-error-occurred)を実行

---

## 4. PHPサイトのデプロイ手順

### Step 1: ファイルのアップロード

FileZillaで `D:\5r3\5r3_php\public_html\` の中身を `/public_html/5r3.co.jp/` にアップロード。

**アップロードするファイル・フォルダ:**

```
index.php
stock.php
sales.php
purchase.php
lp.php
.htaccess
admin/
api/
css/
images/
includes/
js/
uploads/
video/
```

**アップロードしないファイル:**
- `local.sqlite` （ローカル開発用DB）
- `test.php` （テスト用）

### Step 2: config.php の設定

`includes/config.php` でデータベース接続先を設定:

```php
// 本番環境では必ず false
define('USE_SQLITE', false);

// RSサーバーのMySQL情報
define('DB_HOST', 'mysql17.onamae.ne.jp');
define('DB_NAME', 'bycm2_7a54v886');
define('DB_USER', 'bycm2_5r3_cars');
define('DB_PASS', 'パスワード');
define('DB_CHARSET', 'utf8');
```

### Step 3: .htaccess の配置

**重要:** RSサーバー専用の `.htaccess` を使用する。通常のHTTPS判定は動かない。

```apache
# HTTPS強制リダイレクト（お名前.com RSサーバー対応）
RewriteEngine On
RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]

# セキュリティヘッダー
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# .htaccess, .htpasswd のアクセス禁止
<FilesMatch "^\.ht">
    Deny from all
</FilesMatch>
```

**絶対にやってはいけない設定:**
```apache
# NG: RSサーバーではHTTPSは常にoff
RewriteCond %{HTTPS} off
```

### Step 4: 動作確認

1. `https://5r3.co.jp/` にアクセスしてトップページ表示を確認
2. `https://5r3.co.jp/admin/login.php` でログイン画面を確認
3. リダイレクトループが発生したら `.htaccess` を確認

---

## 5. データベースのセットアップ

### phpMyAdminへのアクセス

お名前.com コントロールパネル → データベース → phpMyAdmin

### Step 1: テーブル作成

phpMyAdminの「SQL」タブで `sql/schema.sql` の内容を実行:

```sql
CREATE TABLE IF NOT EXISTS `vehicles` (
    `id` VARCHAR(30) NOT NULL,
    `manage_number` VARCHAR(20) NOT NULL,
    `title` VARCHAR(500) NOT NULL DEFAULT '',
    `price` INT NOT NULL DEFAULT 0,
    `images` TEXT,
    `basic_info` TEXT,
    `detailed_info` TEXT,
    `equipment` TEXT,
    `description` TEXT,
    `display_on_lp` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_manage_number` (`manage_number`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_display_on_lp` (`display_on_lp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `banners` (
    `id` VARCHAR(30) NOT NULL,
    `mode` VARCHAR(10) NOT NULL DEFAULT 'manual',
    `image_url` VARCHAR(500) NOT NULL DEFAULT '',
    `link_url` VARCHAR(500) NOT NULL DEFAULT '',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: 初期データの投入

車両データがある場合は `sql/insert_vehicles.sql` を実行。

### Step 3: ローカルSQLiteから本番MySQLへのデータ移行

ローカルSQLiteに車両データがある場合の移行手順:

1. ローカルでPHPスクリプトを作成してSQLiteからデータを読み取り
2. MySQL用のINSERT文を生成
3. phpMyAdminのSQLタブで実行

---

## 6. SSL証明書とHTTPS設定

### SSL証明書の申請

1. お名前.com コントロールパネル → セキュリティ → SSL証明書
2. 対象ドメインを選択して申込み
3. 反映まで最大数時間かかる場合がある

### 証明書の反映確認

```
https://5r3.co.jp/
```

にアクセスして鍵マークが表示されればOK。

### SSL反映前の注意

- SSL証明書が未反映の状態で `.htaccess` のHTTPSリダイレクトを有効にすると、リダイレクトループが発生する
- SSL反映を待ってから `.htaccess` を配置すること
- 待てない場合は `.htaccess` を一旦 `.htaccess_bak` にリネーム

---

## 7. WordPressをブログとして再配置

### Step 1: フォルダ名の変更

FileZillaで `wp_backup` → `blog` にリネーム。

### Step 2: WordPress URLの変更

phpMyAdminで `wp_options` テーブルの以下2行を変更:

| option_name | 変更前 | 変更後 |
|---|---|---|
| siteurl | https://5r3.co.jp | https://5r3.co.jp/blog |
| home | https://5r3.co.jp | https://5r3.co.jp/blog |

**SQL文:**
```sql
UPDATE wp_options SET option_value = 'https://5r3.co.jp/blog' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = 'https://5r3.co.jp/blog' WHERE option_name = 'home';
```

### Step 3: wp-config.php の確認

`blog/wp-config.php` にSSL対応設定があることを確認:

```php
/** お名前.com RS サーバー SSL対応 */
if( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
    $_SERVER['HTTPS'] = 'on';
}
```

この設定がないと、WordPress管理画面でCSS・画像が読み込めない。

### Step 4: 動作確認

- `https://5r3.co.jp/blog/` でブログが表示されるか
- CSS・画像が正常に読み込まれるか
- `wp-config.php` を**直接編集しない場合**は、phpMyAdminでの変更のみでOK

---

## 8. 管理者アカウントの作成

### adminsテーブルが空の場合

phpMyAdminで以下のSQLを実行:

```sql
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$10$ここにbcryptハッシュ');
```

### bcryptハッシュの生成方法

サーバー上に一時的なPHPファイルを作成:

```php
<?php
echo password_hash('設定したいパスワード', PASSWORD_DEFAULT);
```

ブラウザでアクセスしてハッシュを取得後、**必ずファイルを削除**。

### パスワードリセット

phpMyAdminで `admins` テーブルの `password_hash` カラムを新しいハッシュで更新:

```sql
UPDATE admins SET password_hash = '$2y$10$新しいハッシュ' WHERE username = 'admin';
```

---

## 9. エラー対応・トラブルシューティング

### 9.1 nginx「An error occurred」

**原因:** nginxの設定キャッシュが古い、またはドキュメントルートにファイルが正しく配置されていない。

**解決方法: PHPバージョン変更トリック**

1. お名前.com コントロールパネル → PHP設定
2. PHPバージョンを **8.4 → 8.1** に変更して保存
3. 少し待ってから **8.1 → 8.4** に戻して保存
4. これによりnginxの設定がリロードされる

### 9.2 リダイレクトループ（ERR_TOO_MANY_REDIRECTS）

**原因:** `.htaccess` のHTTPS判定が間違っている。

**誤:**
```apache
RewriteCond %{HTTPS} off
```

**正:**
```apache
RewriteCond %{HTTP:X-Forwarded-Proto} =http
```

**応急処置:** `.htaccess` を `.htaccess_bak` にリネーム。

### 9.3 FTP 550 Permission Denied

**原因:** `/public_html/` 直下にディレクトリ作成権限がない。

**解決:** すべての操作は `/public_html/5r3.co.jp/` 内で行う。

### 9.4 フォントが異なる

**原因:** Next.jsアプリでは Geist Sans が使われていたが、PHPサイトにはフォント指定がない。

**解決:** `includes/header.php` にGoogle Fonts（Inter）を追加:

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
```

`css/style.css` のフォント指定:
```css
font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Hiragino Sans', 'Noto Sans JP', sans-serif;
```

### 9.5 車両データが表示されない

**原因:** MySQLの `vehicles` テーブルが空。

**解決:**
1. ローカルSQLiteからINSERT文を生成
2. phpMyAdminのSQLタブで実行
3. または管理画面（`/admin/new.php`）から手動登録

### 9.6 WordPress CSS・画像が読み込めない

**原因:** `wp_options` テーブルの `siteurl` と `home` が更新されていない。

**解決:** [7. WordPressをブログとして再配置](#7-wordpressをブログとして再配置)の手順を実行。

### 9.7 「パースに失敗しました」（Yahoo!オークション解析）

**原因は複数:**

#### 原因A: WAF（SiteGuard Lite）によるブロック

RSサーバーのWAFがHTMLタグを含むPOSTリクエストを「攻撃」と判断してブロック。

**解決:** HTMLをBase64エンコードして送信する。

- `js/admin.js` の `parseHtml()` 関数:
  ```javascript
  // HTMLをBase64エンコードして送信
  var encoded = btoa(unescape(encodeURIComponent(htmlInput.value)));
  body: JSON.stringify({ htmlBase64: encoded })
  ```

- `admin/api/parse.php` のサーバー側:
  ```php
  if (!empty($body['htmlBase64'])) {
      $html = base64_decode($body['htmlBase64']);
  } else {
      $html = $body['html'] ?? '';
  }
  ```

#### 原因B: PHP 8.2以降で mb_convert_encoding の HTML-ENTITIES が非推奨・削除

**誤（PHP 8.4でエラー）:**
```php
$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
```

**正（PHP 8.4対応）:**
```php
$htmlWithMeta = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . $html;
$dom->loadHTML($htmlWithMeta, LIBXML_NOERROR | LIBXML_NOWARNING);
```

#### 原因C: 正規表現が巨大HTMLに対応できない

`(.*?)` (lazy match) は巨大な日本語JSONで途中切断される場合がある。

**解決:** `strpos` + `substr` で確実に切り出す:
```php
$ndStart = strpos($html, 'id="__NEXT_DATA__"');
if ($ndStart !== false) {
    $jsonStart = strpos($html, '>', $ndStart) + 1;
    $jsonEnd = strpos($html, '</script>', $jsonStart);
    $jsonStr = substr($html, $jsonStart, $jsonEnd - $jsonStart);
    $nextData = json_decode($jsonStr, true);
}
```

#### 原因D: catch (Exception) では ValueError をキャッチできない

PHP 8.4 の `ValueError` は `Error` クラスの子であり `Exception` ではない。

**誤:**
```php
catch (Exception $e) {
```

**正:**
```php
catch (\Throwable $e) {
```

---

## 10. PHP更新時の注意事項

### PHPバージョンアップ時のチェックリスト

お名前.comでPHPバージョンを変更する際の確認事項:

#### 1. 非推奨関数の確認

| 関数 | 非推奨バージョン | 削除バージョン | 代替 |
|------|------|------|------|
| `mb_convert_encoding(..., 'HTML-ENTITIES')` | PHP 8.2 | PHP 8.4 | メタタグ方式 |
| `utf8_encode()` / `utf8_decode()` | PHP 8.2 | PHP 8.4 | `mb_convert_encoding()` |
| `${}` 文字列内変数展開 | PHP 8.2 | PHP 9.0 | `{$var}` |

#### 2. エラーハンドリングの確認

- `catch (Exception $e)` が使われている箇所を `catch (\Throwable $e)` に変更
- PHP 8.x 以降は `TypeError`, `ValueError` 等が `Error` クラスの子で `Exception` ではない

#### 3. parser.php の特記事項

- DOMDocument の `loadHTML()` はHTMLエンコーディング処理方法がバージョンごとに変わる
- 正規表現より `strpos` + `substr` のほうが巨大HTMLに安定
- `__NEXT_DATA__` の構造はYahoo!オークション側の変更で変わる可能性がある

#### 4. WAF対策の維持

- HTMLやスクリプトタグを含むPOSTリクエストは必ずBase64エンコードする
- `SiteGuard Lite` のルール更新でブロック対象が変わる可能性がある
- 新しいAPIエンドポイントで生HTMLを受け取る場合は必ずBase64方式を使う

#### 5. PHPバージョン変更の手順

1. ローカルで新しいPHPバージョンでテスト（`php -v` で確認）
2. お名前.com コントロールパネルでPHPバージョンを変更
3. サイトの全ページの動作を確認
4. エラーが発生したら旧バージョンに戻す

### ファイル更新時のアップロード手順

1. ローカルで編集
2. FileZillaでサーバーの該当ファイルを上書きアップロード
3. ブラウザのキャッシュをクリア（Ctrl+Shift+R）して動作確認
4. JSファイルの更新が反映されない場合はブラウザの強制リロード

---

## 11. ファイル構成リファレンス

### PHPサイト本体

```
public_html/
├── index.php              # トップページ（在庫一覧）
├── stock.php              # 在庫詳細ページ
├── sales.php              # 販売ページ
├── purchase.php           # 買取ページ
├── lp.php                 # ランディングページ
├── .htaccess              # Apache設定（RSサーバー対応版）
│
├── includes/
│   ├── config.php         # DB接続情報・サイト設定 ★重要
│   ├── db.php             # PDO接続（SQLite/MySQL切替）
│   ├── functions.php      # ヘルパー関数
│   ├── auth.php           # 認証関連
│   ├── csrf.php           # CSRFトークン管理
│   ├── parser.php         # Yahoo!オークションHTMLパーサー ★WAF/PHP互換注意
│   ├── header.php         # 共通HTMLヘッダー
│   ├── footer.php         # 共通フッター
│   ├── admin_header.php   # 管理画面用ヘッダー
│   ├── admin_footer.php   # 管理画面用フッター
│   └── .htaccess          # includes へのアクセス禁止
│
├── admin/
│   ├── index.php          # 管理ダッシュボード
│   ├── login.php          # ログイン画面
│   ├── logout.php         # ログアウト
│   ├── new.php            # 新規車両登録
│   ├── edit.php           # 車両編集
│   ├── .htaccess          # Basic認証設定（コメントアウト中）
│   └── api/
│       ├── parse.php      # HTMLパースAPI ★Base64受信
│       ├── vehicle.php    # 車両 単体CRUD
│       ├── vehicles.php   # 車両 一覧・新規作成
│       ├── banner.php     # バナー管理
│       └── upload.php     # 画像アップロード
│
├── api/
│   ├── lp_vehicles.php    # LP用車両API
│   └── lp_contact.php     # LP問い合わせAPI
│
├── js/
│   ├── admin.js           # 管理画面JS ★Base64エンコード処理含む
│   ├── main.js            # フロント共通JS
│   ├── gallery.js         # 画像ギャラリー
│   └── sort.js            # 在庫ソート機能
│
├── css/
│   └── style.css          # カスタムCSS（Tailwind CDNと併用）
│
├── images/                # サイト画像
├── uploads/               # アップロード画像
│   └── .htaccess          # セキュリティ設定
└── video/                 # 動画ファイル
```

### データベーススキーマ

```
sql/
├── schema.sql             # テーブル定義（vehicles, banners, admins）
└── insert_vehicles.sql    # 車両初期データ（SQLiteからの移行用）
```

---

## 12. よく使うコマンド・SQL集

### 管理者パスワードのリセット

```sql
-- パスワードハッシュを生成（サーバー上の一時PHPで実行）
-- <?php echo password_hash('新しいパスワード', PASSWORD_DEFAULT); ?>

-- adminsテーブルを更新
UPDATE admins SET password_hash = '生成したハッシュ' WHERE username = 'admin';
```

### WordPress URLの変更

```sql
-- ブログを /blog/ に移動する場合
UPDATE wp_options SET option_value = 'https://5r3.co.jp/blog' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = 'https://5r3.co.jp/blog' WHERE option_name = 'home';
```

### 車両データの確認

```sql
-- 車両一覧
SELECT id, title, price, created_at FROM vehicles ORDER BY created_at DESC;

-- 車両数
SELECT COUNT(*) FROM vehicles;
```

### デバッグ用テストファイル

サーバー環境の確認用。**使用後は必ず削除すること。**

```php
<?php
// test_env.php - 環境確認用
echo "PHP Version: " . PHP_VERSION . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
```

---

## 付録: 今回のデプロイで発生した問題と解決の時系列

| # | 問題 | 原因 | 解決策 |
|---|------|------|--------|
| 1 | FTP 550 Permission Denied | /public_html直下は権限なし | 5r3.co.jp/内で操作 |
| 2 | nginx "An error occurred" | WordPress残存ファイルとの競合 | 全WPファイルを移動 + PHPバージョン変更トリック |
| 3 | リダイレクトループ | .htaccessの %{HTTPS} off | X-Forwarded-Proto に変更 |
| 4 | フォント不一致 | Geist Sans → システムフォント | Google Fonts Inter を追加 |
| 5 | 車両データ空 | MySQL vehiclesテーブルが空 | SQLiteからINSERT文生成 → phpMyAdminで実行 |
| 6 | WordPress CSS崩れ | siteurl/homeが旧URL | phpMyAdminでwp_options更新 |
| 7 | 管理ログイン不可 | adminsテーブルが空 | bcryptハッシュ生成 → INSERT実行 |
| 8 | パース失敗 | WAF(SiteGuard)がHTMLをブロック + PHP8.4のmb_convert_encoding削除 | Base64エンコード送信 + strpos方式 + Throwableキャッチ |

---

*この文書は 5R3 PHPサイト（5r3.co.jp）のデプロイ・運用に関する技術リファレンスです。*
