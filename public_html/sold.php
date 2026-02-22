<?php
declare(strict_types=1);
/**
 * 販売実績ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDB();

$stmt = $db->query('SELECT * FROM vehicles WHERE deleted_at IS NOT NULL ORDER BY updated_at DESC');
$vehicles = $stmt->fetchAll();

$pageTitle       = '販売実績 - ' . SITE_NAME;
$pageDescription = '5R3 CARSのこれまでの販売実績車両一覧です。';

require_once __DIR__ . '/includes/header.php';
?>

<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <!-- Section Title -->
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="h-px w-12 bg-metallic"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Sales Record</span>
                <div class="h-px w-12 bg-metallic"></div>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">販売実績</h1>
            <p class="mt-4 text-slate-500 font-medium text-sm max-w-xl mx-auto">
                これまでに販売した車両の一覧です。
            </p>
        </div>

        <!-- Vehicle Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <?php if (empty($vehicles)): ?>
                <div class="col-span-full py-20 text-center text-slate-300">販売実績はまだありません。</div>
            <?php else: ?>
                <?php foreach ($vehicles as $v):
                    $imgs     = jsonDecode($v['images']);
                    $imgSrc   = !empty($imgs) ? $imgs[0] : '/images/placeholder.png';
                    $basicInfo = jsonDecode($v['basic_info']);
                    $dPrice   = displayPriceMan($v['price']);
                    $cTitle   = cleanTitle($v['title']);
                ?>
                    <a href="/stock.php?id=<?= h($v['id']) ?>&from=sold"
                       class="vehicle-card bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group flex flex-col">
                        <div class="relative aspect-[16/10] bg-gray-100 overflow-hidden">
                            <img src="<?= h($imgSrc) ?>" alt="<?= h($cTitle) ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 loading="lazy">
                            <!-- Price Tag -->
                            <div class="absolute top-4 left-0 z-10">
                                <div class="bg-metallic text-slate-900 px-5 py-0.5 rounded-r-xl shadow-lg border-y border-r border-white/40 flex flex-col">
                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none mb-0.5 opacity-60">Price</span>
                                    <span class="text-xl md:text-2xl font-black leading-none tracking-tighter">
                                        <?= $dPrice ?><span class="text-[10px] ml-0.5">万円</span>
                                    </span>
                                </div>
                            </div>
                            <!-- SOLD OUT バッジ（右上のみ） -->
                            <div class="absolute top-2 right-2 bg-black text-white text-xs font-bold px-2 py-1 rounded z-10 tracking-widest">SOLD OUT</div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                                <span class="text-white text-[10px] font-black uppercase tracking-widest opacity-80">No. <?= h($v['manage_number']) ?></span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-base font-bold mb-4 group-hover:text-slate-700 transition-colors text-slate-500 leading-snug tracking-tight break-words min-h-[3rem]"><?= h($cTitle) ?></h3>
                            <div class="grid grid-cols-2 gap-y-3 gap-x-4 mb-4">
                                <div class="flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <svg class="w-3 h-3 mr-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <?= h($basicInfo['年式'] ?? '---') ?>
                                </div>
                                <div class="flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <svg class="w-3 h-3 mr-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <?= h($basicInfo['走行距離'] ?? '---') ?>
                                </div>
                            </div>
                            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center text-[10px] font-black text-slate-300 uppercase tracking-widest">View Details</div>
                                <div class="bg-slate-50 group-hover:bg-slate-900 group-hover:text-white p-2.5 rounded-xl transition-all shadow-sm border border-slate-100 group-hover:border-slate-800">
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
