<?php
/**
 * 問い合わせフォーム処理API
 * POST: フォーム送信 → メール送信
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

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

$headers = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
$headers .= "Reply-To: " . ($email ?: $to) . "\r\n";

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
