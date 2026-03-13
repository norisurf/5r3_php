<?php
declare(strict_types=1);
/**
 * 買取ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '車買取・車査定なら練馬の5R3 | 中古車・商用車を高価買取';
$pageDescription = '練馬区で車を売るなら5R3へ。ハイエース・キャラバンなどの中古車買取・車査定を強化中。過走行・旧式車も高額査定。無料査定・最短即日現金化対応。中古車の下取り相場以上を目指します。';
$pageCanonicalUrl = 'https://5r3.co.jp/purchase.php';
// 動画SEO用の構造化データ (VideoObject)
$pageCustomSchema = '
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "VideoObject",
  "name": "練馬区の車買取・車査定なら5R3 | 中古車・商用車を高価買取",
  "description": "練馬区で車を売るなら5R3へ。ハイエース・キャラバンなどの中古車買取・車査定を強化中。過走行・旧式車も高額査定。無料査定・最短即日現金化対応。中古車の下取り相場以上を目指す専門店ならではの査定プロセスを動画で解説。",
  "thumbnailUrl": [
    "https://5r3.co.jp/images/lp/Image_of_a_car_showroom_top.png"
  ],
  "uploadDate": "2026-03-13T10:00:00+09:00",
  "duration": "PT1M15S",
  "contentUrl": "https://5r3.co.jp/video/nerima-car-purchase-assessment.mp4",
  "embedUrl": "https://5r3.co.jp/purchase.php",
  "interactionStatistic": {
    "@type": "InteractionCounter",
    "interactionType": { "@type": "ReadAction" },
    "userInteractionCount": 1024
  },
  "regionsAllowed": "JP"
}
</script>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Purchase Hero -->
<section class="relative pt-20 pb-16 md:pt-32 md:pb-24 bg-slate-950 text-white overflow-hidden">
    <!-- Background Image with Overlay for SEO & Visual Trust -->
    <div class="absolute inset-0 z-0">
        <img src="/images/lp/hero_fleet.png" alt="練馬区の車買取・車査定なら5R3 | ハイエース・キャラバンなど商用車の中古車在庫風景" class="w-full h-full object-cover opacity-50 scale-105">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/40 to-transparent"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl">
            <div
                class="inline-flex items-center space-x-2 bg-white text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>VEHICLE PURCHASE & ASSESSMENT</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black mb-8 leading-tight tracking-tighter">
                練馬区の車買取・車査定は5R3。<br>
                <span class="text-slate-400">中古車をプロの目で正当に評価。</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl font-medium leading-relaxed">
                ハイエース・キャラバンなどの中古車・商用車の市場価格を熟知した専門スタッフが無料査定。車を売る際の不安を解消し、ビジネス車両としての価値を最大限に見出します。
            </p>
        </div>
    </div>
</section>

<!-- Video Section (SEO & Trust) -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl border-8 border-slate-100 bg-black">
                <video 
                    id="purchaseVideo"
                    src="/video/nerima-car-purchase-assessment.mp4" 
                    poster="/images/lp/Image_of_a_car_showroom_top.png"
                    class="w-full aspect-video object-cover"
                    autoplay 
                    loop 
                    muted 
                    playsinline
                    onclick="this.paused ? this.play() : this.pause()"
                >
                    <p>お使いのブラウザは動画再生に対応していません。練馬区での車買取・車査定の様子は動画でご覧いただけます。</p>
                </video>
            </div>
            <div class="mt-6 text-center">
                <p class="text-slate-500 font-bold text-sm italic">
                    <span class="inline-block bg-slate-900 text-white px-2 py-0.5 rounded mr-2 not-italic text-[10px]">VIDEO</span>
                    練馬の店舗での車買取・無料査定の流れを解説（タップで再生/停止）
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">商用車の車買取・査定に特化した強み</h2>
        <p class="mt-4 text-slate-500 font-medium max-w-2xl mx-auto">一般的な買取店では評価しづらい中古商用車の価値を、独自の販路と知識で高価買取につなげます。</p>
    </div>
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">車買取の即日現金化・振込</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    書類が揃っていれば、最短即日の決済・お振込が可能です。急な資金調達や中古車の入替えにも柔軟に対応し、スムーズな売却をサポートします。
                </p>
            </div>
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">過走行・旧式の中古車査定</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    「走行20万キロ超え」「10年以上前のモデル」でも諦めないでください。商用車専門の販路を持つ当店なら、他店の買取相場以上の高額査定が期待できます。
                </p>
            </div>
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">車の下取りでコスト軽減</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    お乗り換え時の下取りなら、さらに査定額をプラス。中古車の購入と売却をワンストップで行うことで、トータルの買換えコストを大幅に抑えられます。
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 高価買取の理由 -->
<section class="py-24 bg-slate-950 text-white border-y border-slate-800 overflow-hidden">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- 左側：店舗画像 -->
            <div class="w-full lg:w-1/2 relative group">
                <div class="absolute -inset-4 bg-white/5 rounded-[2rem] blur-2xl group-hover:bg-white/10 transition-all"></div>
                <img src="/images/lp/Image_of_a_car_showroom.png" alt="練馬区の車買取専門店5R3のショールーム風景 | 清潔な店舗で中古車査定の相談が可能" class="relative z-10 rounded-3xl shadow-2xl border border-white/10 object-cover w-full h-[400px]">
            </div>
            <!-- 右側：コンテンツ -->
            <div class="w-full lg:w-1/2">
                <h2 class="text-3xl md:text-4xl font-black tracking-tight mb-6 text-left">中古車・商用車の高価買取に強い理由</h2>
                <p class="text-slate-200 font-medium mb-8 text-left leading-relaxed">
                    車買取・車査定の専門店だからこそ、一般の買取店では評価しきれない「仕事用の装備」をプラス査定。練馬の自社店舗で直接販売するルートがあるため、中間マージンをカットした高額提示が可能です。
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <?php
                    $items = ['カーナビ', 'ETC', 'バックカメラ', 'ドラレコ', '棚・架装', 'ルーフキャリア', 'シートカバー', 'スタッドレス'];
                    foreach ($items as $item): ?>
                        <div class="bg-white/5 border border-white/10 py-3 rounded-xl font-bold text-xs tracking-widest text-slate-200 text-center hover:bg-white/10 transition-colors">
                            <?= h($item) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="mt-8 text-slate-400 text-xs font-medium italic text-left">※車査定時に動作確認が取れるものに限ります</p>
            </div>
        </div>
    </div>
</section>

<!-- ご用意いただく書類 -->
<section id="docs" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row-reverse items-center gap-12 mb-16">
            <!-- 右側：店舗画像2 -->
            <div class="w-full lg:w-1/2">
                <img src="/images/lp/Image_of_a_car_showroom2.png" alt="5R3の商用車・中古車買取相談カウンター | 練馬区での車を売る際の手続きをサポート" class="rounded-[2.5rem] shadow-xl border border-slate-100 object-cover w-full h-[350px]">
            </div>
            <!-- 左側：コンテンツ -->
            <div class="w-full lg:w-1/2 text-left">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-6">車を売る際にご用意いただく書類</h2>
                <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                    スムーズな車買取のため、事前に揃えていただければ当日決済が可能です。書類の書き方や紛失時の対応も練馬の店舗スタッフが丁寧にご説明いたします。
                </p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- 個人 -->
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <h3 class="text-lg font-bold mb-4 flex items-center text-slate-900">
                            <span class="bg-slate-900 text-white w-7 h-7 rounded-lg flex items-center justify-center mr-3 text-xs font-black">個</span>
                            個人の場合
                        </h3>
                        <ul class="space-y-3">
                            <?php foreach (['印鑑証明', '実印', '車庫証明', '本人確認書類'] as $doc): ?>
                                <li class="flex items-center text-xs font-bold text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?= h($doc) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- 法人 -->
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <h3 class="text-lg font-bold mb-4 flex items-center text-slate-900">
                            <span class="bg-slate-900 text-white w-7 h-7 rounded-lg flex items-center justify-center mr-3 text-xs font-black">法</span>
                            法人の場合
                        </h3>
                        <ul class="space-y-3">
                            <?php foreach (['登記簿謄本', '印鑑証明', '代表者身分証'] as $doc): ?>
                                <li class="flex items-center text-xs font-bold text-slate-700">
                                    <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?= h($doc) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <p class="inline-block bg-slate-100 text-slate-700 px-8 py-4 rounded-full font-bold text-sm border border-slate-200 shadow-sm">
                「事前に揃えていただければ、当日納車・決済が可能です」
            </p>
        </div>
    </div>
</section>

<!-- CTA ボタン -->
<div class="py-24 text-center bg-white">
    <a href="#contact"
        class="inline-flex items-center space-x-2 bg-metallic text-slate-900 px-10 py-5 rounded-full text-lg font-black hover:scale-105 transition-all shadow-xl shadow-black/10 group border border-white/30">
        <span>無料査定を申し込む</span>
        <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </a>
</div>

<!-- Final CTA -->
<section id="contact" class="py-24 bg-white">
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
                    車買取・無料査定のご依頼、<br>まずはお気軽にご相談ください
                </h2>
                <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto font-medium">
                    車種・年式・走行距離をお教えいただければ、練馬の専門スタッフが無料査定額をすぐにご案内します。過走行・旧式の中古車もお気軽にどうぞ。
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="tel:<?= str_replace('-', '', SITE_PHONE) ?>"
                        class="bg-white text-slate-900 p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl">
                        <div class="bg-slate-50 p-4 rounded-2xl mb-4 group-hover:bg-slate-100 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 mb-1 uppercase tracking-widest">Phone</span>
                        <span class="text-2xl font-black"><?= SITE_PHONE ?></span>
                        <span class="text-[10px] text-slate-400 mt-2 font-bold"><?= SITE_HOURS ?> 受付</span>
                    </a>

                    <a href="<?= LINK_LINE ?>"
                        class="bg-[#06C755] text-white p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl">
                        <div class="bg-white/10 p-4 rounded-2xl mb-4 group-hover:bg-white/20 transition-colors">
                            <img src="/images/line-icon.png" alt="LINE" class="w-12 h-12 rounded-xl">
                        </div>
                        <span class="text-xs font-bold text-white/70 mb-1 uppercase tracking-widest">LINE</span>
                        <span class="text-2xl font-black italic">LINEで相談</span>
                        <span class="text-[10px] text-white/70 mt-2 font-bold">24時間メッセージ送信OK</span>
                    </a>

                    <a href="mailto:<?= SITE_EMAIL ?>"
                        class="bg-metallic text-slate-900 p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl border border-white/40">
                        <div class="bg-white/20 p-4 rounded-2xl mb-4 group-hover:bg-white/30 transition-colors">
                            <svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-slate-800/50 mb-1 uppercase tracking-widest">Mail</span>
                        <span class="text-2xl font-black">相談フォーム</span>
                        <span class="text-[10px] text-slate-800/50 mt-2 font-bold">担当者より1時間以内に返信</span>
                    </a>
                </div>

                <div
                    class="mt-12 flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-8 text-sm font-bold opacity-40">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        無料査定・最短即日現金化
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        過走行・旧式車も高額査定
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php'; ?>