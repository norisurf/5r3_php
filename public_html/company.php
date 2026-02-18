<?php
declare(strict_types=1);
/**
 * 会社概要ページ
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '会社概要 - ' . SITE_NAME;
$pageDescription = 'ファイブ・アール・スリー株式会社の会社概要。ワゴン・商用バン専門の中古車販売店です。';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Company Hero -->
<section class="relative pt-20 pb-16 md:pt-32 md:pb-24 bg-slate-900 text-white overflow-hidden">
    <div class="absolute top-0 right-0 w-1/2 h-full bg-slate-800/10 skew-x-12 translate-x-1/4"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center space-x-2 bg-metallic text-slate-900 px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>COMPANY</span>
            </div>
            <h2 class="text-4xl md:text-6xl font-black mb-8 leading-tight tracking-tighter">
                会社概要
            </h2>
            <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl font-medium leading-relaxed">
                ワゴン・商用バン専門のプロフェッショナル集団として、お客様のビジネスを全力でサポートいたします。
            </p>
        </div>
    </div>
</section>

<!-- Company Info -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-slate-50 rounded-3xl border border-slate-100 overflow-hidden">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">会社名</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium">ファイブ・アール・スリー株式会社</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">所在地</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium"><?= h(SITE_ADDRESS) ?></td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">展示場</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium"><?= h(SITE_ADDRESS_STORAGE) ?></td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">電話番号</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium">
                                <a href="tel:<?= str_replace('-', '', SITE_PHONE) ?>" class="hover:text-slate-900 transition-colors"><?= h(SITE_PHONE) ?></a>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">メール</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium">
                                <a href="mailto:<?= SITE_EMAIL ?>" class="hover:text-slate-900 transition-colors"><?= h(SITE_EMAIL) ?></a>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">営業時間</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium"><?= h(SITE_HOURS) ?></td>
                        </tr>
                        <tr>
                            <th class="text-left px-6 md:px-10 py-6 bg-slate-100 font-bold text-slate-700 w-1/3 align-top">古物商許可番号</th>
                            <td class="px-6 md:px-10 py-6 text-slate-700 font-medium">東京都公安委員会 第305582005324号</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto bg-slate-900 rounded-[3rem] p-8 md:p-16 text-center text-white relative overflow-hidden shadow-2xl border border-slate-800">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-800/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] translate-y-1/2 -translate-x-1/2"></div>

            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight">
                    お気軽にお問い合わせください
                </h2>
                <p class="text-lg md:text-xl text-slate-400 mb-12 max-w-2xl mx-auto font-medium">
                    車両のご相談・お見積もりなど、まずはお電話またはメールにてご連絡ください。
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                    <a href="tel:<?= str_replace('-', '', SITE_PHONE) ?>" class="bg-white text-slate-900 p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl">
                        <div class="bg-slate-50 p-4 rounded-2xl mb-4 group-hover:bg-slate-100 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 mb-1 uppercase tracking-widest">Phone</span>
                        <span class="text-2xl font-black"><?= SITE_PHONE ?></span>
                        <span class="text-[10px] text-slate-400 mt-2 font-bold"><?= SITE_HOURS ?> 受付</span>
                    </a>

                    <a href="mailto:<?= SITE_EMAIL ?>" class="bg-metallic text-slate-900 p-6 rounded-3xl group hover:scale-105 transition-transform flex flex-col items-center justify-center shadow-xl border border-white/40">
                        <div class="bg-white/20 p-4 rounded-2xl mb-4 group-hover:bg-white/30 transition-colors">
                            <svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-800/50 mb-1 uppercase tracking-widest">Mail</span>
                        <span class="text-2xl font-black">メールで相談</span>
                        <span class="text-[10px] text-slate-800/50 mt-2 font-bold">担当者より1時間以内に返信</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
