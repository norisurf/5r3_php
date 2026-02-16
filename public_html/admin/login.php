<?php
/**
 * 管理画面ログイン
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

initSession();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (attemptLogin($username, $password)) {
        header('Location: /admin/');
        exit;
    } else {
        $error = 'ユーザー名またはパスワードが正しくありません。';
    }
}

// すでにログイン済みならリダイレクト
if (isset($_SESSION['admin_id'])) {
    header('Location: /admin/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - <?= SITE_NAME ?> 管理画面</title>
    <link rel="icon" href="/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="antialiased bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <img src="/images/5r3_rogo.png" alt="5R3" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-[#003366]">管理画面ログイン</h1>
                <p class="text-sm text-gray-500 mt-2"><?= SITE_NAME ?> 在庫管理システム</p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium">
                <?= h($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <?= csrfField() ?>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ユーザー名</label>
                    <input type="text" name="username" required autofocus class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#003366] focus:outline-none focus:ring-2 focus:ring-[#003366]/20" value="<?= h($_POST['username'] ?? '') ?>">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">パスワード</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#003366] focus:outline-none focus:ring-2 focus:ring-[#003366]/20">
                </div>
                <button type="submit" class="w-full bg-[#003366] text-white py-3 rounded-xl font-bold hover:bg-[#002244] transition-colors">
                    ログイン
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="/" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">サイトに戻る</a>
            </div>
        </div>
    </div>
</body>
</html>
