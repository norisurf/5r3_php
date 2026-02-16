# 5R3 CARS PHP版 構築手順書

## 1. プロジェクト概要

### 1.1 目的
Next.js (Vercel) で稼働中の「5R3 CARS」をPHP + MySQLに書き直し、
お名前.com RSサーバーで動作させる。

### 1.2 移行元の構成
- **フレームワーク**: Next.js 16 (App Router, SSR)
- **データ層**: JSON ファイルDB (Prisma互換アダプター)
- **デプロイ先**: Vercel
- **ソースコード**: D:\5r3\5r3_app

### 1.3 移行先の構成
- **言語**: PHP 8.x
- **データベース**: MySQL 8.x (RSサーバー提供)
- **Webサーバー**: Apache (RSサーバー標準)
- **CSS**: Tailwind CSS (ビルド済みCSSを配置)
- **JS**: Vanilla JavaScript (Framer Motion → CSS animations / IntersectionObserver)
- **デプロイ方法**: FTP / ファイルマネージャー

---

## 2. ディレクトリ構成

```
D:\5r3\5r3_php\
├── BUILD_GUIDE.md          ← 本ファイル（構築手順書）
├── public_html/            ← RSサーバーの公開ディレクトリにアップロードする内容
│   ├── .htaccess           ← HTTPS強制、URL書き換え
│   ├── index.php           ← トップページ（LP）
│   ├── sales.php           ← 中古車販売ページ
│   ├── purchase.php        ← 買取ページ
│   ├── stock.php           ← 車両詳細ページ (?id=xxx)
│   ├── lp.php              ← LP専用ページ
│   │
│   ├── admin/
│   │   ├── .htaccess       ← Basic認証
│   │   ├── index.php       ← 管理ダッシュボード
│   │   ├── new.php         ← 車両新規登録
│   │   ├── edit.php        ← 車両編集 (?id=xxx)
│   │   ├── login.php       ← ログイン画面
│   │   ├── logout.php      ← ログアウト処理
│   │   └── api/
│   │       ├── vehicles.php    ← 車両CRUD API (GET/POST)
│   │       ├── vehicle.php     ← 車両個別API (GET/PUT/DELETE, ?id=xxx)
│   │       ├── banner.php      ← バナー管理API (GET/POST)
│   │       ├── upload.php      ← 画像アップロードAPI
│   │       └── parse.php       ← Yahoo HTMLパーサーAPI
│   │
│   ├── api/
│   │   └── lp_vehicles.php ← LP用車両データ取得API (公開)
│   │
│   ├── includes/
│   │   ├── .htaccess       ← 直接アクセス禁止
│   │   ├── config.php      ← DB接続設定・定数
│   │   ├── db.php          ← PDO接続シングルトン
│   │   ├── auth.php        ← 認証関連関数
│   │   ├── csrf.php        ← CSRFトークン管理
│   │   ├── functions.php   ← 共通ヘルパー関数
│   │   ├── header.php      ← 共通HTMLヘッダー
│   │   ├── footer.php      ← 共通HTMLフッター
│   │   ├── admin_header.php ← 管理画面用ヘッダー
│   │   └── parser.php      ← Yahoo HTML パーサー (DOMDocument)
│   │
│   ├── css/
│   │   └── style.css       ← Tailwind CSS ビルド済み + カスタムCSS
│   │
│   ├── js/
│   │   ├── main.js         ← 共通JS (モバイルメニュー、スクロール等)
│   │   ├── admin.js        ← 管理画面用JS (CRUD操作、トグル等)
│   │   ├── gallery.js      ← 画像ギャラリー・ライトボックス
│   │   └── sort.js         ← 在庫一覧ソート機能
│   │
│   ├── images/             ← サイト画像（ロゴ、アイコン、LP画像等）
│   │   ├── 5r3_rogo.png
│   │   ├── line-icon.png
│   │   ├── insta-icon.png
│   │   ├── x-icon.png
│   │   ├── tiktok-4.png
│   │   ├── Facebook.png
│   │   ├── mail.png
│   │   ├── favicon.png
│   │   └── lp/
│   │       ├── avatar_driver.png
│   │       ├── avatar_caregiver.png
│   │       └── hero_fleet.png
│   │
│   ├── video/              ← 動画ファイル
│   │   ├── 5r3_01.mp4
│   │   ├── 5r3_03.mp4
│   │   └── IMG_0415.MOV
│   │
│   └── uploads/            ← アップロード画像保存先
│       └── .htaccess       ← PHP実行禁止
│
└── sql/
    └── schema.sql          ← テーブル作成SQL
```

