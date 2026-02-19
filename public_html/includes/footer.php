    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-white pt-20 pb-10 border-t border-slate-900">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Company Info -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-2">
                        <img src="/images/5r3_rogo.png" alt="5R3 Logo" class="h-8 w-auto object-contain">
                        <span class="text-2xl font-black italic tracking-tighter">5R3</span>
                        <span class="text-sm"> ファイブ・アール・スリー株式会社</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed font-medium">
                        ワゴン・商用バン専門のプロフェッショナル集団。<br>
                        「最短当日納車」を掲げ、お客様のビジネスを加速させます。
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-base font-bold mb-6 border-l-4 border-slate-500 pl-3 uppercase tracking-widest text-slate-300">Content</h4>
                    <ul class="space-y-4 text-sm text-slate-400 font-bold">
                        <li><a href="/sales.php" class="hover:text-white transition-colors">中古車販売</a></li>
                        <li><a href="/purchase.php" class="hover:text-white transition-colors">買取・下取り</a></li>
                        <li><a href="/lp.php" class="hover:text-white transition-colors">即納の理由</a></li>
                        <li><a href="/#stock" class="hover:text-white transition-colors">在庫車両一覧</a></li>
                        <li><a href="/company.php" class="hover:text-white transition-colors">会社概要</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div>
                    <h4 class="text-base font-bold mb-6 border-l-4 border-slate-500 pl-3 uppercase tracking-widest text-slate-300">Service</h4>
                    <ul class="space-y-4 text-sm text-slate-400 font-bold">
                        <li><a href="/purchase.php" class="hover:text-white transition-colors">無料査定</a></li>
                        <li><a href="/#contact" class="hover:text-white transition-colors">よくある質問</a></li>
                        <li><a href="/#contact" class="hover:text-white transition-colors">全国陸送対応</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-base font-bold mb-6 border-l-4 border-slate-500 pl-3 uppercase tracking-widest text-slate-300">Contact</h4>
                    <ul class="space-y-5 text-sm">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-slate-400 font-medium"><?= defined('SITE_ADDRESS_STORAGE') ? SITE_ADDRESS_STORAGE : '' ?></span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-xl font-black text-slate-200"><?= SITE_PHONE ?></span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-slate-400 font-medium tracking-tight"><?= SITE_EMAIL ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/5 pt-10 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">&copy; 5R3. All Rights Reserved.</p>
                <div class="flex space-x-8 text-[10px] text-slate-500 font-black uppercase tracking-widest">
                    <a href="#" class="hover:text-slate-400 transition-colors">Privacy Policy</a>
                    <a href="/company.php" class="hover:text-slate-400 transition-colors">Company</a>
                    <a href="/admin/" class="hover:text-slate-400 transition-colors">Admin</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top -->
    <button id="scroll-top-btn" class="scroll-top-btn fixed bottom-24 right-4 md:bottom-28 md:right-8 z-50 bg-slate-800 hover:bg-slate-700 text-white p-4 rounded-full shadow-lg transition-all hover:scale-110 active:scale-95" aria-label="トップへ戻る">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <script src="/js/main.js"></script>

    <!-- LocalBusiness Schema.org 構造化データ -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "AutoDealer",
      "name": "ファイブ・アール・スリー株式会社",
      "alternateName": "5R3 CARS",
      "url": "https://5r3.co.jp",
      "logo": "https://5r3.co.jp/images/5r3_rogo.png",
      "image": "https://5r3.co.jp/images/5r3_rogo.png",
      "description": "<?= defined('SITE_DESCRIPTION') ? addslashes(SITE_DESCRIPTION) : '' ?>",
      "telephone": "<?= defined('SITE_PHONE') ? SITE_PHONE : '' ?>",
      "email": "<?= defined('SITE_EMAIL') ? SITE_EMAIL : '' ?>",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "東大泉2-20-5-B1F",
        "addressLocality": "練馬区",
        "addressRegion": "東京都",
        "postalCode": "178-0063",
        "addressCountry": "JP"
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
          "opens": "08:00",
          "closes": "20:00"
        }
      ],
      "priceRange": "¥¥",
      "paymentAccepted": "Cash, Credit Card",
      "currenciesAccepted": "JPY",
      "areaServed": {
        "@type": "Country",
        "name": "JP"
      }
    }
    </script>
    </body>

    </html>