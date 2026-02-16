# 5R3 CARS デプロイガイド

## 概要

GitHub にコードを push すると、GitHub Actions が自動的に本番サーバー（お名前.com RSサーバー）へ FTP デプロイします。

```
[ローカル PC] → git push → [GitHub] → GitHub Actions → FTP → [本番サーバー]
```

---

## 1. デプロイの流れ

### Step 1: ローカルで変更を確認

```bash
cd D:\5r3\5r3_php

# ローカルサーバーで動作確認
cd public_html && php -S localhost:8080
# ブラウザで http://localhost:8080 と http://localhost:8080/admin/ を確認
```

### Step 2: 変更をコミット

```bash
cd D:\5r3\5r3_php

# 変更内容を確認
git status
git diff

# ファイルをステージング（変更したファイルを指定）
git add public_html/admin/index.php public_html/js/admin.js

# コミット
git commit -m "管理画面の改善: ステータスフィルター追加"
```

### Step 3: push（= 自動デプロイ）

```bash
git push origin main
```

これだけで本番サーバーに自動デプロイされます。

### Step 4: デプロイ状況を確認

GitHub リポジトリの Actions タブで進捗を確認:
https://github.com/norisurf/5r3_php/actions

---

## 2. push されるファイル一覧

### デプロイ対象（public_html/ 以下すべて）

```
public_html/
├── index.php              ← トップページ
├── stock.php              ← 車両詳細ページ
├── lp.php                 ← ランディングページ
├── sales.php              ← 販売実績ページ
├── purchase.php           ← 買取ページ
├── sitemap.php            ← サイトマップ
├── css/
│   └── style.css          ← スタイルシート
├── js/
│   ├── admin.js           ← 管理画面JS
│   ├── main.js            ← メインJS
│   ├── sort.js            ← ソート機能JS
│   └── gallery.js         ← ギャラリーJS
├── admin/
│   ├── index.php          ← 管理画面ダッシュボード
│   ├── edit.php           ← 車両編集
│   ├── new.php            ← 車両新規登録
│   ├── login.php          ← ログイン
│   ├── logout.php         ← ログアウト
│   ├── .htaccess          ← アクセス制御
│   └── api/
│       ├── vehicle.php    ← 車両API（個別）
│       ├── vehicles.php   ← 車両API（一覧）
│       ├── banner.php     ← バナーAPI
│       ├── upload.php     ← 画像アップロードAPI
│       └── parse.php      ← HTMLパースAPI
├── api/
│   ├── lp_vehicles.php    ← LP用車両API
│   └── lp_contact.php     ← お問い合わせAPI
├── includes/
│   ├── db.php             ← DB接続
│   ├── functions.php      ← 共通関数
│   ├── auth.php           ← 認証
│   ├── csrf.php           ← CSRF保護
│   ├── header.php         ← 公開ヘッダー
│   ├── footer.php         ← 公開フッター
│   ├── admin_header.php   ← 管理ヘッダー
│   ├── admin_footer.php   ← 管理フッター
│   ├── parser.php         ← HTMLパーサー
│   └── .htaccess          ← アクセス制御
└── images/                ← 静的画像
```

### デプロイ除外ファイル（.gitignore + deploy.yml の exclude）

| ファイル | 理由 |
|---------|------|
| `includes/config.php` | DB接続情報等の機密情報。本番と開発で異なる |
| `local.sqlite` | ローカル開発用のSQLiteデータベース |
| `uploads/*` | ユーザーがアップロードした画像。サーバー上にのみ存在 |
| `video/*` | 動画ファイル。サイズが大きいため |
| `blog/*` | WordPress。別管理のため |
| `test.php` | テスト用ファイル |
| `.ftp-deploy-sync-state.json` | FTPデプロイの状態管理ファイル |

---

## 3. 本番環境のデータの扱い

### コード（PHPファイル等）とデータ（DB）は別管理

