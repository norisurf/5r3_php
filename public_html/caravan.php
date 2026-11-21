<?php
declare(strict_types=1);
/**
 * 日産キャラバン 中古車在庫ページ（SEO: 「日産 キャラバン」「キャラバン 中古」向け）
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDB();
$all = $db->query('SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY price DESC')->fetchAll();
$vehicles = array_values(array_filter($all, fn(array $v): bool => isCaravan((string)$v['title'])));
$count = count($vehicles);

// 価格帯（表示価格・万円）
$prices = array_map(fn(array $v): int => displayPriceMan((int)$v['price']), $vehicles);
$minPrice = $prices ? min($prices) : null;
$maxPrice = $prices ? max($prices) : null;

$countLabel = $count > 0 ? '在庫' . $count . '台' : '在庫多数';
$pageTitle = '日産キャラバン中古車 ' . $countLabel . ' | NV350・E25 | 東京・練馬区の5R3';
$pageDescription = '日産キャラバンの中古車なら東京都練馬区・大泉学園の5R3。NV350キャラバン・E25キャラバンを'
    . ($count > 0 ? '現在' . $count . '台' : '常時多数')
    . '在庫' . ($minPrice !== null ? '（' . $minPrice . '万円〜）' : '')
    . '。ディーゼル・4WD・スーパーロング・ライダーなど仕事用からレジャーまで。東京都内最短当日納車、全国陸送対応。';
$pageCanonicalUrl = 'https://5r3.co.jp/caravan.php';

$faqs = [
    [
        'q' => '日産キャラバンの中古車は何台くらい在庫がありますか？',
        'a' => '5R3はキャラバンを中心に仕入れており、NV350キャラバン・E25キャラバンを常時複数台在庫しています。在庫状況は日々変わるため、このページの在庫一覧が最新です。',
    ],
    [
        'q' => 'ディーゼル車や4WDのキャラバンはありますか？',
        'a' => 'ディーゼルターボ車・4WD車・スーパーロング・ハイルーフなど、仕事用に人気の仕様を優先して仕入れています。ご希望の仕様が在庫にない場合も、お気軽にご相談ください。',
    ],
    [
        'q' => '都内・首都圏のディーゼル規制（NOx・PM法）に対応していますか？',
        'a' => 'ディーゼル車は車両ごとにNOx・PM法の適合状況を確認しています。各車両の詳細ページに記載がありますので、ご不明な点はお問い合わせください。',
    ],
    [
        'q' => 'キャラバンを購入した場合、最短でいつ納車できますか？',
        'a' => '「即納可」の車両は、書類が揃っていれば東京都内最短当日の納車が可能です。練馬区・板橋区・杉並区・西東京市・埼玉県和光市・新座市など近隣エリアはもちろん、全国陸送にも対応しています。',
    ],
    [
        'q' => '今乗っているキャラバンや商用車の下取り・買取はできますか？',
        'a' => 'はい、可能です。過走行や旧型のキャラバンでも査定いたします。乗り換えの場合は下取りと購入をまとめてご相談いただけます。',
    ],
];

// 構造化データ（ItemList + FAQPage + BreadcrumbList）
$itemList = [];
foreach ($vehicles as $i => $v) {
    $itemList[] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'url'      => 'https://5r3.co.jp/stock.php?id=' . rawurlencode((string)$v['id']),
        'name'     => titleWithMaker(cleanTitle((string)$v['title'])),
    ];
}
$schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'           => 'ItemList',
            'name'            => '日産キャラバン 中古車在庫一覧',
            'numberOfItems'   => $count,
            'itemListElement' => $itemList,
        ],
        [
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(fn(array $f): array => [
                '@type'          => 'Question',
                'name'           => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ], $faqs),
        ],
        [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'ホーム', 'item' => 'https://5r3.co.jp/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => '日産キャラバン中古車在庫', 'item' => $pageCanonicalUrl],
            ],
        ],
    ],
];
$pageCustomSchema = '<script type="application/ld+json">'
    . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG)
    . '</script>';

if (!empty($vehicles)) {
    $firstImgs = jsonDecode($vehicles[0]['images']);
    if (!empty($firstImgs)) {
        $ogImage = 'https://5r3.co.jp' . $firstImgs[0];
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ヒーロー -->
<section class="relative pt-16 pb-12 md:pt-24 md:pb-16 bg-slate-900 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-1/2 h-full bg-slate-800/10 skew-x-12 translate-x-1/4"></div>
    <div class="container mx-auto px-4 relative z-10">
        <nav class="text-xs text-slate-400 mb-6" aria-label="パンくずリスト">
            <a href="/" class="hover:text-white transition-colors">ホーム</a>
            <span class="mx-2">/</span>
            <span class="text-slate-200">日産キャラバン中古車在庫</span>
        </nav>
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 bg-metallic text-slate-900 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6">
                <span>NISSAN CARAVAN</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight tracking-tighter">
                日産キャラバンの中古車<br>
                <span class="text-slate-400">在庫一覧</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-300 mb-8 max-w-2xl font-medium leading-relaxed">
                東京都練馬区・大泉学園の5R3は、日産キャラバンを中心に在庫しているワンボックス中古車店です。
                NV350キャラバン・E25キャラバンを<?= $count > 0 ? '現在<strong class="text-white">' . $count . '台</strong>' : '常時多数' ?>在庫。
                ディーゼル・4WD・スーパーロングなど、仕事で使える一台をすぐにご案内します。
            </p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 px-6 py-4 rounded-2xl">
                    <p class="text-[10px] text-white/40 font-bold uppercase">Stock</p>
                    <p class="text-sm font-bold">キャラバン<?= $count > 0 ? ' ' . $count . '台' : '多数' ?>在庫</p>
                </div>
                <?php if ($minPrice !== null): ?>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 px-6 py-4 rounded-2xl">
                    <p class="text-[10px] text-white/40 font-bold uppercase">Price</p>
                    <p class="text-sm font-bold"><?= $minPrice ?>万円〜<?= $maxPrice ?>万円</p>
                </div>
                <?php endif; ?>
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 px-6 py-4 rounded-2xl">
                    <p class="text-[10px] text-white/40 font-bold uppercase">Delivery</p>
                    <p class="text-sm font-bold">都内最短当日納車・全国陸送</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- キャラバン在庫一覧 -->
<section id="stock" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">日産キャラバン 在庫車両</h2>
            <p class="mt-4 text-slate-500 font-medium">「即納可」の車両は最短当日の納車相談が可能です。</p>
        </div>

        <?php if (!empty($vehicles)): ?>
            <div class="flex flex-wrap items-center justify-center gap-2 mb-8">
                <button onclick="sortVehicles('price_high')" data-sort="price_high" class="sort-btn active px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-900 text-white shadow-lg">価格が高い順</button>
                <button onclick="sortVehicles('price_low')" data-sort="price_low" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">価格が安い順</button>
                <button onclick="sortVehicles('year_new')" data-sort="year_new" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">年式が新しい順</button>
                <button onclick="sortVehicles('mileage_low')" data-sort="mileage_low" class="sort-btn px-4 py-2 rounded-full text-sm font-bold transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">走行距離が少ない順</button>
            </div>

            <div id="vehicle-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <?php foreach ($vehicles as $v):
                    $imgs = jsonDecode($v['images']);
                    $imgSrc = !empty($imgs) ? $imgs[0] : '/images/placeholder.png';
                    $basicInfo = jsonDecode($v['basic_info']);
                    $dPrice = displayPriceMan((int)$v['price']);
                    $cTitle = cleanTitle((string)$v['title']);

                    $yearVal = 0;
                    $yearStr = $basicInfo['年式'] ?? '';
                    if (preg_match('/\((\d{4})年?\)/', $yearStr, $m)) {
                        $yearVal = (int)$m[1];
                    } elseif (preg_match('/(\d{4})/', $yearStr, $m)) {
                        $yearVal = (int)$m[1];
                    }
                    $mileageVal = 0;
                    $mileageStr = $basicInfo['走行距離'] ?? '';
                    if (preg_match('/([\d,.]+)\s*万/', $mileageStr, $m)) {
                        $mileageVal = (int)(floatval(str_replace(',', '', $m[1])) * 10000);
                    } elseif (preg_match('/([\d,]+)/', $mileageStr, $m)) {
                        $mileageVal = (int)str_replace(',', '', $m[1]);
                    }
                ?>
                    <a href="/stock.php?id=<?= h((string)$v['id']) ?>"
                        class="vehicle-card bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group flex flex-col"
                        data-price="<?= (int)$v['price'] ?>" data-created="<?= h((string)$v['created_at']) ?>"
                        data-year="<?= $yearVal ?>" data-mileage="<?= $mileageVal ?>">
                        <div class="relative aspect-[16/10] bg-gray-100 overflow-hidden">
                            <img src="<?= h($imgSrc) ?>" alt="日産 <?= h($cTitle) ?> 中古車"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
                            <div class="absolute top-4 left-0 z-10">
                                <div class="bg-metallic text-slate-900 px-5 py-0.5 rounded-r-xl shadow-lg border-y border-r border-white/40 flex flex-col">
                                    <span class="text-[10px] font-black uppercase tracking-widest leading-none mb-0.5 opacity-60">Price</span>
                                    <span class="text-xl md:text-2xl font-black leading-none tracking-tighter">
                                        <?= $dPrice ?><span class="text-[10px] ml-0.5">万円</span>
                                    </span>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded z-10">即納可</div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-base font-bold mb-4 group-hover:text-slate-700 transition-colors text-slate-800 leading-snug tracking-tight break-words min-h-[3rem]">
                                <?= h($cTitle) ?>
                            </h3>
                            <div class="grid grid-cols-2 gap-y-3 gap-x-4 mb-4">
                                <div class="flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    <?= h($basicInfo['年式'] ?? '---') ?>
                                </div>
                                <div class="flex items-center text-[10px] font-bold text-slate-500 uppercase tracking-wider">
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
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 mb-12">現在、掲載中のキャラバンは入れ替え中です。入荷予定の車両もご案内できますので、お気軽にお問い合わせください。</p>
        <?php endif; ?>

        <div class="text-center">
            <a href="/sales.php" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                キャラバン以外の在庫（ハイエース・軽バンなど）も見る →
            </a>
        </div>
    </div>
</section>

<!-- キャラバンの選び方 -->
<section class="py-20 bg-slate-50 border-y border-slate-100">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-black text-center mb-10 text-slate-900">中古の日産キャラバンの選び方</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold mb-3 text-slate-900">NV350キャラバン（E26型）</h3>
                    <p class="text-slate-600 leading-relaxed">2012年から販売された先代モデル。中古車として流通量が多く、年式・装備・価格の選択肢が豊富です。ガソリン・ディーゼルともに仕事用・レジャー用で人気があります。</p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold mb-3 text-slate-900">E25型キャラバン</h3>
                    <p class="text-slate-600 leading-relaxed">NV350の一つ前のモデル。車両価格を抑えたい方に選ばれています。耐久性の高さから、年式が古くても現場用として十分に活躍します。</p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold mb-3 text-slate-900">DX・GX・ライダー</h3>
                    <p class="text-slate-600 leading-relaxed">DXは仕事向けのシンプルな仕様、GXは装備が充実した上級グレード、ライダーはエアロや内装を仕立てたカスタムモデルです。用途とご予算に合わせてお選びください。</p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-bold mb-3 text-slate-900">ディーゼル・4WD・スーパーロング</h3>
                    <p class="text-slate-600 leading-relaxed">長距離・重積載ならディーゼルターボ、雪道や現場の悪路なら4WD、荷室の長さが必要ならスーパーロングがおすすめです。ディーゼル車はNOx・PM法の適合状況もご確認いただけます。</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-black text-center mb-10 text-slate-900">日産キャラバン購入のよくある質問</h2>
            <div class="space-y-4">
                <?php foreach ($faqs as $f): ?>
                <details class="bg-slate-50 rounded-2xl border border-slate-100 p-6 group">
                    <summary class="font-bold text-slate-900 cursor-pointer"><?= h($f['q']) ?></summary>
                    <p class="mt-4 text-slate-600 leading-relaxed"><?= h($f['a']) ?></p>
                </details>
                <?php endforeach; ?>
            </div>
            <div class="mt-10 text-center">
                <a href="/purchase_caravan.php" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                    キャラバンを売りたい方はこちら（キャラバン買取・査定）→
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section id="contact" class="py-20 bg-slate-900 text-white text-center">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight">キャラバンの在庫確認・<br>即納のご相談はお電話で</h2>
        <p class="text-slate-400 mb-10 font-medium">ご希望の年式・仕様・ご予算をお伝えいただければ、在庫からすぐにご提案します。</p>
        <a href="<?= SITE_PHONE_TEL ?>" class="inline-flex items-center justify-center bg-red-600 text-white px-10 py-5 rounded-full shadow-2xl hover:bg-red-700 transition-all text-3xl font-black">
            <?= SITE_PHONE ?>
        </a>
        <p class="mt-4 text-sm text-slate-400"><?= SITE_HOURS ?></p>
    </div>
</section>

<script src="/js/sort.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
