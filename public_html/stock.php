<?php
declare(strict_types=1);
/**
 * 車両詳細ページ
 * /stock.php?id=xxx
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: /');
    exit;
}

$db = getDB();
$stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ?');
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    header('Location: /');
    exit;
}

$images = jsonDecode($vehicle['images']);
$basicInfo = jsonDecode($vehicle['basic_info'], []);
$detailedInfo = jsonDecode($vehicle['detailed_info'], []);
$equipment = jsonDecode($vehicle['equipment'], []);
$price = displayPrice((int)$vehicle['price']);
$title = cleanTitle($vehicle['title']);
$description = cleanDescription($vehicle['description'] ?? '');

$pageTitle = $title . ' - ' . SITE_NAME;
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white pb-20 pt-8 text-gray-800">
    <div class="container mx-auto px-4">
        <a href="/" class="mb-8 inline-flex items-center gap-2 text-gray-500 hover:text-[#003366] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>在庫一覧に戻る</span>
        </a>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
            <!-- 画像ギャラリー -->
            <div class="space-y-4">
                <!-- メイン画像 -->
                <div class="relative aspect-video w-full cursor-zoom-in overflow-hidden rounded-3xl bg-gray-100 shadow-lg group">
                    <img id="main-image" src="<?= h($images[0] ?? '') ?>" alt="<?= h($title) ?>" class="h-full w-full object-contain" loading="lazy">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 opacity-0 transition-all group-hover:bg-black/10 group-hover:opacity-100">
                        <div class="rounded-full bg-white/20 p-4 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- サムネイル -->
                <div class="grid grid-cols-5 gap-2 md:grid-cols-10">
                    <?php foreach ($images as $idx => $img): ?>
                    <button class="gallery-thumb relative aspect-square overflow-hidden rounded-xl border-2 transition-all <?= $idx === 0 ? 'border-yellow-400 ring-2 ring-yellow-400/20' : 'border-transparent hover:border-gray-300' ?>" data-src="<?= h($img) ?>">
                        <img src="<?= h($img) ?>" alt="サムネイル <?= $idx + 1 ?>" class="h-full w-full object-cover" loading="lazy">
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 車両情報 -->
            <div class="space-y-8">
                <div>
                    <h1 class="text-2xl font-bold leading-tight text-black md:text-3xl"><?= h($title) ?></h1>
                    <div class="mt-4 flex flex-wrap gap-4">
                        <?php if (!empty($basicInfo['年式'])): ?>
                        <div class="flex items-center gap-1.5 rounded-full bg-gray-100 px-4 py-1.5 text-sm font-medium text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span><?= h($basicInfo['年式']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($basicInfo['走行距離'])): ?>
                        <div class="flex items-center gap-1.5 rounded-full bg-gray-100 px-4 py-1.5 text-sm font-medium text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span><?= h($basicInfo['走行距離']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($vehicle['manage_number'])): ?>
                        <div class="flex items-center gap-1.5 rounded-full bg-gray-100 px-4 py-1.5 text-sm font-medium text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span><?= h($vehicle['manage_number']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 価格ボックス -->
                <div class="rounded-3xl border border-gray-800 bg-black p-8 text-white shadow-xl shadow-black/20">
                    <div class="mb-2 text-sm font-medium text-gray-400">車両本体価格（税込）</div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-black text-white md:text-5xl"><?= number_format($price) ?></span>
                        <span class="text-xl font-bold">円</span>
                    </div>
                    <a href="tel:<?= str_replace('-', '', SITE_PHONE) ?>" class="mt-8 flex w-full items-center justify-center gap-2 rounded-xl bg-metallic px-8 py-4 text-lg font-bold text-slate-900 shadow-xl transition-all hover:scale-105 active:scale-95 border border-white/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>お問い合わせ: <?= SITE_PHONE ?></span>
                    </a>
                </div>

                <!-- クイック情報 -->
                <div class="grid grid-cols-2 gap-4">
                    <?php if (!empty($basicInfo['メーカー名'])): ?>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
                        <div class="text-xs text-gray-500">メーカー</div>
                        <div class="mt-1 font-bold text-gray-800"><?= h($basicInfo['メーカー名']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($basicInfo['グレード名'])): ?>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
                        <div class="text-xs text-gray-500">グレード</div>
                        <div class="mt-1 font-bold text-gray-800"><?= h($basicInfo['グレード名']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($basicInfo['ミッション'])): ?>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
                        <div class="text-xs text-gray-500">ミッション</div>
                        <div class="mt-1 font-bold text-gray-800"><?= h($basicInfo['ミッション']) ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($detailedInfo['修復歴'])): ?>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
                        <div class="text-xs text-gray-500">修復歴</div>
                        <div class="mt-1 font-bold text-gray-800"><?= h($detailedInfo['修復歴']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- スペック・装備品・商品説明 -->
        <div class="mt-16 grid grid-cols-1 gap-12 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-8">
                <!-- 基本情報テーブル -->
                <?php if (!empty($basicInfo)): ?>
                <div>
                    <h3 class="mb-4 text-xl font-bold text-[#003366] border-l-4 border-[#facc15] pl-3">自動車基本情報</h3>
                    <div class="overflow-hidden rounded-2xl border border-gray-200">
                        <table class="w-full text-left text-sm">
                            <tbody>
                                <?php $i = 0; foreach ($basicInfo as $key => $value): ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?>">
                                    <th class="w-1/3 px-4 py-3 font-medium text-gray-600 border-r border-gray-200"><?= h($key) ?></th>
                                    <td class="px-4 py-3 text-gray-800"><?= h((string)$value) ?></td>
                                </tr>
                                <?php $i++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 詳細情報テーブル -->
                <?php if (!empty($detailedInfo)): ?>
                <div>
                    <h3 class="mb-4 text-xl font-bold text-[#003366] border-l-4 border-[#facc15] pl-3">車両詳細情報</h3>
                    <div class="overflow-hidden rounded-2xl border border-gray-200">
                        <table class="w-full text-left text-sm">
                            <tbody>
                                <?php $i = 0; foreach ($detailedInfo as $key => $value): ?>
                                <tr class="<?= $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?>">
                                    <th class="w-1/3 px-4 py-3 font-medium text-gray-600 border-r border-gray-200"><?= h($key) ?></th>
                                    <td class="px-4 py-3 text-gray-800"><?= h((string)$value) ?></td>
                                </tr>
                                <?php $i++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 装備品 -->
                <?php if (!empty($equipment)): ?>
                <div>
                    <h3 class="mb-4 text-xl font-bold text-[#003366] border-l-4 border-[#facc15] pl-3">装備品</h3>
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        <?php foreach ($equipment as $item): ?>
                        <div class="flex items-center gap-2 rounded-xl bg-white p-3 shadow-sm border border-gray-100">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-[#facc15]/20 text-[#003366]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700"><?= h($item) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- 商品説明 -->
            <div class="lg:col-span-1">
                <div class="rounded-3xl bg-gray-50 p-6 md:p-8">
                    <h3 class="mb-6 text-xl font-bold text-[#003366] border-l-4 border-[#facc15] pl-3">商品説明</h3>
                    <div class="whitespace-pre-wrap text-base leading-relaxed text-gray-700"><?= nl2br(h($description)) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ライトボックス -->
<div id="lightbox" class="lightbox">
    <button id="lightbox-close" class="absolute right-4 top-4 z-50 rounded-full bg-white/10 p-2 text-white transition-colors hover:bg-white/20">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <button id="lightbox-prev" class="absolute left-4 top-1/2 z-50 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition-colors hover:bg-white/20 md:left-8">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button id="lightbox-next" class="absolute right-4 top-1/2 z-50 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition-colors hover:bg-white/20 md:right-8">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="relative h-[85vh] w-[90vw] md:w-[85vw]">
        <img id="lightbox-img" src="" alt="車両拡大画像" class="h-full w-full object-contain">
    </div>
    <div id="lightbox-counter" class="absolute bottom-6 left-1/2 -translate-x-1/2 rounded-full bg-black/50 px-4 py-2 text-sm text-white backdrop-blur-md"></div>
</div>

<script src="/js/gallery.js"></script>
<?php
declare(strict_types=1); require_once __DIR__ . '/includes/footer.php'; ?>
