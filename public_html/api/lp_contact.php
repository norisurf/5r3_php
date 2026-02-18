<?php
declare(strict_types=1);
/**
 * 問い合わせフォーム処理API
 * POST: フォーム送信 → メール送信
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// CSRF検証
initSession();
verifyCsrf();

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$vehicleType = trim($_POST['car_type'] ?? $_POST['vehicleType'] ?? '');

// バリデーション
if (empty($name)) {
    jsonResponse(['error' => 'お名前を入力してください'], 400);
}
if (empty($phone) && empty($email)) {
    jsonResponse(['error' => '電話番号またはメールアドレスを入力してください'], 400);
}

// メールアドレスのバリデーション（入力された場合）
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['error' => '有効なメールアドレスを入力してください'], 400);
}

// ヘッダインジェクション対策：改行文字を除去
$name = str_replace(["\r", "\n"], '', $name);
$phone = str_replace(["\r", "\n"], '', $phone);
$email = str_replace(["\r", "\n"], '', $email);

// mb_send_mail用のエンコーディング設定（文字化け防止）
mb_language('Japanese');
mb_internal_encoding('UTF-8');

// メール送信
$to = SITE_EMAIL;
$subject = '【5R3 CARS】お問い合わせ';
$body = "お問い合わせがありました。\nお客様へ連絡をお願いいたします。\n\n";
$body .= "お名前: {$name}\n";
$body .= "電話番号: {$phone}\n";
$body .= "メール: {$email}\n";
if (!empty($vehicleType)) {
    $body .= "希望車種: {$vehicleType}\n";
}
if (!empty($message)) {
    $body .= "メッセージ:\n{$message}\n";
}
$body .= "\n送信日時: " . date('Y-m-d H:i:s') . "\n";

$fromDomain = parse_url('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), PHP_URL_HOST) ?: 'localhost';
$fromDomain = preg_replace('/[^a-zA-Z0-9.\-]/', '', $fromDomain);
$headers = "From: noreply@{$fromDomain}\r\n";
if (!empty($email)) {
    $headers .= "Reply-To: {$email}\r\n";
}

$sent = @mb_send_mail($to, $subject, $body, $headers);

if ($sent) {
    // フォーム送信後、トップページにリダイレクト
    header('Location: /?sent=1#contact');
    exit;
} else {
    // メール送信失敗でもユーザーには成功として見せる（ログに記録）
    error_log("Contact form mail failed: name={$name}, phone={$phone}, email={$email}");
    header('Location: /?sent=1#contact');
    exit;
}
