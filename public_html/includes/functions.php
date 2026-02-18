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
