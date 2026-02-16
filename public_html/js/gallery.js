/**
 * 画像ギャラリー・ライトボックス
 */
document.addEventListener('DOMContentLoaded', function() {
    var mainImage = document.getElementById('main-image');
    var lightbox = document.getElementById('lightbox');
    var lightboxImg = document.getElementById('lightbox-img');
    var lightboxCounter = document.getElementById('lightbox-counter');
    var thumbnails = document.querySelectorAll('.gallery-thumb');
    var images = [];
    var currentIndex = 0;

    thumbnails.forEach(function(thumb, index) {
        images.push(thumb.dataset.src);
        thumb.addEventListener('click', function() {
            currentIndex = index;
            updateMainImage();
            updateThumbnails();
        });
    });

    function updateMainImage() {
        if (mainImage && images[currentIndex]) {
            mainImage.src = images[currentIndex];
        }
    }

    function updateThumbnails() {
        thumbnails.forEach(function(thumb, index) {
            if (index === currentIndex) {
                thumb.classList.add('border-yellow-400', 'ring-2', 'ring-yellow-400/20');
                thumb.classList.remove('border-transparent');
            } else {
                thumb.classList.remove('border-yellow-400', 'ring-2', 'ring-yellow-400/20');
                thumb.classList.add('border-transparent');
            }
        });
    }

    // ライトボックス開く
    if (mainImage) {
        mainImage.parentElement.addEventListener('click', function() {
            openLightbox();
        });
    }

    function openLightbox() {
        if (!lightbox) return;
        lightbox.classList.add('active');
        updateLightboxImage();
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLightboxImage() {
        if (lightboxImg && images[currentIndex]) {
            lightboxImg.src = images[currentIndex];
        }
        if (lightboxCounter) {
            lightboxCounter.textContent = (currentIndex + 1) + ' / ' + images.length;
        }
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        updateMainImage();
        updateThumbnails();
        updateLightboxImage();
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateMainImage();
        updateThumbnails();
        updateLightboxImage();
    }

    // ライトボックスボタン
    var closeBtn = document.getElementById('lightbox-close');
    var prevBtn = document.getElementById('lightbox-prev');
    var nextBtn = document.getElementById('lightbox-next');

    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
    if (prevBtn) prevBtn.addEventListener('click', function(e) { e.stopPropagation(); prevImage(); });
    if (nextBtn) nextBtn.addEventListener('click', function(e) { e.stopPropagation(); nextImage(); });

    // キーボード操作
    document.addEventListener('keydown', function(e) {
        if (!lightbox || !lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });
});
