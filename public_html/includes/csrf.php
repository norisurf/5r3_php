<?php
/**
 * CSRFトークン管理
 */

/**
 * CSRFトークンを生成・取得
 */
function csrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRFトークンの隠しフィールドを出力
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/**
 * CSRFトークンを検証
 */
function verifyCsrf(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        die('不正なリクエストです');
    }
}

/**
 * API用CSRF検証（JSONレスポンス）
 */
function verifyCsrfApi(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => '不正なリクエストです']);
        exit;
    }
}
