<?php
declare(strict_types=1);
/**
 * 認証関連関数
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * セッション開始
 */
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
        session_start([
            'cookie_httponly' => true,
            'cookie_secure'  => $isHttps,
            'cookie_samesite' => 'Strict',
            'gc_maxlifetime' => SESSION_LIFETIME,
        ]);
    }
}

/**
 * ログインチェック（admin配下の全ページで呼ぶ）
 */
function requireLogin(): void {
    initSession();
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * ログイン試行
 */
function attemptLogin(string $username, string $password): bool {
    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        initSession();
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        return true;
    }
    return false;
}

/**
 * ログアウト
 */
function logout(): void {
    initSession();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}

/**
 * API用ログインチェック（JSONレスポンス）
 */
function requireApiLogin(): void {
    initSession();
    if (empty($_SESSION['admin_logged_in'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => '認証が必要です']);
        exit;
    }
}
