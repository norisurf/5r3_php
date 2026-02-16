<?php
/**
 * 管理API: 画像アップロード
 * POST → ファイルアップロード
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/csrf.php';

initSession();
requireApiLogin();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

verifyCsrfApi();

// ファイルチェック
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE   => 'ファイルサイズが大きすぎます（サーバー制限）',
        UPLOAD_ERR_FORM_SIZE  => 'ファイルサイズが大きすぎます',
        UPLOAD_ERR_PARTIAL    => 'ファイルが部分的にしかアップロードされませんでした',
        UPLOAD_ERR_NO_FILE    => 'ファイルが選択されていません',
        UPLOAD_ERR_NO_TMP_DIR => 'サーバーの一時ディレクトリがありません',
        UPLOAD_ERR_CANT_WRITE => 'サーバーのディスクに書き込めませんでした',
    ];
    $code = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
    jsonResponse(['error' => $errorMessages[$code] ?? 'アップロードエラー'], 400);
}

$file = $_FILES['file'];

// サイズチェック
if ($file['size'] > MAX_UPLOAD_SIZE) {
    jsonResponse(['error' => 'ファイルサイズは10MB以下にしてください'], 400);
}

// MIMEタイプチェック
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes, true)) {
    jsonResponse(['error' => 'JPEG, PNG, WebP, GIF形式のみ対応しています'], 400);
}

// アップロードディレクトリ作成
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// ユニークなファイル名を生成
$timestamp = time();
$ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
$ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext); // サニタイズ
$fileName = 'banner_' . $timestamp . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$filePath = UPLOAD_DIR . '/' . $fileName;

// ファイルを移動
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    jsonResponse(['error' => 'アップロードに失敗しました'], 500);
}

// 公開URLを返す
$publicUrl = UPLOAD_URL . '/' . $fileName;
jsonResponse(['url' => $publicUrl, 'fileName' => $fileName]);
