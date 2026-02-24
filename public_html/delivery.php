<?php
declare(strict_types=1);
/**
 * 全国陸送対応ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '全国陸送対応 - ' . SITE_NAME;
$pageDescription = '日本全国どこへでも格安で陸送対応。地域別の陸送代金目安をご確認いただけます。';
require_once __DIR__ . '/includes/header.php';

$deliveryFees = [
    [
        'region' => '埼玉県(西部地域/中央地域)<br>東京都(西武線沿い/多摩北部)',
        'price' => '2.0万円 - 2.5万円'
    ],
    [
        'region' => '東京都、千葉県(北西部/北東部)<br>神奈川県(県央地域/横浜市)<br>埼玉県(東部/北部/秩父地域)',
        'price' => '2.5万円 - 3.5万円'
    ],
    [
        'region' => '千葉県(南部/外房周辺)<br>神奈川県(横須賀地域/県西地域)',
        'price' => '3.5万円 - 4.0万円'
    ],
    [
        'region' => '栃木県 / 群馬県 / 茨城県',
        'price' => '4.0万円 ～'
    ],
    [
        'region' => '静岡県 / 山梨県 / 福島県',
        'price' => '5.0万円 ～'
    ],
    [
        'region' => '長野県 / 富山県 / 宮城県 / 新潟県 / 岐阜県 / 愛知県',
        'price' => '5.5万円 ～'
    ],
    [
        'region' => '岩手県 / 石川県 / 滋賀県 / 三重県 / 奈良県 / 京都府 / 大阪府 / 兵庫県',
        'price' => '6.5万円 ～'
    ],
    [
        'region' => '福井県 / 和歌山県',
        'price' => '7.0万円 ～'
    ],
    [
        'region' => '秋田県 / 山形県 / 福岡県',
        'price' => '7.5万円 ～'
    ],
    [
        'region' => '青森県 / 岡山県 / 広島県 / 佐賀県',
        'price' => '8.0万円 ～'
    ],
    [
        'region' => '四国 (徳島県 / 香川県 / 愛媛県 / 高知県)',
        'price' => '8.5万円 ～'
    ],
    [
        'region' => '鳥取県 / 島根県 / 山口県 / 大分県 / 長崎県 / 熊本県',
        'price' => '9.0万円 ～'
    ],
    [
        'region' => '北海道 / 宮崎県 / 鹿児島県',
        'price' => '10.0万円 ～'
    ],
    [
        'region' => '沖縄県',
        'price' => '13.5万円 ～'
    ],
];
?>

<!-- Hero -->
<section class="relative pt-20 pb-16 md:pt-32 md:pb-24 bg-slate-900 text-white overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="/images/lp/riku_01.png" alt="Land Transportation Carrier"
            class="w-full h-full object-cover opacity-70">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/50 to-transparent"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <div
                class="inline-flex items-center space-x-2 bg-metallic text-slate-900 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6 border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                <span>DELIVERY</span>
            </div>
            <h2 class="text-4xl md:text-6xl font-black mb-8 leading-tight tracking-tighter">
                全国陸送対応
            </h2>
            <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl font-medium leading-relaxed">
                北は北海道から南は沖縄まで、日本全国どこへでも大切なお車をお届けします。提携陸送会社との連携により、安心・確実な格安輸送を実現しています。
            </p>
        </div>
    </div>
</section>

<!-- Fee Table -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto mb-16 text-center">
            <h3 class="text-2xl md:text-3xl font-black text-slate-900 mb-4 tracking-tight">陸送代金表</h3>
            <p class="text-slate-500 font-medium italic text-sm">※下記料金表は目安となります。詳細はお問い合わせください。</p>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th
                                class="px-6 md:px-10 py-5 text-left text-xs font-black uppercase tracking-widest border-r border-white/10">
                                対象地域</th>
                            <th
                                class="px-6 md:px-10 py-5 text-center text-xs font-black uppercase tracking-widest w-40 md:w-56">
                                陸送目安料金</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-bold">
                        <?php foreach ($deliveryFees as $fee): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 md:px-10 py-6 text-slate-700 text-sm leading-relaxed">
                                    <?= $fee['region'] ?>
                                </td>
                                <td class="px-6 md:px-10 py-6 text-center bg-slate-50/50">
                                    <span
                                        class="bg-red-600 text-white px-4 py-1.5 rounded-full text-sm md:text-base font-black shadow-lg shadow-red-200 inline-block whitespace-nowrap">
                                        <?= h($fee['price']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-12 space-y-4">
                <div class="bg-slate-50 p-6 md:p-8 rounded-3xl border border-slate-100">
                    <ul class="space-y-3 text-sm text-slate-600 font-medium">
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2">※</span>
                            <span>その他離島などはお問合せ下さい。</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2">※</span>
                            <span>陸送手配は、お客様ご自身での手配も可能です。</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-600 mr-2">※</span>
                            <span>納車場所の詳細（市区町村）や、首都部から離れている場合は料金が大幅に変動する場合がございます。</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div
            class="max-w-5xl mx-auto bg-slate-900 rounded-[3rem] p-8 md:p-16 text-center text-white relative overflow-hidden shadow-2xl border border-slate-800">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-slate-800/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/2">
            </div>

            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight">
                    陸送やお見積りについて<br>お気軽にご相談ください
                </h2>
                <div class="flex flex-col md:flex-row items-center justify-center gap-6 mt-12">
                    <a href="tel:<?= str_replace('-', '', SITE_PHONE) ?>"
                        class="bg-white text-slate-900 px-10 py-5 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl w-full md:w-auto min-w-[280px]">
                        <span class="text-xs font-bold text-slate-400 mb-1 uppercase tracking-widest">Phone</span>
                        <span class="text-2xl font-black">
                            <?= SITE_PHONE ?>
                        </span>
                    </a>
                    <a href="#contact"
                        class="bg-red-600 text-white px-10 py-5 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl w-full md:w-auto min-w-[280px]">
                        <span class="text-xs font-bold text-white/70 mb-1 uppercase tracking-widest">Contact Form</span>
                        <span class="text-2xl font-black">メールで相談</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>