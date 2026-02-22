<?php
declare(strict_types=1);
/**
 * データベース接続 (PDO シングルトン)
 */
require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // ローカル開発: SQLiteファイルが存在する場合はSQLiteを使用
        $sqliteFile = dirname(__DIR__) . '/local.sqlite';
        if (defined('USE_SQLITE') && USE_SQLITE && file_exists($sqliteFile)) {
            $pdo = new PDO('sqlite:' . $sqliteFile, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } else {
            $host = (DB_HOST === 'localhost') ? '127.0.0.1' : DB_HOST;
            $dsn = 'mysql:host=' . $host . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
    }
    return $pdo;
}

/**
 * ランダムID生成 (英数字)
 */
function generateId(): string {
    return bin2hex(random_bytes(12));
}

/**
 * 管理番号生成 (CAR-YYYYMMDD-001)
 */
function generateManageNumber(PDO $pdo): string {
    $date = date('Ymd');
    $seq = 1;
    while (true) {
        $num = 'CAR-' . $date . '-' . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM vehicles WHERE manage_number = ?');
        $stmt->execute([$num]);
        if ((int)$stmt->fetchColumn() === 0) {
            return $num;
        }
        $seq++;
    }
}
