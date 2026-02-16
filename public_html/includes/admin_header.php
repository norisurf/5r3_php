<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '管理画面 - ' . SITE_NAME) ?></title>
    <link rel="icon" href="/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
    <meta name="csrf-token" content="<?= csrfToken() ?>">
</head>
<body class="antialiased bg-gray-50">
    <!-- Admin Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#003366] text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="/admin/" class="flex items-center space-x-3">
                    <img src="/images/5r3_rogo.png" alt="5R3" class="h-8 w-auto object-contain brightness-0 invert">
                    <span class="text-lg font-bold">管理画面</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="/" target="_blank" class="text-sm text-white/70 hover:text-white transition-colors">サイトを表示</a>
                    <a href="/admin/logout.php" class="text-sm bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition-colors">ログアウト</a>
                </div>
            </div>
        </div>
    </header>
    <main class="pt-24 pb-20">