---

## 3. データベース設計

### 3.1 テーブル: `vehicles`

| カラム名 | 型 | 説明 |
|---|---|---|
| id | VARCHAR(30) PRIMARY KEY | ランダムID |
| manage_number | VARCHAR(20) UNIQUE | 管理番号 (CAR-YYYYMMDD-001) |
| title | VARCHAR(500) | 車両タイトル |
| price | INT | 価格（円） |
| images | JSON / TEXT | 画像URL配列 (JSON文字列) |
| basic_info | JSON / TEXT | 基本情報 (JSON文字列) |
| detailed_info | JSON / TEXT | 詳細情報 (JSON文字列) |
| equipment | JSON / TEXT | 装備品 (JSON文字列) |
| description | TEXT | 商品説明 |
| display_on_lp | TINYINT(1) DEFAULT 0 | LP表示フラグ |
| created_at | DATETIME DEFAULT CURRENT_TIMESTAMP | 作成日時 |
| updated_at | DATETIME ON UPDATE CURRENT_TIMESTAMP | 更新日時 |

### 3.2 テーブル: `banners`

| カラム名 | 型 | 説明 |
|---|---|---|
| id | VARCHAR(30) PRIMARY KEY | ランダムID |
| mode | VARCHAR(10) | 'auto' or 'manual' |
| image_url | VARCHAR(500) | バナー画像URL |
| link_url | VARCHAR(500) | リンク先URL |
| is_active | TINYINT(1) DEFAULT 1 | アクティブフラグ |
| created_at | DATETIME DEFAULT CURRENT_TIMESTAMP | 作成日時 |
| updated_at | DATETIME ON UPDATE CURRENT_TIMESTAMP | 更新日時 |

### 3.3 テーブル: `admins`

| カラム名 | 型 | 説明 |
|---|---|---|
| id | INT AUTO_INCREMENT PRIMARY KEY | 管理者ID |
| username | VARCHAR(50) UNIQUE | ユーザー名 |
| password_hash | VARCHAR(255) | bcryptハッシュ |
| created_at | DATETIME DEFAULT CURRENT_TIMESTAMP | 作成日時 |

---

## 4. 構築フェーズと実装順序

### Phase 1: 基盤構築
1. ディレクトリ構成の作成
2. `sql/schema.sql` — テーブル作成SQL
3. `includes/config.php` — DB接続情報・サイト設定定数
4. `includes/db.php` — PDO接続シングルトン
5. `includes/functions.php` — 共通ヘルパー (エスケープ、ID生成等)
6. `includes/auth.php` — セッション認証関数
7. `includes/csrf.php` — CSRFトークン管理
8. `.htaccess` ファイル群（HTTPS強制、アクセス制御）

### Phase 2: CSS/JS 基盤
9. `css/style.css` — Tailwind CDN or ビルド済みCSS + カスタムCSS（.bg-metallic等）
10. `js/main.js` — モバイルメニュー開閉、スクロールトップ、Sticky CTA

### Phase 3: 共通テンプレート
11. `includes/header.php` — HTMLヘッダー + ナビゲーション
12. `includes/footer.php` — HTMLフッター
13. `includes/admin_header.php` — 管理画面用ヘッダー

### Phase 4: フロントエンドページ
14. `index.php` — トップページ（Hero, 在庫一覧, 共感, 強み, お客様の声, CTA）
15. `sales.php` — 中古車販売ページ
16. `purchase.php` — 買取ページ
17. `stock.php` — 車両詳細ページ（ギャラリー、スペック、説明）
18. `lp.php` — LP専用ページ
19. `js/gallery.js` — 画像ギャラリー・ライトボックス
20. `js/sort.js` — 在庫ソート機能（価格順、年式順、走行距離順）

### Phase 5: 管理画面 API
21. `admin/api/vehicles.php` — 車両一覧取得 (GET) / 新規登録 (POST)
22. `admin/api/vehicle.php` — 車両詳細取得 (GET) / 更新 (PUT) / 削除 (DELETE)
23. `admin/api/banner.php` — バナー設定取得 (GET) / 更新 (POST)
24. `admin/api/upload.php` — 画像アップロード
25. `admin/api/parse.php` — Yahoo HTMLパーサー
26. `api/lp_vehicles.php` — LP表示車両API（公開）

