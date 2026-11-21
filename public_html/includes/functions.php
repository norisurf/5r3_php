<?php
declare(strict_types=1);
/**
 * 共通ヘルパー関数
 */

/**
 * HTMLエスケープ
 */
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * JSON文字列を安全にデコード
 */
function jsonDecode(string $str, mixed $default = []): mixed {
    if (empty($str)) return $default;
    $decoded = json_decode($str, true);
    return $decoded !== null ? $decoded : $default;
}

/**
 * 値がJSON文字列ならそのまま、配列/オブジェクトならJSON化
 */
function ensureJsonString(mixed $val): string {
    if (is_string($val)) {
        // 有効なJSONかチェック
        json_decode($val);
        if (json_last_error() === JSON_ERROR_NONE) return $val;
        return json_encode($val, JSON_UNESCAPED_UNICODE);
    }
    return json_encode($val ?? [], JSON_UNESCAPED_UNICODE);
}

/**
 * 表示価格を計算 (+100,000円、1万円単位で四捨五入)
 */
function displayPrice(int $price): int {
    return (int)(round(($price + 100000) / 10000) * 10000);
}

/**
 * 表示価格を万円で取得
 */
function displayPriceMan(int $price): int {
    return (int)round(($price + 100000) / 10000);
}

/**
 * タイトルから「即決」以下を削除
 */
function cleanTitle(string $title): string {
    $pos = mb_strpos($title, '即決');
    return $pos !== false ? trim(mb_substr($title, 0, $pos)) : $title;
}

/**
 * 外部画像として取得してよいURLか（Yahoo!オークションの画像サーバーのみ許可）
 * 任意URLの取得（SSRF）やローカルファイルの読み出しを防ぐ。
 */
function isAllowedRemoteImageUrl(string $url): bool {
    $parts = parse_url($url);
    if (!is_array($parts) || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
        return false;
    }
    $host = strtolower($parts['host'] ?? '');
    return (bool)preg_match('/(^|\.)(yimg\.jp|yahoo\.co\.jp)$/', $host);
}

/**
 * 車両タイトルがキャラバン（NV350 / E25 / E26 等）かどうか
 */
function isCaravan(string $title): bool {
    return (bool)preg_match('/キャラバン|NV350|CARAVAN/iu', $title);
}

/**
 * メーカー名と車種名パターンの対応表（SEO用）
 */
function makerPatterns(): array {
    return [
        '日産'     => '/日産|ニッサン|NV350|NV200|キャラバン|バネット|クリッパー|セレナ|エルグランド/u',
        'トヨタ'   => '/トヨタ|ハイエース|レジアスエース|ライトエース|タウンエース|プロボックス|ノア|ヴォクシー|アルファード|ヴェルファイア/u',
        'マツダ'   => '/マツダ|ボンゴ/u',
        'ホンダ'   => '/ホンダ|N-VAN|N-WGN|N-BOX|アクティ|エリシオン|ステップワゴン|オデッセイ/u',
        'スズキ'   => '/スズキ|エブリイ|エブリィ|キャリイ/u',
        'ダイハツ' => '/ダイハツ|ハイゼット|アトレー/u',
        'いすゞ'   => '/いすゞ|コモ/u',
        '三菱'     => '/三菱|ミニキャブ|デリカ/u',
    ];
}

/**
 * 車両タイトルからメーカー名を推定（判定できなければ null）
 */
function detectMaker(string $title): ?string {
    foreach (makerPatterns() as $maker => $pattern) {
        if (preg_match($pattern, $title)) {
            return $maker;
        }
    }
    return null;
}

/**
 * 車種名の直前にメーカー名を挿入（既に含まれていれば何もしない）
 * 例: 「H25 検8/12 NV350キャラバンDX」→「H25 検8/12 日産 NV350キャラバンDX」
 */
function titleWithMaker(string $title): string {
    $maker = detectMaker($title);
    if ($maker === null || mb_strpos($title, $maker) !== false) {
        return $title;
    }
    if (!preg_match(makerPatterns()[$maker], $title, $m, PREG_OFFSET_CAPTURE)) {
        return $maker . ' ' . $title;
    }
    $pos = $m[0][1]; // バイト位置
    return rtrim(substr($title, 0, $pos)) . ($pos > 0 ? ' ' : '') . $maker . ' ' . substr($title, $pos);
}

/**
 * 商品説明から最後の「■」以下を削除
 */
function cleanDescription(string $desc): string {
    $pos = mb_strrpos($desc, '■');
    return $pos !== false ? trim(mb_substr($desc, 0, $pos)) : $desc;
}

/**
 * APIレスポンス (JSON)
 */
function jsonResponse(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * リクエストのJSONボディを取得
 */
function getJsonBody(): array {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}

/**
 * リクエストメソッドを取得 (_METHODオーバーライド対応)
 */
function getRequestMethod(): string {
    if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
        return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
    }
    if (isset($_POST['_method'])) {
        return strtoupper($_POST['_method']);
    }
    return $_SERVER['REQUEST_METHOD'];
}
