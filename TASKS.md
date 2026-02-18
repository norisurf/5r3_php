# 5R3 PHP - コード改善タスク

`.agents/skills/php-best-practices` に基づく監査結果

---

## CRITICAL（即時修正）

### C-1: DBパスワードのハードコード除去
- **ファイル:** `public_html/includes/config.php`
- **内容:** 本番DBパスワードがソースコードに直書きされていた
- **対応:** `.env` ファイルに移行、`getenv()` で読み込み、`.gitignore` に追加
- [x] 完了

### C-2: メールヘッダインジェクション対策
- **ファイル:** `public_html/api/lp_contact.php`
- **内容:** `$_SERVER['HTTP_HOST']` と `$email` をバリデーションなしで `From` / `Reply-To` ヘッダに使用
- **対応:** Fromドメインをサニタイズ、emailを `filter_var(FILTER_VALIDATE_EMAIL)` で検証、改行文字を除去
- [x] 完了

### C-3: 問い合わせフォームのCSRF保護追加
- **ファイル:** `public_html/api/lp_contact.php`, `public_html/index.php`, `public_html/lp.php`
- **内容:** 公開フォームにCSRFトークン検証がなかった
- **対応:** フォームに `csrfField()` 追加、処理側に `initSession()` + `verifyCsrf()` 追加
- [x] 完了

### C-4: 全ファイルに `declare(strict_types=1)` 追加
- **ファイル:** 全PHPファイル（27ファイル）
- **内容:** strict_types 宣言がどのファイルにもなかった
- **対応:** `<?php` 直後に `declare(strict_types=1);` を追加（HTMLテンプレート3ファイルは対象外）
- [x] 完了

---

## HIGH（早めに修正）

### H-1: APIエラーレスポンスからPDOエラー詳細を削除
- **ファイル:** `admin/api/vehicles.php`, `vehicle.php`, `banner.php`, `parse.php`, `fetch_and_parse.php`
- **内容:** `$e->getMessage()` をレスポンスに含めておりDB構造等が漏洩する可能性があった
- **対応:** `details` キーを削除し、`error_log()` でサーバーログにのみ記録
- [x] 完了

### H-3: banner.php の `NOW()` をSQLite互換にする
- **ファイル:** `public_html/admin/api/banner.php`
- **内容:** `NOW()` はMySQL専用。SQLiteモードでエラーになる
- **対応:** PHP側で `date('Y-m-d H:i:s')` を生成してバインド
- [x] 完了

### H-5: 型宣言の不足している関数を修正
- **ファイル:** `public_html/includes/functions.php`
- **内容:** 一部の関数でパラメータ型・戻り値型が未宣言
- **対応:** `jsonDecode(): mixed`, `ensureJsonString(mixed): string`, `jsonResponse(mixed): never` に修正
- [x] 完了

---

## MEDIUM（改善推奨）

### M-1: `switch` を `match` 式に置き換え → スキップ
- **内容:** HTTPメソッド振り分けの `switch` は処理ブロックが大きく、`match` 変換は可読性低下のためスキップ
- [x] スキップ（現状維持が適切）

### M-2: 緩い比較を厳密比較に
- **ファイル:** `public_html/index.php`, `public_html/lp.php`
- **内容:** `$_GET['sent'] == '1'` → `$_GET['sent'] === '1'`
- [x] 完了

---

## 除外タスク

| タスク | 理由 |
|--------|------|
| H-2: TikTokリンクのURL修正 | ユーザー指示により除外 |
| H-4: 空の `LINK_FACEBOOK` リンクの非表示処理 | ユーザー指示により除外 |

---

## 対応不要（参考情報）

| ルール | 理由 |
|--------|------|
| PSR-4 Autoloading | 手続き型アプリのため現状では過剰。将来的にリファクタリングする場合に検討 |
| Constructor Promotion | クラスベースでないため該当なし |
| SOLID原則 | 手続き型のため直接適用困難。大規模化する場合に検討 |
| Enum活用 | 現状のコード規模では定数で十分 |

---

## 進捗サマリー

| 優先度 | 総数 | 完了 | スキップ |
|--------|------|------|----------|
| CRITICAL | 4 | 4 | 0 |
| HIGH | 3 | 3 | 0 |
| MEDIUM | 2 | 1 | 1 |
| **合計** | **9** | **8** | **1** |
