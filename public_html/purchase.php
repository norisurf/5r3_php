<?php
declare(strict_types=1);
/**
 * 買取ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '車両買取・下取り - ' . SITE_NAME;
$pageDescription = 'ハイエース・キャラバンなど商用車の買取査定は5R3 CARSへ。過走行・旧式車もOK。最短即日現金化可能。';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Purchase Hero -->
<section class="relative pt-20 pb-16 md:pt-32 md:pb-24 bg-slate-950 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-full h-full opacity-10 pointer-events-none">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at center, #ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 bg-white text-slate-950 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>VEHICLE PURCHASE</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black mb-8 leading-tight tracking-tighter">
                その価値、<br>
                <span class="text-slate-400">プロの目で正当に評価。</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl font-medium leading-relaxed">
                ハイエース・キャラバンなど商用車の市場価格を熟知した専門スタッフが査定。古いお車や過走行車も、ビジネス車両としての価値を最大限に見出します。
            </p>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">即日現金化・お振込</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    書類が揃っていれば、最短即日の決済・お振込が可能です。急な資金調達や入替えにも柔軟に対応いたします。
                </p>
            </div>
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">過走行・旧式車OK</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    「走行20万キロ超え」「10年以上前のモデル」でも、商用車専門の販路を持つ当店なら、高価買取が期待できます。
                </p>
            </div>
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl transition-all">
                <div class="bg-slate-900 text-white w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <h3 class="text-xl font-bold mb-4 text-slate-900">下取り差額を軽減</h3>
                <p class="text-slate-500 text-sm leading-relaxed font-medium">
                    お乗り換え時の下取りなら、さらに査定額をプラス。購入と売却をワンストップで行うことで、トータルのコストを抑えられます。
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 高価買取の理由 -->
<section class="py-24 bg-slate-950 text-white border-y border-slate-800">
    <div class="container mx-auto px-4 text-center">
        <div class="mb-12">
            <h2 class="text-3xl md:text-4xl font-black tracking-tight">高価買取の理由</h2>
            <p class="mt-4 text-slate-400 font-medium max-w-2xl mx-auto">商用車専門だからこそ、一般の買取店では評価しきれない「装備」や「状態」をプラス査定します。</p>
        </div>
        <div class="max-w-4xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 mt-12">
            <?php
            $items = ['カーナビ', 'ETC', 'バックカメラ', 'ドラレコ', '棚・架装', 'ルーフキャリア', 'シートカバー', 'スタッドレス'];
            foreach ($items as $item): ?>
            <div class="bg-white/5 border border-white/10 py-4 rounded-xl font-bold text-sm tracking-widest text-slate-400"><?= h($item) ?></div>
            <?php endforeach; ?>
        </div>
        <p class="mt-12 text-slate-500 text-sm font-medium italic">※動作確認が取れるものに限ります</p>
    </div>
</section>

<!-- ご用意いただく書類 -->
<section id="docs" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">ご用意いただく書類</h2>
            <p class="mt-4 text-slate-500 font-medium max-w-2xl mx-auto">事前に揃えていただければ、当日納車が可能です。必要に応じて代行も承ります。</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- 個人 -->
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
                <h3 class="text-xl font-bold mb-6 flex items-center text-slate-900">
                    <span class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">個</span>
                    個人の場合
                </h3>
                <ul class="space-y-4">
                    <?php foreach (['印鑑証明', '実印', '車庫証明', '本人確認書類'] as $doc): ?>
                    <li class="flex items-center text-sm font-bold text-slate-700">
                        <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= h($doc) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- 法人 -->
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
                <h3 class="text-xl font-bold mb-6 flex items-center text-slate-900">
                    <span class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center mr-3 text-sm">法</span>
                    法人の場合
                </h3>
                <ul class="space-y-4">
                    <?php foreach (['登記簿謄本', '印鑑証明', '代表者身分証'] as $doc): ?>
                    <li class="flex items-center text-sm font-bold text-slate-700">
                        <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= h($doc) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="mt-12 text-center">
            <p class="inline-block bg-slate-100 text-slate-700 px-6 py-3 rounded-full font-bold text-sm border border-slate-200 shadow-sm">
                「事前に揃えていただければ、当日納車が可能です」
            </p>
        </div>
    </div>
</section>

<!-- CTA ボタン -->
<div class="py-24 text-center bg-white">
    <a href="#contact" class="inline-flex items-center space-x-2 bg-metallic text-slate-900 px-10 py-5 rounded-full text-lg font-black hover:scale-105 transition-all shadow-xl shadow-black/10 group border border-white/30">
        <span>無料査定を申し込む</span>
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

<?php
declare(strict_types=1); require_once __DIR__ . '/includes/footer.php'; ?>
