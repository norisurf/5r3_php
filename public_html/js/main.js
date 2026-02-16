/**
 * 5R3 CARS - メインJS
 * モバイルメニュー、スクロールアニメーション、Sticky CTA
 */

document.addEventListener('DOMContentLoaded', function() {
    // --- モバイルメニュー ---
    const menuBtn = document.getElementById('menu-btn');
    const menuClose = document.getElementById('menu-close');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    if (menuClose && mobileMenu) {
        menuClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    // メニュー内リンククリックで閉じる
    if (mobileMenu) {
        mobileMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }

    // --- スクロールアニメーション (IntersectionObserver) ---
    const fadeElements = document.querySelectorAll('.fade-up, .fade-right');
    if (fadeElements.length > 0 && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        fadeElements.forEach(function(el) {
            observer.observe(el);
        });
    } else {
        // フォールバック: 全て表示
        fadeElements.forEach(function(el) {
            el.classList.add('visible');
        });
    }

    // --- スクロールトップボタン ---
    const scrollTopBtn = document.getElementById('scroll-top-btn');
    if (scrollTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // --- 初期表示用のフェードイン (ヒーローセクション) ---
    // ページ上部のfade-upは即座に表示する
    document.querySelectorAll('.fade-up-immediate').forEach(function(el) {
        requestAnimationFrame(function() {
            el.classList.add('visible');
        });
    });
});
