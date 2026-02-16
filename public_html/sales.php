<?php
/**
 * 中古車販売ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '中古車販売 - ' . SITE_NAME;
$pageDescription = 'ワゴン・商用バン専門の5R3 CARSが厳選した在庫車両をご紹介。徹底した品質管理とスピード納車でビジネスを止めません。';
require_once __DIR__ . '/includes/header.php';

// 車両データ取得
$db = getDB();
$vehicles = $db->query('SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC')->fetchAll();
?>

<!-- 在庫一覧 -->
<section id="stock" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">在庫車両一覧</h2>
            <p class="mt-4 text-slate-500 font-medium">厳選されたワゴン・商用バンをご覧ください</p>
        </div>

        <?php if (!empty($vehicles)): ?>
        <!-- ソートボタン -->
        <div class="flex flex-wrap gap-2 mb-8 justify-center">
            <button onclick="sortVehicles('newest')" class="sort-btn active rounded-full bg-slate-900 px-4 py-2 text-xs font-bold text-white" data-sort="newest">新着順</button>
            <button onclick="sortVehicles('price-asc')" class="sort-btn rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200" data-sort="price-asc">価格が安い順</button>
            <button onclick="sortVehicles('price-desc')" class="sort-btn rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200" data-sort="price-desc">価格が高い順</button>
            <button onclick="sortVehicles('mileage')" class="sort-btn rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200" data-sort="mileage">走行距離順</button>
            <button onclick="sortVehicles('year')" class="sort-btn rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-200" data-sort="year">年式順</button>
        </div>

        <div id="stock-grid" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <?php foreach ($vehicles as $v):
                $vImages = jsonDecode($v['images']);
                $vBasicInfo = jsonDecode($v['basic_info'], []);
                $vPrice = displayPrice((int)$v['price']);
                $vPriceMan = displayPriceMan((int)$v['price']);
                $vTitle = cleanTitle($v['title']);
            ?>
            <a href="/stock.php?id=<?= h($v['id']) ?>" class="stock-card group block overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all hover:shadow-xl hover:-translate-y-1"
               data-price="<?= $vPrice ?>"
               data-year="<?= h($vBasicInfo['年式'] ?? '') ?>"
               data-mileage="<?= h($vBasicInfo['走行距離'] ?? '') ?>"
               data-created="<?= h($v['created_at']) ?>">
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                    <img src="<?= h($vImages[0] ?? '') ?>" alt="<?= h($vTitle) ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                        <div class="metallic-price-tag inline-flex items-baseline gap-1 rounded-lg px-3 py-1.5">
                            <span class="text-2xl font-black"><?= $vPriceMan ?></span>
                            <span class="text-xs font-bold">万円</span>
                            <span class="text-[8px] font-medium ml-1">(税込)</span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-bold text-gray-800 line-clamp-2 mb-2"><?= h($vTitle) ?></h3>
                    <div class="flex flex-wrap gap-2 text-[10px] text-gray-500">
                        <?php if (!empty($vBasicInfo['年式'])): ?>
                        <span class="rounded bg-gray-100 px-2 py-0.5"><?= h($vBasicInfo['年式']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($vBasicInfo['走行距離'])): ?>
                        <span class="rounded bg-gray-100 px-2 py-0.5"><?= h($vBasicInfo['走行距離']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center text-gray-500">現在在庫はございません。お気軽にお問い合わせください。</p>
        <?php endif; ?>
    </div>
</section>

<!-- Sales Hero -->
<section class="relative pt-20 pb-16 md:pt-32 md:pb-24 bg-slate-900 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-1/2 h-full bg-slate-800/10 skew-x-12 translate-x-1/4"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 bg-metallic text-slate-900 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>USED CAR SALES</span>
            </div>
            <h2 class="text-4xl md:text-6xl font-black mb-8 leading-tight tracking-tighter">
                プロ仕様の厳選在庫を、<br>
                <span class="text-slate-400">ビジネスに即戦力で。</span>
            </h2>
            <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl font-medium leading-relaxed">
                ワゴン・商用バン専門の5R3 CARSだからこそ可能な、徹底した品質管理とスピード納車。あなたのビジネスを止めない、最適な一台が見つかります。
            </p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 px-6 py-4 rounded-2xl flex items-center space-x-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <div>
                        <p class="text-[10px] text-white/40 font-bold uppercase">Quality</p>
                        <p class="text-sm font-bold">24項目事前点検済</p>
                    </div>
                </div>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 px-6 py-4 rounded-2xl flex items-center space-x-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <div>
                        <p class="text-[10px] text-white/40 font-bold uppercase">Delivery</p>
                        <p class="text-sm font-bold">最短当日納車対応</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- サポート情報 -->
<section class="bg-slate-50 py-24 border-y border-slate-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">購入をサポート</h2>
            <p class="mt-4 text-slate-500 font-medium max-w-2xl mx-auto">安心してお乗りいただけるよう、お手続きから納車後までプロがサポートします。</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <span class="w-2 h-8 bg-metallic rounded-full mr-3"></span>
                    日本全国への納車対応
                </h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    関東近郊はもちろん、提携陸送会社との連携により日本全国どこへでもお届けいたします。遠方のお客様も、オンライン商談で詳しく車両をご確認いただけます。
                </p>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <span class="w-2 h-8 bg-metallic rounded-full mr-3"></span>
                    アフターフォロー
                </h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    納車後のメンテナンスや車検、増車のご相談もお気軽に。プロのメカニックがあなたのビジネスパートナーとして長くお付き合いさせていただきます。
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 購入までの流れ -->
<section id="flow" class="py-24 bg-white border-y border-slate-100">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">購入までの流れ</h2>
            <p class="mt-4 text-slate-500 font-medium">即納をご希望の場合の最短フローです。</p>
        </div>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 max-w-5xl mx-auto">
            <?php
            $steps = [
                ['title' => '在庫確認・相談', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
                ['title' => '必要書類確認', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                ['title' => '車検・登録（最短当日）', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                ['title' => '納車', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
            ];
            foreach ($steps as $i => $step): ?>
            <div class="flex flex-col items-center flex-1 relative group w-full md:w-auto">
                <div class="bg-white w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 group-hover:scale-110 transition-transform relative z-10">
                    <svg class="w-6 h-6 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $step['icon'] ?></svg>
                </div>
                <div class="text-center mt-6">
                    <span class="text-[10px] text-slate-400 font-bold mb-1 block uppercase tracking-widest">STEP 0<?= $i + 1 ?></span>
                    <p class="text-sm font-bold text-slate-800"><?= h($step['title']) ?></p>
                </div>
                <?php if ($i < count($steps) - 1): ?>
                <div class="hidden md:block absolute top-8 left-[70%] w-full h-0.5 bg-slate-100 -z-0"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 当日納車が可能な条件 -->
<section id="conditions" class="py-24 bg-white border-y border-slate-100">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">当日納車が可能な条件</h2>
                <p class="mt-4 text-slate-500 font-medium">最短納車を実現するためには、お客様のご協力も必要不可欠です。下記の条件をご確認ください。</p>
            </div>

            <div class="bg-slate-50 p-8 md:p-10 rounded-3xl space-y-6 border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    $requirements = [
                        '必要書類（印鑑証明・実印等）が手元に全て揃っていること',
                        '店舗に在庫がある車両であること（取り寄せは対象外）',
                        '平日かつ、陸運局の開庁時間内に手続きが完了すること',
                        'お支払い方法の確認（現金・事前振込・即日ローン審査等）が取れること',
                    ];
                    foreach ($requirements as $req): ?>
                    <div class="flex items-start bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                        <svg class="w-5 h-5 text-slate-400 shrink-0 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-bold leading-relaxed text-slate-700"><?= h($req) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-start">
                    <svg class="w-5 h-5 text-white/50 shrink-0 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs text-white/70 font-medium leading-relaxed">
                        ※ 道路状況や陸運局の混雑状況、特殊架装が必要な場合などは、翌日以降の納車となる場合がございます。あらかじめご了承ください。
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA ボタン -->
<div class="py-24 text-center bg-white">
    <a href="#contact" class="inline-flex items-center space-x-2 bg-slate-900 text-white px-10 py-5 rounded-full text-lg font-black hover:bg-slate-800 transition-all shadow-xl shadow-black/10 group">
        <span>在庫車両について相談する</span>
        <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>

<!-- Final CTA -->
<section id="contact" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto bg-slate-900 rounded-[3rem] p-8 md:p-16 text-center text-white relative overflow-hidden shadow-2xl border border-slate-800">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-800/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/2"></div>

            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight">
                    急ぎの増車・納車、<br>まずはご相談ください
                </h2>
                <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto font-medium">
                    書類の準備状況や、ご希望の車種についてお聞かせください。プロが全力で当日納車に向けて並行稼働します。
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="tel:<?= str_replace('-', '', SITE_PHONE) ?>" class="bg-white text-slate-900 p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl">
                        <div class="bg-slate-50 p-4 rounded-2xl mb-4 group-hover:bg-slate-100 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 mb-1 uppercase tracking-widest">Phone</span>
                        <span class="text-2xl font-black"><?= SITE_PHONE ?></span>
                        <span class="text-[10px] text-slate-400 mt-2 font-bold"><?= SITE_HOURS ?> 受付</span>
                    </a>

                    <a href="<?= LINK_LINE ?>" class="bg-[#06C755] text-white p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl">
                        <div class="bg-white/10 p-4 rounded-2xl mb-4 group-hover:bg-white/20 transition-colors">
                            <img src="/images/line-icon.png" alt="LINE" class="w-12 h-12 rounded-xl">
                        </div>
                        <span class="text-xs font-bold text-white/70 mb-1 uppercase tracking-widest">LINE</span>
                        <span class="text-2xl font-black italic">LINEで相談</span>
                        <span class="text-[10px] text-white/70 mt-2 font-bold">24時間メッセージ送信OK</span>
                    </a>

                    <a href="mailto:<?= SITE_EMAIL ?>" class="bg-metallic text-slate-900 p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl border border-white/40">
                        <div class="bg-white/20 p-4 rounded-2xl mb-4 group-hover:bg-white/30 transition-colors">
                            <svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-800/50 mb-1 uppercase tracking-widest">Mail</span>
                        <span class="text-2xl font-black">相談フォーム</span>
                        <span class="text-[10px] text-slate-800/50 mt-2 font-bold">担当者より1時間以内に返信</span>
                    </a>
                </div>

                <div class="mt-12 flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-8 text-sm font-bold opacity-40">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        全国陸送納車対応
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        業者販売大歓迎
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="/js/sort.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
