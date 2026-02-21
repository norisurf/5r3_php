<?php

declare(strict_types=1);

/**
 * トップページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

initSession();

$db = getDB();

// 車両一覧取得
$stmt = $db->query('SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY price DESC');
$vehicles = $stmt->fetchAll();

// バナー取得
$stmt = $db->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
$banner = $stmt->fetch();

$bannerImageUrl = '';
$bannerLinkUrl = '';

if ($banner) {
    if ($banner['mode'] === 'auto') {
        $stmt2 = $db->query("SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 1");
        $latestVehicle = $stmt2->fetch();
        if ($latestVehicle) {
            $imgs = jsonDecode($latestVehicle['images']);
            $bannerImageUrl = !empty($imgs) ? $imgs[0] : '';
            $bannerLinkUrl = '/stock.php?id=' . $latestVehicle['id'];
        }
    } else {
        $bannerImageUrl = $banner['image_url'];
        $bannerLinkUrl = $banner['link_url'];
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ================================================================ -->
<!-- デスクトップ ヒーロー -->
<!-- ================================================================ -->
<section class="relative pt-24 pb-20 lg:pt-32 lg:pb-32 overflow-hidden bg-white hidden lg:block">
    <div class="container relative mx-auto px-4 z-10">
        <div class="flex flex-row items-center gap-16">
            <div class="flex-1 text-left space-y-6">
                <!-- Copy -->
                <div class="inline-block bg-red-600 text-white text-lg md:text-xl font-bold px-4 py-2 rounded-lg mb-1 shadow-lg animate-pulse">
                    仕事用の車が今すぐ必要な方へ
                </div>
                <h1 class="text-7xl font-black tracking-tight leading-[1.1] text-slate-900">
                    最短<span class="text-red-600 text-8xl mx-2">即日</span>在庫案内<br>
                    できます
                </h1>

                <p class="text-3xl text-slate-700 font-bold">
                    ワンボックスカー・ミニバン多数在庫
                </p>

                <p class="text-slate-500 font-medium">
                    車が壊れても、仕事は止めさせません。今すぐお電話ください。
                </p>

                <!-- Button -->
                <div class="pt-6">
                    <a href="<?= SITE_PHONE_TEL ?>" class="w-full inline-flex flex-col items-center justify-center bg-red-600 text-white px-10 py-5 rounded-full shadow-2xl shadow-red-600/30 hover:bg-red-700 transition-all active:scale-95 group border-4 border-white ring-4 ring-red-100">
                        <div class="flex items-center text-4xl font-black">
                            <svg class="w-10 h-10 mr-3 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <?= SITE_PHONE ?>
                        </div>
                        <span class="text-base font-bold opacity-90 mt-1"><?= SITE_HOURS ?> ｜ 在庫確認だけOK</span>
                    </a>
                </div>
            </div>

            <!-- Video -->
            <div class="flex-1 w-full relative fade-right">
                <div class="relative rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white aspect-[4/3]">
                    <video src="/video/5r3_01.mp4" autoplay loop muted playsinline class="object-cover w-full h-full"></video>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-7 left-7 text-white text-left">
                        <p class="font-bold text-shadow-lg text-lg">現在の在庫数</p>
                        <p class="text-sm md:text-3xl font-black text-shadow-lg">20<span class="text-2xl">台以上</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- スマホ ヒーロー (動画) -->
<!-- ================================================================ -->
<section class="relative pt-18 pb-8 overflow-hidden bg-white lg:hidden">
    <div class="container relative mx-auto px-4 z-10">
        <div class="w-full relative fade-up-immediate">
            <div class="relative rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white aspect-[4/3]">
                <video src="/video/5r3_01.mp4" autoplay loop muted playsinline class="object-cover w-full h-full"></video>
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                <div class="absolute bottom-3 left-5 text-white text-left">
                    <p class="font-bold text-shadow-lg text-base">現在の在庫数</p>
                    <p class="text-lg font-black text-shadow-lg">20<span class="text-xl">台以上</span></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- 在庫車両一覧 -->
<!-- ================================================================ -->
<section id="stock" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <!-- Section Title -->
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="h-px w-12 bg-metallic"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Stock</span>
                <div class="h-px w-12 bg-metallic"></div>
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">在庫車両</h2>
            <p class="mt-4 text-slate-500 font-medium text-sm max-w-xl mx-auto">
                「即納可」ラベル付きの車両は、最短当日中の納車相談が可能です。
            </p>
        </div>

        <!-- Sort Controls -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-8">
            <div class="flex items-center text-sm text-slate-500 mr-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                </svg>
                並び替え:
            </div>
            <button onclick="sortVehicles('price_high')" data-sort="price_high" class="sort-btn active px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-900 text-white shadow-lg">価格が高い順</button>
            <button onclick="sortVehicles('price_low')" data-sort="price_low" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">価格が安い順</button>
            <button onclick="sortVehicles('year_new')" data-sort="year_new" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">年式が新しい順</button>
            <button onclick="sortVehicles('mileage_low')" data-sort="mileage_low" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">走行距離が少ない順</button>
            <button onclick="sortVehicles('newest')" data-sort="newest" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">更新順</button>
        </div>

        <!-- Vehicle Grid -->
        <div id="vehicle-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <?php if (empty($vehicles)): ?>
                <div class="col-span-full py-20 text-center text-slate-300">現在在庫している車両はありません。</div>
            <?php else: ?>
                <?php foreach ($vehicles as $v):
                    $imgs = jsonDecode($v['images']);
                    $imgSrc = !empty($imgs) ? $imgs[0] : '/images/placeholder.png';
                    $basicInfo = jsonDecode($v['basic_info']);
                    $dPrice = displayPriceMan($v['price']);
                    $cTitle = cleanTitle($v['title']);
                ?>
                    <?php
                    // 年式から数値を抽出（例: "令和5年(2023年)" → 2023, "2023年" → 2023, "R5" → 5）
                    $yearVal = 0;
                    $yearStr = $basicInfo['年式'] ?? '';
                    if (preg_match('/\((\d{4})年?\)/', $yearStr, $m)) {
                        $yearVal = (int)$m[1];
                    } elseif (preg_match('/(\d{4})/', $yearStr, $m)) {
                        $yearVal = (int)$m[1];
                    }
                    // 走行距離から数値を抽出（例: "5,000km" → 5000, "12.3万km" → 123000）
                    $mileageVal = 0;
                    $mileageStr = $basicInfo['走行距離'] ?? '';
                    if (preg_match('/([\d,.]+)\s*万/', $mileageStr, $m)) {
                        $mileageVal = (int)(floatval(str_replace(',', '', $m[1])) * 10000);
                    } elseif (preg_match('/([\d,]+)/', $mileageStr, $m)) {
                        $mileageVal = (int)str_replace(',', '', $m[1]);
                    }
                    ?>
                    <a href="/stock.php?id=<?= h($v['id']) ?>" class="vehicle-card bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group flex flex-col" data-price="<?= (int)$v['price'] ?>" data-created="<?= h($v['created_at']) ?>" data-year="<?= $yearVal ?>" data-mileage="<?= $mileageVal ?>">
                        <div class="relative aspect-[16/10] bg-gray-100 overflow-hidden">
                            <img src="<?= h($imgSrc) ?>" alt="<?= h($cTitle) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                            <!-- Price Tag -->
                            <div class="absolute top-4 left-0 z-10">
                                <div class="bg-metallic text-slate-900 px-5 py-0.5 rounded-r-xl shadow-lg border-y border-r border-white/40 flex flex-col">
                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none mb-0.5 opacity-60">Price</span>
                                    <span class="text-xl md:text-2xl font-black leading-none tracking-tighter">
                                        <?= $dPrice ?><span class="text-[10px] ml-0.5">万円</span>
                                    </span>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded z-10">即納可</div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                                <span class="text-white text-[10px] font-black uppercase tracking-widest opacity-80">No. <?= h($v['manage_number']) ?></span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-base font-bold mb-4 group-hover:text-slate-700 transition-colors text-slate-800 leading-snug tracking-tight break-words min-h-[3rem]"><?= h($cTitle) ?></h3>
                            <div class="grid grid-cols-2 gap-y-3 gap-x-4 mb-4">
                                <div class="flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    <svg class="w-3 h-3 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <?= h($basicInfo['年式'] ?? '---') ?>
                                </div>
                                <div class="flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    <svg class="w-3 h-3 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <?= h($basicInfo['走行距離'] ?? '---') ?>
                                </div>
                            </div>
                            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center text-[10px] font-black text-slate-400 uppercase tracking-widest">View Details</div>
                                <div class="bg-slate-50 group-hover:bg-slate-900 group-hover:text-white p-2.5 rounded-xl transition-all shadow-sm border border-slate-100 group-hover:border-slate-800">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- スマホ テキストセクション（在庫一覧の後） -->
<!-- ================================================================ -->
<section class="relative py-12 overflow-hidden bg-white lg:hidden">
    <div class="container relative mx-auto px-4 z-10">
        <div class="text-center space-y-6">
            <div class="inline-block bg-red-600 text-white text-lg font-bold px-4 py-2 rounded-lg mb-2 shadow-lg animate-pulse">
                仕事用の車が今すぐ必要な方へ
            </div>
            <h2 class="text-4xl font-black tracking-tight leading-[1.1] text-slate-900">
                最短<span class="text-red-600 text-5xl mx-2">即日</span>在庫案内<br>できます
            </h2>
            <p class="text-xl text-slate-700 font-bold">日産キャラバン・軽バン多数在庫</p>
            <p class="text-slate-500 font-medium">車が壊れても、仕事は止めさせません。<br>今すぐお電話ください。</p>
            <div class="pt-6">
                <a href="<?= SITE_PHONE_TEL ?>" class="w-full flex flex-col items-center justify-center bg-red-600 text-white px-10 py-5 rounded-full shadow-2xl shadow-red-600/30 hover:bg-red-700 transition-all active:scale-95 border-4 border-white ring-4 ring-red-100">
                    <div class="flex items-center text-2xl font-black">
                        <svg class="w-8 h-8 mr-3 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <?= SITE_PHONE ?>
                    </div>
                    <span class="text-sm font-bold opacity-90 mt-1"><?= SITE_HOURS ?> ｜ 在庫確認だけOK</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- 共感ゾーン -->
<!-- ================================================================ -->
<section class="py-20 bg-slate-100">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-[2rem] shadow-xl border border-slate-200 fade-up">
            <h2 class="text-2xl md:text-3xl font-black text-center mb-8 text-slate-800">
                <span class="text-red-600">こんな状況</span>でお困りではありませんか？
            </h2>
            <ul class="space-y-4 font-bold text-lg md:text-xl text-slate-700 mb-8">
                <li class="flex items-start">
                    <svg class="w-8 h-8 text-red-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    急に車が壊れて仕事に行けない
                </li>
                <li class="flex items-start">
                    <svg class="w-8 h-8 text-red-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    すぐ使える車じゃないと困る
                </li>
                <li class="flex items-start">
                    <svg class="w-8 h-8 text-red-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    修理を待っている時間がない
                </li>
                <li class="flex items-start">
                    <svg class="w-8 h-8 text-red-500 mr-3 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    とにかく早く、確実に乗りたい
                </li>
            </ul>
            <p class="text-center text-slate-500 font-medium border-t border-slate-100 pt-8">
                その状況、毎日のようにご相談を受けています。<br>私たちが最短スピードで解決します。
            </p>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- 解決策（強み） -->
<!-- ================================================================ -->
<section class="py-20 bg-slate-900 text-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="text-center p-6 bg-slate-800 rounded-3xl border border-slate-700 fade-up">
                <div class="bg-slate-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <h3 class="text-xl font-black mb-4">仕事用車両に特化</h3>
                <p class="text-slate-300 font-medium">キャラバン・軽バン・ワンボックス中心。<br>現場で使える車だけを厳選。</p>
            </div>
            <div class="text-center p-6 bg-slate-800 rounded-3xl border border-slate-700 fade-up">
                <div class="bg-slate-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-black mb-4">最短即日〜数日納車</h3>
                <p class="text-slate-300 font-medium">名義変更・整備も対応。<br>独自のルートで最速を実現。</p>
            </div>
            <div class="text-center p-6 bg-slate-800 rounded-3xl border border-slate-700 fade-up">
                <div class="bg-slate-700 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <h3 class="text-xl font-black mb-4">電話1本で在庫確認</h3>
                <p class="text-slate-300 font-medium">来店前に車が決まる。<br>今ある在庫をその場でお伝えします。</p>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- お客様の声 -->
<!-- ================================================================ -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-black text-center mb-12 text-slate-900">お客様の声</h2>
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 relative fade-up">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full overflow-hidden border border-white shadow-md shrink-0">
                        <img src="/images/lp/avatar_driver.png" alt="ユーザー" class="w-full h-full object-cover">
                    </div>
                    <div class="font-bold text-slate-900 text-sm">建設業・40代</div>
                </div>
                <p class="text-slate-800 font-bold leading-relaxed">
                    「突然車が壊れて困っていましたが、電話したその日に車が決まり、本当に助かりました」
                </p>
            </div>
            <div class="bg-slate-50 p-8 rounded-[2rem] border border-slate-100 relative fade-up">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full overflow-hidden border border-white shadow-md shrink-0">
                        <img src="/images/lp/avatar_caregiver.png" alt="ユーザー" class="w-full h-full object-cover">
                    </div>
                    <div class="font-bold text-slate-900 text-sm">個人事業主</div>
                </div>
                <p class="text-slate-800 font-bold leading-relaxed">
                    「仕事に使う車だったので、話が早くて助かりました。手続きもスムーズでした。」
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================ -->
<!-- 最終CTA (contact) -->
<!-- ================================================================ -->
<section id="contact" class="py-20 bg-slate-900 text-white text-center">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-5xl font-black mb-8 leading-tight">
            今すぐ仕事で使う車が必要なら<br>まずは電話で状況を教えてください
        </h2>
        <div class="flex flex-col items-center justify-center gap-6 max-w-xl mx-auto">
            <a href="<?= SITE_PHONE_TEL ?>" class="w-full flex flex-col items-center justify-center bg-red-600 text-white px-8 py-6 rounded-full shadow-2xl shadow-red-600/30 hover:bg-red-700 transition-all active:scale-95 border-4 border-slate-800 ring-4 ring-red-500/50">
                <div class="flex items-center text-3xl md:text-5xl font-black">
                    <svg class="w-8 h-8 md:w-12 md:h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <?= SITE_PHONE ?>
                </div>
                <span class="text-base font-bold opacity-90 mt-2"><?= SITE_HOURS ?> ｜ 相談だけOK</span>
            </a>
            <p class="text-slate-400 text-sm mt-4">
                ※フォームでの相談は、折り返しにお時間をいただく場合があります。<br>お急ぎの方は必ずお電話ください。
            </p>
        </div>

        <!-- フォーム（補助） -->
        <div id="form" class="mt-16 max-w-md mx-auto opacity-80 hover:opacity-100 transition-opacity">
            <?php if (isset($_GET['sent']) && $_GET['sent'] === '1'): ?>
                <!-- 送信完了メッセージ -->
                <div class="bg-slate-800 p-8 rounded-2xl border border-slate-700 text-center">
                    <div class="mb-6">
                        <svg class="w-16 h-16 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-white mb-4">送信を承りました</h3>
                    <p class="text-slate-300 font-medium leading-relaxed">
                        只今、フォームからの送信を承りました。<br>
                        担当者が確認次第、折り返しご連絡をいたします。<br><br>
                        お時間がかかる場合がございますので<br>
                        お急ぎの場合にはお電話をお願いいたします。
                    </p>
                    <a href="<?= SITE_PHONE_TEL ?>" class="mt-6 inline-flex items-center justify-center bg-red-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-red-700 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <?= SITE_PHONE ?>
                    </a>
                </div>
            <?php else: ?>
                <!-- 通常フォーム -->
                <p class="text-slate-400 text-sm mb-4">ー 営業時間外の方はこちら ー</p>
                <form action="/api/lp_contact.php" method="POST" class="space-y-4 bg-slate-800 p-6 rounded-2xl border border-slate-700 text-left">
                    <?= csrfField() ?>
                    <div>
                        <label class="block text-xs font-bold mb-1 text-slate-400">お名前</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-600 text-white focus:ring-2 focus:ring-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1 text-slate-400">電話番号</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-600 text-white focus:ring-2 focus:ring-slate-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1 text-slate-400">希望車種（任意）</label>
                        <input type="text" name="car_type" class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-600 text-white focus:ring-2 focus:ring-slate-500 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-slate-700 hover:bg-slate-600 text-white text-lg font-bold py-4 rounded-xl transition-all">フォームで送信</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Sticky CTA (Mobile) -->
<div class="fixed bottom-0 left-0 right-0 p-3 bg-white/95 backdrop-blur-md border-t border-slate-200 z-50 md:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
    <a href="<?= SITE_PHONE_TEL ?>" class="w-full bg-red-600 text-white text-center py-4 rounded-xl font-black text-2xl flex items-center justify-center shadow-lg active:scale-95 transition-transform">
        <svg class="w-6 h-6 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
        今すぐ電話する
    </a>
    <div class="text-center text-[10px] text-slate-500 font-bold mt-1">8:00〜20:00</div>
</div>

<!-- Desktop Sticky CTA -->
<div class="hidden md:block fixed bottom-8 right-8 z-50 fade-up-immediate">
    <a href="<?= SITE_PHONE_TEL ?>" class="flex items-center bg-red-600 hover:bg-red-700 text-white px-8 py-5 rounded-full font-bold text-xl shadow-2xl shadow-red-900/40 transition-all hover:scale-105 group border-4 border-white ring-2 ring-red-200">
        <svg class="w-6 h-6 mr-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
        今すぐ電話 <?= SITE_PHONE ?>
    </a>
</div>

<div class="h-24 md:h-0"></div>

<script src="/js/sort.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>