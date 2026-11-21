<?php
declare(strict_types=1);
/**
 * 認証関連関数
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * HTTPS 接続かどうか
 * RSサーバーは nginx（SSL終端）→ Apache 構成のため $_SERVER['HTTPS'] は常に off。
 * nginx が付与する X-Forwarded-Proto も確認する。
 */
function isHttpsRequest(): bool {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? 80) == 443)
        || strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

/**
 * セッション開始
 */
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure'  => isHttpsRequest(),
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

/** ログイン失敗の許容回数と、ロックする時間（秒） */
const LOGIN_MAX_FAILURES = 5;
const LOGIN_LOCK_SECONDS = 900; // 15分

/**
 * アクセス元IP
 * リバースプロキシ（nginx）経由の場合、nginx が末尾に追加した X-Forwarded-For の値を使う
 * （先頭側はクライアントが偽装できるため使わない）。
 */
function clientIp(): string {
    $remote = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    $isProxy = $remote === '' || !filter_var($remote, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    $xff = (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
    if ($isProxy && $xff !== '') {
        $parts = array_map('trim', explode(',', $xff));
        $last = end($parts);
        if (filter_var($last, FILTER_VALIDATE_IP)) {
            return $last;
        }
    }
    return $remote !== '' ? $remote : 'unknown';
}

/**
 * ログイン失敗記録ファイルのパス（IPごと）
 */
function loginThrottleFile(): string {
    $dir = sys_get_temp_dir() . '/5r3_login_throttle';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    return $dir . '/' . hash('sha256', clientIp()) . '.json';
}

/**
 * ロック中なら残り秒数、ロックされていなければ 0
 */
function loginLockRemaining(): int {
    $file = loginThrottleFile();
    if (!is_file($file)) {
        return 0;
    }
    $data = json_decode((string)@file_get_contents($file), true) ?: [];
    $first = (int)($data['first'] ?? 0);
    $count = (int)($data['count'] ?? 0);
    $elapsed = time() - $first;
    if ($elapsed >= LOGIN_LOCK_SECONDS) {
        @unlink($file);
        return 0;
    }
    return $count >= LOGIN_MAX_FAILURES ? LOGIN_LOCK_SECONDS - $elapsed : 0;
}

/**
 * ログイン失敗を記録
 */
function recordLoginFailure(): void {
    $file = loginThrottleFile();
    $data = is_file($file) ? (json_decode((string)@file_get_contents($file), true) ?: []) : [];
    if (time() - (int)($data['first'] ?? 0) >= LOGIN_LOCK_SECONDS) {
        $data = ['first' => time(), 'count' => 0];
    }
    $data['count'] = (int)$data['count'] + 1;
    @file_put_contents($file, json_encode($data), LOCK_EX);
}

/**
 * ログイン成功時に失敗記録を消去
 */
function clearLoginFailures(): void {
    @unlink(loginThrottleFile());
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
