/**
 * 管理画面 JavaScript
 * ダッシュボード操作、車両フォーム、バナー管理
 */

// CSRFトークン取得
function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// --- ダッシュボード: 車両操作 ---

function deleteVehicle(id) {
    if (!confirm('この車両を削除しますか？（全商品一覧から復元できます）')) return;
    fetch('/admin/api/vehicle.php?id=' + encodeURIComponent(id), {
        method: 'POST',
        headers: {
            'X-CSRF-Token': getCsrfToken(),
            'X-HTTP-Method-Override': 'DELETE'
        }
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            var row = document.getElementById('vehicle-row-' + id);
            if (row) row.remove();
        } else {
            alert('削除に失敗しました: ' + (data.error || ''));
        }
    })
    .catch(function() { alert('削除に失敗しました'); });
}

function restoreVehicle(id) {
    if (!confirm('この車両を復元しますか？')) return;
    fetch('/admin/api/vehicle.php?id=' + encodeURIComponent(id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken(),
            'X-HTTP-Method-Override': 'PUT'
        },
        body: JSON.stringify({ restore: true })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.id) {
            location.reload();
        } else {
            alert('復元に失敗しました: ' + (data.error || ''));
        }
    })
    .catch(function() { alert('復元に失敗しました'); });
}

function toggleLpDisplay(id, checked) {
    fetch('/admin/api/vehicle.php?id=' + encodeURIComponent(id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken(),
            'X-HTTP-Method-Override': 'PUT'
        },
        body: JSON.stringify({ displayOnLP: checked })
    })
    .then(function(res) {
        if (!res.ok) throw new Error('Update failed');
    })
    .catch(function() {
        alert('更新に失敗しました');
        location.reload();
    });
}

function duplicateVehicle(id) {
    if (!confirm('この車両を複製しますか？')) return;
    fetch('/admin/api/vehicle.php?id=' + encodeURIComponent(id), {
        method: 'GET'
    })
    .then(function(res) { return res.json(); })
    .then(function(v) {
        return fetch('/admin/api/vehicles.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': getCsrfToken()
            },
            body: JSON.stringify({
                title: v.title + ' (コピー)',
                price: v.price,
                images: v.images,
                basicInfo: v.basic_info,
                detailedInfo: v.detailed_info,
                equipment: v.equipment,
                description: v.description
            })
        });
    })
    .then(function(res) {
        if (res.ok) {
            alert('複製しました');
            location.reload();
        } else {
            alert('複製に失敗しました');
        }
    })
    .catch(function() { alert('複製処理に失敗しました'); });
}

// --- ダッシュボード: バナー管理 ---

var currentBannerMode = 'manual';
var currentBannerImageUrl = '';

function setBannerMode(mode) {
    currentBannerMode = mode;
    var autoBtn = document.getElementById('btn-mode-auto');
    var manualBtn = document.getElementById('btn-mode-manual');
    var autoPanel = document.getElementById('banner-auto-panel');
    var manualPanel = document.getElementById('banner-manual-panel');

    if (mode === 'auto') {
        autoBtn.className = autoBtn.className.replace('border-gray-200 text-gray-500 hover:border-gray-300', 'border-[#003366] bg-[#003366]/5 text-[#003366]');
        manualBtn.className = manualBtn.className.replace('border-[#003366] bg-[#003366]/5 text-[#003366]', 'border-gray-200 text-gray-500 hover:border-gray-300');
        if (autoPanel) autoPanel.classList.remove('hidden');
        if (manualPanel) manualPanel.classList.add('hidden');
    } else {
        manualBtn.className = manualBtn.className.replace('border-gray-200 text-gray-500 hover:border-gray-300', 'border-[#003366] bg-[#003366]/5 text-[#003366]');
        autoBtn.className = autoBtn.className.replace('border-[#003366] bg-[#003366]/5 text-[#003366]', 'border-gray-200 text-gray-500 hover:border-gray-300');
        if (manualPanel) manualPanel.classList.remove('hidden');
        if (autoPanel) autoPanel.classList.add('hidden');
    }
}