### Phase 6: 管理画面 UI
27. `admin/login.php` — ログインフォーム + 認証処理
28. `admin/logout.php` — ログアウト処理
29. `admin/index.php` — 管理ダッシュボード（車両一覧テーブル、バナー管理）
30. `admin/new.php` — 車両新規登録フォーム（HTMLパース + 手動入力）
31. `admin/edit.php` — 車両編集フォーム
32. `js/admin.js` — 管理画面用JS（削除確認、LP表示トグル、複製、バナープレビュー等）

### Phase 7: セキュリティ・仕上げ
33. 全ページでXSS対策（htmlspecialchars）の確認
34. 全API でSQLインジェクション対策（プリペアドステートメント）の確認
35. CSRFトークンの全フォーム適用確認
36. アップロードファイル検証の確認
37. `includes/parser.php` — DOMDocument でのYahooパーサー実装

---

## 5. 各ファイルの実装仕様

### 5.1 フロントエンドページ

#### `index.php` (トップページ)
移行元: `src/app/page.tsx` + 複数コンポーネント

**セクション構成:**
1. HeroSection (デスクトップ/スマホ切り替え) — 動画背景、キャッチコピー、電話CTA
2. HeroVideoSection (スマホ用動画ファースト)
3. Stock (在庫一覧) — DBから取得、ソート機能付き
4. HeroTextSection (スマホ用テキスト)
5. EmpathySection (共感ゾーン) — 4つの課題提示
6. StrengthsSection (3つの強み) — カード3枚
7. TestimonialsSection (お客様の声) — 2件のテスティモニアル
8. FinalCTASection (最終CTA) — 電話、LINE、フォーム
9. StickyCTA — モバイル固定CTA + デスクトップフローティングCTA

**Framer Motion → CSS/JS 変換:**
- `motion.div` の `initial/animate` → CSS `@keyframes` + `IntersectionObserver`
- `animate-pulse` → CSS `animation: pulse`
- `animate-bounce` → CSS `animation: bounce`

#### `stock.php` (車両詳細)
移行元: `src/app/stock/[id]/page.tsx`

**パラメータ:** `?id=xxx`
**処理:**
1. `$_GET['id']` からDB検索
2. 404の場合はエラーページ表示
3. JSON文字列をパースして画像/スペック/装備品を展開
4. 価格計算: `round(($price + 100000) / 10000) * 10000`
5. タイトル: 「即決」以下を削除

### 5.2 管理画面

#### `admin/index.php` (ダッシュボード)
移行元: `src/app/admin/page.tsx`

**機能:**
- 車両一覧テーブル（管理番号、車名、価格、LP表示トグル、アクション）
- バナー管理（自動/手動モード切替、画像アップロード、プレビュー）
- 車両削除（確認ダイアログ → Ajax DELETE）
- 車両複製（確認ダイアログ → Ajax POST）
- LP表示トグル（チェックボックス → Ajax PUT）

#### `admin/new.php` (新規登録)
移行元: `src/app/admin/new/page.tsx` + `VehicleForm.tsx`

**機能:**
- Yahoo HTMLパーサー（テキストエリアにHTML貼り付け → 解析 → フォーム自動入力）
- 手動フォーム入力（タイトル、価格、画像URL、基本情報JSON、詳細JSON、装備品JSON、説明）
- 画像管理（URL追加/削除、サムネイルプレビュー）

#### `admin/edit.php` (編集)
移行元: `src/app/admin/edit/[id]/page.tsx`

**機能:** 新規登録と同じフォーム。`?id=xxx` で既存データを読み込み。

### 5.3 API

#### 車両CRUD API
| ファイル | メソッド | 機能 |
|---|---|---|
| `admin/api/vehicles.php` | GET | 全車両一覧取得（created_at DESC） |
| `admin/api/vehicles.php` | POST | 新規車両登録（管理番号自動生成） |
| `admin/api/vehicle.php?id=xxx` | GET | 車両詳細取得 |
| `admin/api/vehicle.php?id=xxx` | PUT | 車両更新 |
| `admin/api/vehicle.php?id=xxx` | DELETE | 車両削除 |

#### バナーAPI
| ファイル | メソッド | 機能 |
|---|---|---|
| `admin/api/banner.php` | GET | アクティブバナー取得（autoモード時は最新車両画像を返す） |
| `admin/api/banner.php` | POST | バナー設定更新（既存を非アクティブ化→新規作成） |

