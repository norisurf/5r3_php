<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KFWPVCJJF6"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-KFWPVCJJF6');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_TITLE) ?></title>
    <meta name="description" content="<?= h($pageDescription ?? SITE_DESCRIPTION) ?>">
    <link rel="icon" href="/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="antialiased">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="/" class="relative z-50 flex items-center space-x-2 h-8 md:h-12 w-auto group">
                    <img src="/images/5r3_rogo.png" alt="5R3 CARS Logo" class="h-full w-auto object-contain">
                    <span class="text-2xl font-black italic tracking-tighter text-slate-900 group-hover:text-slate-700 transition-colors">5R3</span>
                </a>

                <!-- Right Section (Desktop) -->
                <div class="hidden md:flex items-center ml-auto">
                    <nav class="hidden lg:flex items-center space-x-8 text-base font-bold mr-10">
                        <a href="/sales.php" class="text-slate-800 hover:text-slate-500 transition-colors tracking-tighter">中古車販売</a>
                        <a href="/purchase.php" class="text-slate-800 hover:text-slate-500 transition-colors tracking-tighter">買取</a>
                        <a href="<?= LINK_LINE ?>" class="hover:opacity-80 transition-opacity">
                            <img src="/images/line-icon.png" alt="LINE" class="w-8 h-8 object-contain rounded-lg">
                        </a>
                        <a href="<?= LINK_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-opacity">
                            <img src="/images/insta-icon.png" alt="Instagram" class="w-8 h-8 object-contain rounded-lg">
                        </a>
                        <a href="<?= LINK_X ?>" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-opacity">
                            <img src="/images/x-icon.png" alt="X" class="w-8 h-8 object-contain rounded-lg">
                        </a>
                        <a href="<?= LINK_TIKTOK ?>" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-opacity">
                            <img src="/images/tiktok-4.png" alt="TikTok" class="w-8 h-8 object-contain rounded-lg">
                        </a>
                        <a href="<?= LINK_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" class="hover:opacity-80 transition-opacity">
                            <img src="/images/Facebook.png" alt="Facebook" class="w-8 h-8 object-contain rounded-lg">
                        </a>
                        <a href="mailto:<?= SITE_EMAIL ?>" class="hover:opacity-80 transition-opacity">
                            <img src="/images/mail.png" alt="メール" class="w-8 h-8 object-contain rounded-lg">
                        </a>
                    </nav>
                    <div class="flex items-center space-x-4">
                        <div class="flex flex-col items-end">
                            <div class="flex items-center text-slate-800 font-bold text-lg">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span><?= SITE_PHONE ?></span>
                            </div>
                            <div class="flex items-center text-[10px] text-zinc-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><?= SITE_HOURS ?> (即納相談受付中)</span>
                            </div>
                        </div>
                        <a href="#contact" class="bg-slate-900 text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-slate-800 transition-all flex items-center shadow-lg shadow-black/10">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            即納相談する
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button id="menu-btn" class="md:hidden relative z-50 p-2 text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100 shrink-0">
            <span class="text-xl font-bold text-slate-900">MENU</span>
            <button id="menu-close" class="p-2 text-slate-900 bg-slate-50 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 flex flex-col space-y-8">
            <nav class="flex flex-col space-y-6 text-xl font-black text-slate-900">
                <a href="/sales.php" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">中古車販売<span class="text-slate-300">→</span></a>
                <a href="/purchase.php" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">買取<span class="text-slate-300">→</span></a>
                <a href="<?= LINK_LINE ?>" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">
                    <span class="flex items-center"><img src="/images/line-icon.png" alt="LINE" class="w-10 h-10 mr-3 rounded-xl">LINEで相談</span><span class="text-slate-300">→</span>
                </a>
                <a href="<?= LINK_INSTAGRAM ?>" target="_blank" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">
                    <span class="flex items-center"><img src="/images/insta-icon.png" alt="Instagram" class="w-10 h-10 mr-3 rounded-xl">Instagram</span><span class="text-slate-300">→</span>
                </a>
                <a href="<?= LINK_X ?>" target="_blank" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">
                    <span class="flex items-center"><img src="/images/x-icon.png" alt="X" class="w-10 h-10 mr-3 rounded-xl">X (Twitter)</span><span class="text-slate-300">→</span>
                </a>
                <a href="<?= LINK_TIKTOK ?>" target="_blank" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">
                    <span class="flex items-center"><img src="/images/tiktok-4.png" alt="TikTok" class="w-10 h-10 mr-3 rounded-xl">TikTok</span><span class="text-slate-300">→</span>
                </a>
                <a href="mailto:<?= SITE_EMAIL ?>" class="hover:text-slate-600 border-b border-slate-100 pb-4 flex justify-between items-center group tracking-tighter">
                    <span class="flex items-center"><img src="/images/mail.png" alt="メール" class="w-10 h-10 mr-3 rounded-xl">メールで相談</span><span class="text-slate-300">→</span>
                </a>
            </nav>
            <div class="mt-auto pb-8 flex flex-col space-y-4">
                <div class="flex flex-col items-center bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <div class="flex items-center text-slate-900 font-bold text-2xl mb-2">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span><?= SITE_PHONE ?></span>
                    </div>
                    <span class="text-sm text-slate-500 font-medium"><?= SITE_HOURS ?> (即納相談受付中)</span>
                </div>
                <a href="#contact" class="bg-slate-900 text-white px-6 py-5 rounded-full text-center text-lg font-bold shadow-xl shadow-black/10 flex items-center justify-center transition-all active:scale-95">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    即納相談フォームへ
                </a>
            </div>
        </div>
    </div>

    <main class="min-h-screen pt-16 md:pt-20">