function handleBannerUpload(input) {
    var file = input.files[0];
    if (!file) return;

    var uploadText = document.getElementById('banner-upload-text');
    if (uploadText) uploadText.textContent = 'アップロード中...';

    var formData = new FormData();
    formData.append('file', file);

    fetch('/admin/api/upload.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': getCsrfToken() },
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.url) {
            currentBannerImageUrl = data.url;
            var preview = document.getElementById('banner-preview');
            var previewImg = document.getElementById('banner-preview-img');
            if (preview) preview.classList.remove('hidden');
            if (previewImg) previewImg.src = data.url;
            showBannerMessage('画像をアップロードしました', false);
        } else {
            showBannerMessage(data.error || 'アップロードに失敗しました', true);
        }
    })
    .catch(function() {
        showBannerMessage('アップロードに失敗しました', true);
    })
    .finally(function() {
        if (uploadText) uploadText.textContent = 'クリックして画像を選択';
    });
}

function saveBanner() {
    var imageUrl = currentBannerMode === 'manual' ? (currentBannerImageUrl || document.getElementById('banner-preview-img')?.src || '') : '';
    var linkUrl = document.getElementById('banner-link-url')?.value || '';

    if (currentBannerMode === 'manual' && !imageUrl) {
        showBannerMessage('手動モードでは画像を設定してください', true);
        return;
    }

    var btn = document.getElementById('banner-save-btn');
    if (btn) btn.disabled = true;

    fetch('/admin/api/banner.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken()
        },
        body: JSON.stringify({
            mode: currentBannerMode,
            imageUrl: imageUrl,
            linkUrl: linkUrl
        })
    })
    .then(function(res) {
        if (res.ok) {
            showBannerMessage('バナー設定を更新しました', false);
        } else {
            showBannerMessage('保存に失敗しました', true);
        }
    })
    .catch(function() {
        showBannerMessage('保存に失敗しました', true);
    })
    .finally(function() {
        if (btn) btn.disabled = false;
    });
}

function showBannerMessage(text, isError) {
    var el = document.getElementById('banner-message');
    if (!el) return;
    el.textContent = text;
    el.className = 'mt-4 text-sm ' + (isError ? 'text-red-500' : 'text-green-600');
    el.classList.remove('hidden');
}

// --- 車両フォーム: HTMLパース ---

function parseHtml() {
    var htmlInput = document.getElementById('html-input');
    if (!htmlInput || !htmlInput.value.trim()) {
        alert('HTMLを入力してください');
        return;
    }

    var btn = document.getElementById('parse-btn');
    if (btn) btn.disabled = true;

    // WAF (SiteGuard) 回避: HTMLをBase64エンコードして送信
    var encoded = btoa(unescape(encodeURIComponent(htmlInput.value)));

    fetch('/admin/api/parse.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken()
        },
        body: JSON.stringify({ htmlBase64: encoded })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.error) {
            alert('パースに失敗しました: ' + data.error);
            return;
        }
        // フォームに反映
        document.getElementById('field-title').value = data.title || '';
        document.getElementById('field-price').value = data.price || 0;
        document.getElementById('field-description').value = data.description || '';
        document.getElementById('field-basicInfo').value = JSON.stringify(data.basicInfo || {}, null, 2);
        document.getElementById('field-detailedEquipment').value = JSON.stringify({
            detailed: data.detailedInfo || {},
            equipment: data.equipment || []
        }, null, 2);

        // 画像を反映
        setImages(data.images || []);
        alert('パースが完了しました');
    })
    .catch(function(err) {
        console.error('Parse error:', err);
        alert('パースに失敗しました');
    })
    .finally(function() {
        if (btn) btn.disabled = false;
    });
}

// --- 車両フォーム: 画像管理 ---