#### その他API
| ファイル | メソッド | 機能 |
|---|---|---|
| `admin/api/upload.php` | POST | 画像アップロード（10MB制限、MIME検証、ランダムファイル名） |
| `admin/api/parse.php` | POST | Yahoo HTML パース |
| `api/lp_vehicles.php` | GET | LP表示フラグONの車両一覧取得 |

### 5.4 セキュリティ

#### `.htaccess` (public_html)
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

#### `.htaccess` (admin)
```apache
AuthType Basic
AuthName "Admin Area"
AuthUserFile /home/USER/.htpasswd
Require valid-user
```

#### `.htaccess` (uploads)
```apache
php_flag engine off
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>
```

#### `.htaccess` (includes)
```apache
Deny from all
```

---

## 6. 技術変換マッピング

### 6.1 React/Next.js → PHP/HTML

| Next.js | PHP版 |
|---|---|
| `<Link href="/sales">` | `<a href="/sales.php">` |
| `<Image src="" fill>` | `<img src="" class="...">` |
| `useEffect(() => fetch(...))` | PHPサーバーサイドでDB取得 or JS `fetch()` |
| `useState` | JS変数 or DOM操作 |
| Server Components (async) | PHPでDB取得してHTMLに埋め込み |
| Client Components ("use client") | HTML + JavaScript |
| `params.id` | `$_GET['id']` |
| `notFound()` | `http_response_code(404); include 'includes/404.php';` |

### 6.2 Framer Motion → CSS/JS

| Framer Motion | CSS/JS 代替 |
|---|---|
| `initial={{ opacity: 0, y: 20 }}` | `.fade-up { opacity: 0; transform: translateY(20px); }` |
| `animate={{ opacity: 1, y: 0 }}` | `.fade-up.visible { opacity: 1; transform: translateY(0); }` |
| `transition={{ delay: 0.1 }}` | `transition: all 0.6s ease 0.1s;` |
| `animate-pulse` | `animation: pulse 2s infinite;` |
| `animate-bounce` | `animation: bounce 1s infinite;` |
| IntersectionObserver で要素の可視判定 | JS: `new IntersectionObserver(callback)` |

### 6.3 Cheerio → PHP DOMDocument

| Cheerio (Node.js) | PHP |
|---|---|
| `cheerio.load(html)` | `$dom = new DOMDocument(); @$dom->loadHTML($html);` |
| `$("script").each(...)` | `$xpath->query("//script")` |
| `$(el).attr("src")` | `$el->getAttribute("src")` |
| `$(el).text()` | `$el->textContent` |
| `$("h1").first()` | `$xpath->query("//h1")->item(0)` |

---

## 7. 既存データ移行

### 7.1 車両データ
現在の `data/db.json` からSQLに変換するスクリプトを用意。

### 7.2 画像ファイル
- Yahooオークション画像: 外部URL参照のため移行不要
- アップロード画像: `public/uploads/` → `public_html/uploads/` にFTPコピー
- サイト画像: `public/` → `public_html/images/` にFTPコピー
- 動画: `public/video/` → `public_html/video/` にFTPコピー

---

## 8. RSサーバー設定手順

### 8.1 事前準備
1. お名前.com Navi にログイン
2. コントロールパネルを開く
3. PHPバージョンを 8.1 以上に設定

### 8.2 MySQL作成
1. 「データベース」→「MySQL」→「追加」
2. DB名・ユーザー名・パスワードをメモ
3. phpMyAdmin で `sql/schema.sql` を実行

### 8.3 SSL設定
1. 「セキュリティ」→「SSL証明書」
2. 無料SSL（Let's Encrypt）を有効化

### 8.4 ファイルアップロード
1. FTPクライアント（FileZilla等）で接続
2. `public_html/` の内容をサーバーの `public_html/` にアップロード
3. `uploads/` ディレクトリのパーミッションを 755 に設定

### 8.5 設定ファイル編集
1. `includes/config.php` のDB接続情報をRSサーバーの値に変更
2. `.htpasswd` ファイルを作成してサーバーに配置

### 8.6 初回セットアップ
1. ブラウザで `https://ドメイン名/admin/login.php` にアクセス
2. 初回管理者アカウントの作成（setup スクリプトまたは手動SQL INSERT）