```
┌─────────────────────────────────────────────────┐
│  GitHub / git push で管理するもの                 │
│  → PHPコード、JS、CSS、HTML                      │
│  → デプロイ時に本番サーバーのファイルが上書きされる  │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  本番サーバー上でのみ管理するもの                   │
│  → MySQL データベース（車両データ、バナー、管理者）  │
│  → config.php（DB接続情報）                       │
│  → uploads/（アップロード画像）                    │
│  → video/（動画ファイル）                         │
│  → blog/（WordPress）                            │
└─────────────────────────────────────────────────┘
```

### 重要なポイント

- **git push してもデータベースは一切変更されません**
- 車両データ、バナー設定、管理者アカウントはすべて MySQL に保存されているため、デプロイしても影響なし
- `config.php` はデプロイ対象外なので、本番のDB接続情報は安全
- `uploads/` もデプロイ対象外なので、本番のアップロード画像は安全

### データベースを変更する場合（テーブル構造変更時のみ）

テーブル構造を変更する場合は、push の **前に** phpMyAdmin で SQL を実行してください。

```
1. phpMyAdmin で ALTER TABLE 等の SQL を実行
2. その後で git push してコードをデプロイ
```

SQL ファイルは `sql/` ディレクトリに保管:
- `sql/schema.sql` - テーブル定義
- `sql/migrate_add_soft_delete.sql` - ソフトデリート追加マイグレーション

---

## 4. ローカルと本番を同期する方法

### ケースA: コード（PHP/JS/CSS）の同期

ローカル → 本番:
```bash
git add . && git commit -m "変更内容" && git push origin main
# GitHub Actions が自動でFTPデプロイ
```

本番 → ローカル:
```bash
git pull origin main
# 他のPCで push した変更を取り込む
```

### ケースB: 本番のデータ（車両情報）をローカルに同期

ローカルは SQLite、本番は MySQL で別々のデータベースです。
データを同期するには以下の方法があります:

**方法1: 管理画面から手動で同じ操作をする**
- ローカルの http://localhost:8080/admin/ で同じ車両を登録

**方法2: エクスポート/インポートスクリプトを使う**
- 本番サーバーに一時的にエクスポートPHPを設置
- JSONでデータをダウンロード
- ローカルのSQLiteにインポート
- （必要に応じて作成します）

### ケースC: 本番の config.php を変更する場合

`config.php` は git 管理外なので、FTP で直接編集する必要があります。

---

## 5. GitHub Secrets の設定（初回のみ）

GitHub Actions が FTP デプロイするために、以下の Secrets を設定する必要があります:

1. https://github.com/norisurf/5r3_php/settings/secrets/actions にアクセス
2. 以下の 3つの Secret を追加:

| Secret 名 | 値 |
|-----------|-----|
| `FTP_HOST` | お名前.com RSサーバーの FTP ホスト名 |
| `FTP_USER` | FTP ユーザー名 |
| `FTP_PASS` | FTP パスワード |

※ FTP 情報はお名前.com のコントロールパネルで確認できます。

---

## 6. よくある操作

### 管理画面を修正してデプロイ

```bash
# 1. ローカルで修正 & 確認
# 2. コミット & push
git add public_html/admin/index.php public_html/js/admin.js
git commit -m "管理画面: 検索機能追加"
git push origin main
```

### トップページを修正してデプロイ

```bash
git add public_html/index.php public_html/css/style.css
git commit -m "トップページ: ソート順を変更"
git push origin main
```

### 変更を元に戻す（push 前）

```bash
# 特定ファイルの変更を取り消し
git checkout -- public_html/admin/index.php

# すべての変更を取り消し
git checkout -- .
```

### 複数の PC で作業する場合

```bash
# 作業開始前に最新を取得
git pull origin main

# 作業完了後に push
git add . && git commit -m "変更内容" && git push origin main
```

---

## 7. 注意事項

1. **push する前にローカルで必ず動作確認**してください
2. **config.php は絶対にコミットしない**でください（DB パスワードが含まれます）
3. **テーブル構造の変更は push の前に** phpMyAdmin で実行してください
4. **blog/ ディレクトリ**は WordPress なので、git/デプロイとは別管理です
5. **uploads/ や video/** の画像・動画は FTP で直接管理してください