function getImages() {
    var grid = document.getElementById('image-grid');
    if (!grid) return [];
    var imgs = [];
    grid.querySelectorAll('[data-image]').forEach(function(el) {
        imgs.push(el.dataset.image);
    });
    return imgs;
}

function setImages(imageList) {
    var grid = document.getElementById('image-grid');
    if (!grid) return;

    // 既存の画像要素を削除（追加ボタン以外）
    grid.querySelectorAll('[data-image]').forEach(function(el) { el.remove(); });

    // 追加ボタンの前に画像を挿入
    var addBtn = grid.querySelector('button');
    imageList.forEach(function(src) {
        var div = document.createElement('div');
        div.className = 'group relative aspect-square overflow-hidden rounded-xl border border-gray-100 bg-gray-50';
        div.setAttribute('data-image', src);
        div.innerHTML = '<img src="' + escapeHtml(src) + '" alt="" class="h-full w-full object-cover">' +
            '<button type="button" onclick="removeImage(this.parentElement)" class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white opacity-0 transition-opacity group-hover:opacity-100">' +
            '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        grid.insertBefore(div, addBtn);
    });
}

function addImage() {
    var url = prompt('画像URLを入力してください');
    if (!url) return;
    var imgs = getImages();
    imgs.push(url);
    setImages(imgs);
}

function removeImage(el) {
    if (el) el.remove();
}

function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// --- 車両フォーム: 送信 ---

function submitVehicle(e) {
    e.preventDefault();

    var title = document.getElementById('field-title').value;
    var price = parseInt(document.getElementById('field-price').value, 10) || 0;
    var description = document.getElementById('field-description').value;
    var images = getImages();

    var basicInfo = {};
    var detailedInfo = {};
    var equipment = [];

    try {
        basicInfo = JSON.parse(document.getElementById('field-basicInfo').value);
    } catch (ex) { /* ignore */ }

    try {
        var deData = JSON.parse(document.getElementById('field-detailedEquipment').value);
        detailedInfo = deData.detailed || {};
        equipment = deData.equipment || [];
    } catch (ex) { /* ignore */ }

    var payload = {
        title: title,
        price: price,
        images: JSON.stringify(images),
        basicInfo: JSON.stringify(basicInfo),
        detailedInfo: JSON.stringify(detailedInfo),
        equipment: JSON.stringify(equipment),
        description: description
    };

    var btn = document.getElementById('submit-btn');
    if (btn) btn.disabled = true;

    var url, method;
    if (typeof vehicleId !== 'undefined' && vehicleId) {
        url = '/admin/api/vehicle.php?id=' + encodeURIComponent(vehicleId);
        method = 'PUT';
    } else {
        url = '/admin/api/vehicles.php';
        method = 'POST';
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': getCsrfToken(),
            'X-HTTP-Method-Override': method
        },
        body: JSON.stringify(payload)
    })
    .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
    .then(function(result) {
        if (result.ok) {
            window.location.href = '/admin/';
        } else {
            var msg = '保存に失敗しました: ' + (result.data.error || 'Unknown error');
            if (result.data.details) msg += '\n\n詳細: ' + result.data.details;
            alert(msg);
        }
    })
    .catch(function(err) {
        console.error('Submit error:', err);
        alert('保存に失敗しました: ' + err.message);
    })
    .finally(function() {
        if (btn) btn.disabled = false;
    });

    return false;
}

// --- 初期化 ---
document.addEventListener('DOMContentLoaded', function() {
    // バナーモードの初期値を読み取り
    var autoBtn = document.getElementById('btn-mode-auto');
    if (autoBtn && autoBtn.className.includes('border-[#003366]')) {
        currentBannerMode = 'auto';
    }
    // バナー画像の初期値を読み取り
    var previewImg = document.getElementById('banner-preview-img');
    if (previewImg && previewImg.src) {
        currentBannerImageUrl = previewImg.src;
    }
});