---

## 9. テスト項目

### フロントエンド
- [ ] トップページ表示（全セクション）
- [ ] レスポンシブ（スマホ/デスクトップ切り替え）
- [ ] モバイルメニュー開閉
- [ ] 在庫一覧ソート機能
- [ ] 車両詳細ページ表示
- [ ] 画像ギャラリー・ライトボックス
- [ ] 中古車販売ページ表示
- [ ] 買取ページ表示
- [ ] LP専用ページ表示
- [ ] Sticky CTA（モバイル/デスクトップ）
- [ ] アニメーション（フェードイン、スクロール）

### 管理画面
- [ ] ログイン/ログアウト
- [ ] 車両一覧表示
- [ ] 車両新規登録
- [ ] Yahoo HTMLパース → 自動入力
- [ ] 車両編集
- [ ] 車両削除
- [ ] 車両複製
- [ ] LP表示トグル
- [ ] バナー管理（自動/手動モード）
- [ ] 画像アップロード

### セキュリティ
- [ ] HTTPS リダイレクト
- [ ] Basic認証（admin）
- [ ] ログイン認証（セッション）
- [ ] SQLインジェクション対策
- [ ] XSS対策
- [ ] CSRFトークン
- [ ] アップロードファイル検証
- [ ] uploadsディレクトリのPHP実行禁止
- [ ] includesディレクトリのアクセス禁止

---

## 10. RSサーバーデプロイ手順（詳細）

### 10.1 事前準備: コントロールパネル設定

#### (A) PHPバージョン確認
1. お名前.com Navi → RSサーバーコントロールパネルにログイン
2. 「Web設定」→「PHPバージョン」→ **PHP 8.1以上** に設定

#### (B) MySQLデータベース作成
1. コントロールパネル →「データベース」→「MySQL」→「追加」
2. 以下を設定:
   - データベース名: `5r3_cars`（任意）
   - 文字コード: `utf8mb4`
3. DBユーザーを作成し、作成したDBへのアクセス権限を付与
4. 以下の情報をメモしておく:

| 項目 | 値の例 |
|---|---|
| ホスト名 | `mysql○○○.rs.onamae.ne.jp`（コンパネに表示される） |
| DB名 | 作成したデータベース名 |
| ユーザー名 | 作成したユーザー名 |
| パスワード | 設定したパスワード |

#### (C) SSL設定
1. 「セキュリティ」→「SSL証明書」→ 無料SSL（Let's Encrypt）を有効化
2. 反映まで数分〜数時間かかる場合あり

### 10.2 スキーマ実行（phpMyAdmin）

1. コントロールパネル →「phpMyAdmin」を開く
2. 左側で作成したデータベースを選択
3. 「SQL」タブをクリック
4. `sql/schema.sql` の内容をコピー＆ペーストして「実行」

> **注意:** `INSERT INTO admins` の行は実行しなくてOK（後のmigrateスクリプトが管理者を作成します）

### 10.3 config.php を本番用に編集

アップロード前に `public_html/includes/config.php` を以下のように変更:

```php
// ★ ローカル → 本番に変更
define('USE_SQLITE', false);  // true → false に変更

// ★ DB接続情報をRSサーバーの値に変更
define('DB_HOST', 'mysql○○○.rs.onamae.ne.jp');  // コンパネで確認したホスト名
define('DB_NAME', '実際のDB名');
define('DB_USER', '実際のユーザー名');
define('DB_PASS', '実際のパスワード');
define('DB_CHARSET', 'utf8mb4');
```

### 10.4 FTPアップロード

FTPクライアント（FileZilla等）でRSサーバーに接続。

#### FTP接続情報
| 項目 | 値 |
|---|---|
| ホスト | コンパネに記載のFTPサーバー名 |
| ユーザー | FTPアカウント |
| パスワード | FTPパスワード |
| ポート | 21（FTPS: 990） |

#### アップロード対象

```
ローカル (5r3_php/)               → サーバー
──────────────────────────────────────────────────────
public_html/ の中身すべて         → /public_html/（ドキュメントルート）
migrate.php                       → /migrate.php（ルート直下、一時的に）
sql/                              → /sql/（一時的に）
```

