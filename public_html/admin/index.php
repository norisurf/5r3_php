<?php
/**
 * 管理画面ダッシュボード
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = '在庫管理システム - ' . SITE_NAME;
require_once __DIR__ . '/../includes/admin_header.php';

$db = getDB();

// 表示モード: ?show=all で全商品（削除済み含む）を表示
$showAll = ($_GET['show'] ?? '') === 'all';

// 車両一覧取得
if ($showAll) {
    $stmt = $db->query('SELECT * FROM vehicles ORDER BY created_at DESC');
} else {
    $stmt = $db->query('SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC');
}
$vehicles = $stmt->fetchAll();

// バナー取得
$stmt = $db->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1");
$banner = $stmt->fetch();

$bannerMode = $banner ? $banner['mode'] : 'manual';
$bannerImageUrl = $banner ? $banner['image_url'] : '';
$bannerLinkUrl = $banner ? $banner['link_url'] : '';
$latestVehicleTitle = '';

// 自動モードの場合、最新車両情報を取得
if ($banner && $banner['mode'] === 'auto') {
    $stmt2 = $db->query("SELECT * FROM vehicles WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 1");
    $latestVehicle = $stmt2->fetch();
    if ($latestVehicle) {
        $images = jsonDecode($latestVehicle['images']);
        $bannerImageUrl = !empty($images) ? $images[0] : '';
        $latestVehicleTitle = $latestVehicle['title'];
    }
}
?>

<div class="container mx-auto px-4 text-gray-800">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-[#003366]">在庫管理システム</h1>
            <p class="mt-2 text-gray-500 text-sm">車両の登録、編集、削除を行えます。</p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($showAll): ?>
                <a href="/admin/" class="flex items-center gap-2 rounded-2xl border-2 border-[#003366] px-5 py-3 font-bold text-[#003366] transition-all hover:bg-[#003366]/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>公開中のみ表示</span>
                </a>
            <?php else: ?>
                <a href="/admin/?show=all" class="flex items-center gap-2 rounded-2xl border-2 border-gray-300 px-5 py-3 font-bold text-gray-600 transition-all hover:border-[#003366] hover:text-[#003366]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>全商品一覧</span>
                </a>
            <?php endif; ?>
            <a href="/admin/new.php" class="flex items-center gap-2 rounded-2xl bg-[#003366] px-6 py-3 font-bold text-white transition-all hover:bg-[#002244] hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>車両を新規登録</span>
            </a>
        </div>
    </div>

    <!-- バナー管理セクション -->
    <div class="mb-8 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-xl font-bold text-[#003366] flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            トップページバナー設定
        </h2>

        <!-- モード選択 -->
        <div class="mb-6">
            <label class="mb-3 block text-sm font-medium text-gray-600">表示モード</label>
            <div class="flex gap-4">
                <button type="button" id="btn-mode-auto" onclick="setBannerMode('auto')" class="flex flex-1 items-center justify-center gap-3 rounded-xl border-2 p-4 transition-all <?= $bannerMode === 'auto' ? 'border-[#003366] bg-[#003366]/5 text-[#003366]' : 'border-gray-200 text-gray-500 hover:border-gray-300' ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <div class="text-left">
                        <div class="font-bold">自動（最新車両）</div>
                        <div class="text-xs opacity-70">新規登録した車両の画像を自動表示</div>
                    </div>
                </button>
                <button type="button" id="btn-mode-manual" onclick="setBannerMode('manual')" class="flex flex-1 items-center justify-center gap-3 rounded-xl border-2 p-4 transition-all <?= $bannerMode === 'manual' ? 'border-[#003366] bg-[#003366]/5 text-[#003366]' : 'border-gray-200 text-gray-500 hover:border-gray-300' ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                    <div class="text-left">
                        <div class="font-bold">手動（カスタム）</div>
                        <div class="text-xs opacity-70">任意の画像をアップロードして表示</div>
                    </div>
                </button>
            </div>
        </div>

        <!-- 自動モード表示 -->
        <div id="banner-auto-panel" class="<?= $bannerMode !== 'auto' ? 'hidden' : '' ?>">
            <div class="rounded-xl bg-blue-50 p-4 mb-4">
                <p class="text-sm text-blue-700">
                    <strong>自動モード:</strong> 最新の登録車両の画像が自動的にトップページに表示されます。
                    <?php if ($latestVehicleTitle): ?>
                    <span class="block mt-1">現在の表示: <strong><?= h($latestVehicleTitle) ?></strong></span>
                    <?php endif; ?>
                    <?php if ($bannerImageUrl && $bannerMode === 'auto'): ?>
                    <span class="block mt-2">
                        <img src="<?= h($bannerImageUrl) ?>" alt="Latest vehicle" class="mt-2 max-h-32 rounded-lg border border-blue-200">
                    </span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- 手動モード表示 -->
        <div id="banner-manual-panel" class="<?= $bannerMode !== 'manual' ? 'hidden' : '' ?>">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-600">バナー画像</label>
                    <div class="relative">
                        <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" id="banner-file-input" onchange="handleBannerUpload(this)" class="hidden">
                        <label for="banner-file-input" class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 px-4 py-6 transition-colors hover:border-[#003366] hover:bg-gray-50" id="banner-upload-label">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span class="text-gray-500" id="banner-upload-text">クリックして画像を選択</span>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">JPEG, PNG, WebP, GIF (最大10MB)</p>
                    <div id="banner-preview" class="mt-4 <?= empty($bannerImageUrl) || $bannerMode !== 'manual' ? 'hidden' : '' ?>">
                        <p class="mb-2 text-sm text-gray-500">現在の画像:</p>
                        <img id="banner-preview-img" src="<?= h($bannerImageUrl) ?>" alt="Banner preview" class="max-h-40 rounded-xl border border-gray-200 object-cover">
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-600">リンクURL</label>
                    <input type="text" id="banner-link-url" value="<?= h($bannerLinkUrl) ?>" placeholder="/stock.php?id=xxxxx または https://..." class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-[#003366] focus:outline-none focus:ring-2 focus:ring-[#003366]/20">
                    <p class="mt-2 text-xs text-gray-400">バナーをクリックした時の遷移先（任意）</p>
                </div>
            </div>
        </div>

        <!-- 保存ボタン -->
        <button type="button" onclick="saveBanner()" id="banner-save-btn" class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-[#003366] px-6 py-3 font-bold text-white transition-all hover:bg-[#002244]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            バナー設定を保存
        </button>
        <p id="banner-message" class="mt-4 text-sm hidden"></p>
    </div>

    <!-- 車両一覧テーブル -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-4 w-20">画像</th>
                    <th class="px-4 py-4">管理番号 / 登録日</th>
                    <th class="px-4 py-4">車両名 / 価格</th>
                    <th class="px-4 py-4 text-center">LP表示</th>
                    <th class="px-4 py-4">ステータス</th>
                    <th class="px-4 py-4 text-right">アクション</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($vehicles)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">車両が登録されていません。</td>
                </tr>
                <?php else: ?>
                <?php foreach ($vehicles as $v):
                    $vImages = jsonDecode($v['images']);
                    $thumbSrc = !empty($vImages) ? $vImages[0] : '';
                    $isDeleted = !empty($v['deleted_at']);
                ?>
                <tr class="<?= $isDeleted ? 'opacity-50 bg-gray-50' : 'hover:bg-gray-50/50' ?> transition-colors" id="vehicle-row-<?= h($v['id']) ?>">
                    <td class="px-4 py-3">
                        <?php if ($thumbSrc): ?>
                        <img src="<?= h($thumbSrc) ?>" alt="" class="w-16 h-12 object-cover rounded-lg border border-gray-200">
                        <?php else: ?>
                        <div class="w-16 h-12 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4">
                        <div class="font-bold text-[#003366]"><?= h($v['manage_number']) ?></div>
                        <div class="text-xs text-gray-400"><?= h($v['created_at']) ?></div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="max-w-md truncate font-medium text-gray-700"><?= h($v['title']) ?></div>
                        <div class="font-bold text-gray-900 mt-1"><?= number_format($v['price']) ?>円</div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <?php if ($isDeleted): ?>
                        <span class="text-xs text-gray-400">-</span>
                        <?php else: ?>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" <?= $v['display_on_lp'] ? 'checked' : '' ?> onchange="toggleLpDisplay('<?= h($v['id']) ?>', this.checked)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003366]"></div>
                        </label>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4">
                        <?php if ($isDeleted): ?>
                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">削除済み</span>
                        <?php else: ?>
                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">公開中</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="/stock.php?id=<?= h($v['id']) ?>" target="_blank" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors" title="プレビュー">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <?php if (!$isDeleted): ?>
                            <button onclick="duplicateVehicle('<?= h($v['id']) ?>')" class="rounded-xl p-2 text-green-400 hover:bg-green-50 hover:text-green-600 transition-colors" title="複製">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            </button>
                            <a href="/admin/edit.php?id=<?= h($v['id']) ?>" class="rounded-xl p-2 text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="編集">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <button onclick="deleteVehicle('<?= h($v['id']) ?>')" class="rounded-xl p-2 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="削除">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <?php else: ?>
                            <button onclick="restoreVehicle('<?= h($v['id']) ?>')" class="rounded-xl p-2 text-orange-400 hover:bg-orange-50 hover:text-orange-600 transition-colors" title="復元">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 010 10H9m4-10l-4-4m4 4l-4 4"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="/js/admin.js"></script>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
