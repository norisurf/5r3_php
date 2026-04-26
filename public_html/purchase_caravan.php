<?php
declare(strict_types=1);
/**
 * キャラバン買取専門ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// キャラバンの販売実績データを取得
$db = getDB();
$stmt = $db->query("SELECT * FROM vehicles WHERE deleted_at IS NOT NULL AND title LIKE '%キャラバン%' ORDER BY updated_at DESC LIMIT 8");
$sold_caravans = $stmt->fetchAll();

$pageTitle = '日産キャラバン買取査定専門店！高額買取の理由と販売実績 | 中古車なら5R3';
$pageDescription = '練馬区で日産キャラバンを売るなら5R3へ。NV350キャラバン、E25、E26などの中古車買取・車査定を強化中。過走行・旧式車・仕事用の架装車も高額査定。キャラバン専門の販売ルートがあるため相場以上の買取を目指します。';
$pageCanonicalUrl = 'https://5r3.co.jp/purchase_caravan.php';
// LLMO・SEO向けの構造化データ (FAQ & Video)
$pageCustomSchema = '
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "VideoObject",
      "name": "日産キャラバンの車買取・車査定 | 商用車を高価買取する5R3",
      "description": "練馬区でキャラバンを売るなら5R3へ。過走行・旧式のキャラバンも高額査定。中古車の下取り相場以上を目指す専門店ならではの査定プロセス。",
      "thumbnailUrl": [
        "https://5r3.co.jp/images/lp/Image_of_a_car_showroom_top.png"
      ],
      "uploadDate": "2026-03-13T10:00:00+09:00",
      "duration": "PT1M15S",
      "embedUrl": "https://5r3.co.jp/purchase_caravan.php",
      "interactionStatistic": {
        "@type": "InteractionCounter",
        "interactionType": { "@type": "ReadAction" },
        "userInteractionCount": 1024
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [{
        "@type": "Question",
        "name": "走行距離が20万キロを超えているキャラバンでも買取可能ですか？",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "はい、可能です。日産キャラバンは耐久性が高く、海外や国内の職人層から需要が大きいため、過走行車でもしっかりと価格が付きます。"
        }
      }, {
        "@type": "Question",
        "name": "車内に仕事用の棚やラックが付いていますが、そのまま査定できますか？",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "もちろんです。商用車専門の5R3では、棚やルーフキャリアなどの仕事用装備をプラス査定の対象として評価いたします。"
        }
      }]
    }
  ]
}
</script>';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Desktop Hero (Video side-by-side like index.php) -->
<section class="relative pt-24 pb-20 lg:pt-32 lg:pb-32 bg-slate-950 text-white overflow-hidden hidden lg:block">
    <!-- Background Image with Overlay for SEO & Visual Trust -->
    <div class="absolute inset-0 z-0 bg-slate-950">
        <img src="/images/lp/IMG_0530.jpg" alt="日産キャラバンの中古車買取・車査定なら5R3 | NV350在庫風景" class="w-full h-full object-cover opacity-20 scale-105 brightness-75">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/80 to-slate-950/30"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-row items-center gap-16">
            <div class="flex-1 text-left">
                <div class="inline-flex items-center space-x-2 bg-white text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>NISSAN CARAVAN PURCHASE</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-8 leading-tight tracking-tighter">
                    日産キャラバンの買取査定は5R3。<br>
                    <span class="text-red-600">プロの目でキャラバンを最高評価。</span>
                </h1>
                <p class="text-lg text-slate-300 mb-10 max-w-xl font-medium leading-relaxed">
                    NV350キャラバン、E25、E26など、キャラバンの市場相場を熟知した専門スタッフが無料査定。専門店ならではの直販ルートがあるからこそ、一般店では評価されない装備や過走行のキャラバンでも高価買取を実現します。
                </p>
            </div>
            
            <!-- Video (Desktop) -->
            <div class="flex-1 w-full relative">
                <div class="relative rounded-[2rem] overflow-hidden shadow-2xl border-4 border-slate-800 aspect-[4/3] bg-black">
                    <video 
                        id="purchaseVideoDesktop"
                        src="/video/buy_2026-04-26.mp4" 
                        poster="/images/lp/Image_of_a_car_showroom_top.png"
                        class="w-full h-full object-cover"
                        autoplay 
                        loop 
                        muted 
                        playsinline
                    ></video>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Hero -->
<section class="relative pt-20 pb-12 bg-slate-950 text-white overflow-hidden lg:hidden">
    <div class="absolute inset-0 z-0 bg-slate-950">
        <img src="/images/lp/IMG_0530.jpg" alt="日産キャラバンの中古車買取・車査定なら5R3 | NV350在庫風景" class="w-full h-full object-cover opacity-20 scale-105 brightness-75">
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/95 via-slate-950/80 to-slate-950"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl text-center">
            <div class="inline-flex items-center space-x-2 bg-white text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>NISSAN CARAVAN PURCHASE</span>
            </div>
            <h1 class="text-3xl font-black mb-6 leading-tight tracking-tighter">
                日産キャラバンの買取査定は5R3。<br>
                <span class="text-red-600 block mt-2">プロの目でキャラバンを最高評価。</span>
            </h1>
            <p class="text-sm text-slate-300 mb-8 max-w-2xl font-medium leading-relaxed text-left">
                NV350キャラバン、E25、E26など、キャラバンの市場相場を熟知した専門スタッフが無料査定。専門店ならではの直販ルートがあるからこそ、一般店では評価されない装備や過走行のキャラバンでも高価買取を実現します。
            </p>
            
            <!-- Video (Mobile) -->
            <div class="w-full relative mx-auto mb-4">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-800 aspect-[4/3] bg-black">
                    <video 
                        id="purchaseVideoMobile"
                        src="/video/buy_2026-04-26.mp4" 
                        poster="/images/lp/Image_of_a_car_showroom_top.png"
                        class="w-full h-full object-cover"
                        autoplay 
                        loop 
                        muted 
                        playsinline
                    ></video>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- キャラバン 買取の強み -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">キャラバンの買取・査定に特化した強み</h2>
        <p class="mt-4 text-slate-500 font-medium max-w-2xl mx-auto">キャラバンのポテンシャルを最大限に評価し、独自の販売ルートで高額な買取額を提示します。</p>
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
                <h3 class="text-xl font-bold mb-4 text-slate-900">仕事用装備の徹底プラス査定</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    職人さん仕様の棚、ルーフキャリア、特注の床張りなど、一般店ではマイナスになりがちなオプションも、次に必要とする方へのアピール材料として大きくプラス査定します。
                </p>
            </div>
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">過走行・旧型キャラバン歓迎</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    20万キロ超えやE25等の旧型キャラバンでも心配無用。キャラバンのタフな耐久性を求めている独自の国内外ルートがあるため、他社で断られた車両でも買取実績が豊富です。
                </p>
            </div>
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">即日現金化・乗り換えサポート</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    日産キャラバンから新しいキャラバンや他車種への乗り換えもスムーズ。買取による資金調達を最短即日で対応し、下取りと購入のワンストップでビジネスを止めません。
                </p>
            </div>
        </div>
    </div>
</section>

<!-- キャラバンの買取実績 (SOLD ITEMS) -->
<section class="py-24 bg-slate-50 border-y border-slate-100">
    <div class="container mx-auto px-4">
        <!-- Section Title -->
        <div class="text-center mb-16">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="h-px w-12 bg-metallic"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Caravan Purchase Record</span>
                <div class="h-px w-12 bg-metallic"></div>
            </div>
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">日産キャラバン 買取実績の一部</h2>
            <p class="mt-4 text-slate-500 font-medium text-sm max-w-xl mx-auto">
                5R3では数多くのキャラバンを買い取り、次のお客様へお届けしてまいりました。独自のネットワークによりキャラバンの高額買取を実現しています。
            </p>
        </div>

        <!-- Vehicle Grid (Purchase Record) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <?php if (empty($sold_caravans)): ?>
                <div class="col-span-full py-10 text-center text-slate-400">現在、表示できるキャラバンの買取実績データはありません。</div>
            <?php else: ?>
                <?php foreach ($sold_caravans as $v):
                    $imgs     = jsonDecode($v['images']);
                    $imgSrc   = !empty($imgs) ? $imgs[0] : '/images/placeholder.png';
                    $basicInfo = jsonDecode($v['basic_info']);
                    $dPrice   = displayPriceMan($v['price']);
                    $cTitle   = cleanTitle($v['title']);
                ?>
                    <a href="/stock.php?id=<?= h($v['id']) ?>"
                       class="vehicle-card bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-xl transition-all group flex flex-col">
                        <div class="relative aspect-[16/10] bg-gray-100 overflow-hidden">
                            <img src="<?= h($imgSrc) ?>" alt="<?= h($cTitle) ?>買取実績"
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
                            <!-- SOLD OUT バッジ -->
                            <div class="absolute top-2 right-2 bg-black text-white text-xs font-bold px-2 py-1 rounded z-10 tracking-widest">SOLD OUT</div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent">
                                <span class="text-white text-[10px] font-black uppercase tracking-widest opacity-80">No. <?= h($v['manage_number']) ?></span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-base font-bold mb-4 group-hover:text-slate-700 transition-colors text-slate-600 leading-snug tracking-tight break-words min-h-[3rem]"><?= h($cTitle) ?></h3>
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
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center">
            <a href="/sold.php" class="inline-block bg-slate-900 text-white px-8 py-3 rounded-full font-bold text-sm hover:bg-slate-800 transition-colors shadow-lg">すべての買取実績を見る</a>
        </div>
    </div>
</section>

<!-- LLMO/SEO向けのよくある質問 (FAQ) -->
<section class="py-24 bg-white border-y border-slate-100">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">日産キャラバン買取におけるよくある質問</h2>
            <p class="mt-4 text-slate-500 font-medium">査定をお考えのお客様からお問い合わせいただく内容をまとめました。</p>
        </div>

        <div class="space-y-6">
            <!-- FAQ 1 -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-lg font-bold text-slate-900 flex items-start mb-3">
                    <span class="text-metallic mr-2 lg:text-xl font-black">Q.</span>
                    走行距離が20万キロを超えているキャラバンでも買取可能ですか？
                </h3>
                <p class="text-slate-600 font-medium pl-8">
                    <span class="text-slate-400 font-black mr-2">A.</span>
                    はい、可能です。日産キャラバンは耐久性が高く、海外や国内の職人層から常に需要が大きいため、過走行車であってもしっかりと価格が付きます。
                </p>
            </div>
            <!-- FAQ 2 -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                <h3 class="text-lg font-bold text-slate-900 flex items-start mb-3">
                    <span class="text-metallic mr-2 lg:text-xl font-black">Q.</span>
                    車内に仕事用の棚やラックが付いていますが、そのまま査定できますか？
                </h3>
                <p class="text-slate-600 font-medium pl-8">
                    <span class="text-slate-400 font-black mr-2">A.</span>
                    もちろんです。商用車専門の5R3では、棚やルーフキャリア、特殊な床張りなどの仕事用装備を「次の方に役立つ付加価値」と考え、プラス査定の対象として評価いたします。撤去せずにそのままお見せください。
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section id="contact" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div
            class="max-w-5xl mx-auto bg-slate-900 rounded-[3rem] p-8 md:p-16 text-center text-white relative overflow-hidden shadow-2xl border border-slate-800">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-800/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/2"></div>

            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight">
                    キャラバンの買取・無料査定、<br>まずはお気軽にご相談ください
                </h2>
                <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto font-medium">
                    年式・走行距離・架装の状態をお教えいただければ、キャラバン専門のスタッフが概算の査定額をすぐにご案内します。
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

                <div class="mt-12 flex flex-col md:flex-row items-center justify-center space-y-4 md:space-y-0 md:space-x-8 text-sm font-bold opacity-40">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        キャラバン無料査定・最短即日現金化
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        NV350・E25も高額査定
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- スマホ固定電話ボタン用スペーサー -->
<div class="md:hidden h-28"></div>

<!-- スマホ固定電話ボタン -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-50 p-4">
    <a href="tel:0339222226" class="w-full flex flex-col items-center justify-center bg-red-600 text-white px-10 py-5 rounded-full shadow-2xl shadow-red-600/30 hover:bg-red-700 transition-all active:scale-95 border-4 border-white ring-4 ring-red-100">
        <div class="flex items-center text-2xl font-black">
            <svg class="w-8 h-8 mr-3 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            03-3922-2226
        </div>
        <span class="text-sm font-bold opacity-90 mt-1">8:00 - 20:00 ｜ 査定だけでもOK</span>
    </a>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