| ディレクトリ | 内容 | 備考 |
|---|---|---|
| `public_html/includes/` | PHP設定・共通処理 | config.phpは本番値に変更済のこと |
| `public_html/admin/` | 管理画面 | |
| `public_html/api/` | 公開APIエンドポイント | |
| `public_html/css/` | スタイルシート | |
| `public_html/js/` | JavaScript | |
| `public_html/images/` | サイト画像（ロゴ、アイコン等） | |
| `public_html/video/` | 動画ファイル（3ファイル） | サイズが大きいため後回しでもOK |
| `public_html/uploads/` | バナー画像 | |
| `public_html/*.php` | 各ページ（index, stock, sales, purchase, lp） | |

#### アップロードしないもの

| ファイル | 理由 |
|---|---|
| `public_html/local.sqlite` | ローカル開発用SQLiteファイル |
| `setup_local.php` | ローカル開発用セットアップスクリプト |
| `BUILD_GUIDE.md` | 構築手順書（サーバーに不要） |

### 10.5 パーミッション設定

FTPクライアントで以下のパーミッションを設定:

```
/public_html/uploads/   → 707 または 777（画像アップロード用に書き込み可能に）
```

### 10.6 データ移行

#### 方法A: サーバー上でmigrateスクリプトを実行（推奨）

1. Next.jsの `data/db.json` をサーバーにアップロード（例: `/data/db.json`）
2. `migrate.php` 内の `$JSON_PATH` をサーバー上のパスに変更:
   ```php
   $JSON_PATH = dirname(__FILE__) . '/data/db.json';
   ```
3. ブラウザで `https://ドメイン/migrate.php` にアクセス
4. 「移行完了」メッセージを確認
5. **完了後、以下を必ず削除:**
   - `/migrate.php`
   - `/data/db.json`
   - `/sql/`

#### 方法B: phpMyAdminから手動インポート

1. ローカルでmigrateスクリプトを実行し、MySQLダンプを作成
2. phpMyAdminの「インポート」でSQLファイルを実行
3. 管理者アカウントは手動で作成:
   ```sql
   INSERT INTO admins (username, password_hash)
   VALUES ('admin', '$2y$10$...');  -- password_hash() で生成したハッシュ
   ```

### 10.7 動作確認チェックリスト

| # | 確認項目 | URL |
|---|---|---|
| 1 | トップページ表示 | `https://ドメイン/` |
| 2 | 在庫一覧（ソート機能） | `https://ドメイン/` の在庫セクション |
| 3 | 車両詳細ページ | `https://ドメイン/stock.php?id=（任意のID）` |
| 4 | 中古車販売ページ | `https://ドメイン/sales.php` |
| 5 | 買取ページ | `https://ドメイン/purchase.php` |
| 6 | LPページ | `https://ドメイン/lp.php` |
| 7 | 管理画面ログイン | `https://ドメイン/admin/login.php` |
| 8 | 車両の新規追加・編集・削除 | 管理画面から操作 |
| 9 | バナー管理 | 管理画面から操作 |
| 10 | モバイル表示 | スマホまたはDevToolsで確認 |

### 10.8 デプロイ後のセキュリティ対応（必須）

1. **管理者パスワードを変更** — `admin123` → 強固なパスワードに変更
2. **一時ファイルの削除** — `migrate.php`、`sql/`、`db.json`（アップロードした場合）
3. **`.htpasswd` の設置**（Basic認証を使う場合）:
   ```bash
   htpasswd -c /home/ユーザー名/.htpasswd admin
   ```
   `admin/.htaccess` の `AuthUserFile` パスをサーバーの絶対パスに修正
4. **PHPエラー表示をOFFに** — 本番環境では `display_errors = Off` を確認

### 10.9 トラブルシューティング

| 症状 | 原因・対処法 |
|---|---|
| 500 Internal Server Error | `.htaccess` の記述エラー。RSサーバーで未対応のディレクティブがないか確認 |
| DB接続エラー | `config.php` のホスト名・DB名・ユーザー名・パスワードを再確認 |
| ページが真っ白 | PHPバージョンが古い可能性。コンパネでPHP 8.1以上に設定 |
| 画像アップロードできない | `uploads/` のパーミッションを707/777に設定 |
| CSSが効かない | ブラウザキャッシュをクリア。パスが正しいか確認 |
| 車両画像が表示されない | 外部URL（Yahoo）の画像リンク切れの可能性。管理画面で画像URLを確認 |
| ログインできない | migrateスクリプトで管理者が正しく作成されたか確認。phpMyAdminで`admins`テーブルを確認 |
