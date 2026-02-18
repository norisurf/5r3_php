<?php
declare(strict_types=1);
/**
 * 管理画面ダッシュボード
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = '在庫管理システム - ' . SITE_NAME;
require_once __DIR__ . '/../includes/admin_header.php';

$db = getDB();

// 表示モード: active=出品中, stopped=公開停止 (デフォルト: 出品中)
$filter = $_GET['filter'] ?? 'active';
$search = trim($_GET['q'] ?? '');

// 車両一覧取得
if ($filter === 'stopped') {
    $sql = 'SELECT * FROM vehicles WHERE deleted_at IS NOT NULL';
} else {
    $sql = 'SELECT * FROM vehicles WHERE deleted_at IS NULL';
}

// 検索条件追加
if ($search !== '') {
    $sql .= ' AND title LIKE ?';
    $stmt = $db->prepare($sql . ' ORDER BY created_at DESC');
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = $db->query($sql . ' ORDER BY created_at DESC');
}
$vehicles = $stmt->fetchAll();

// 件数取得
$stmtActive = $db->query('SELECT COUNT(*) FROM vehicles WHERE deleted_at IS NULL');
$activeCount = $stmtActive->fetchColumn();
$stmtStopped = $db->query('SELECT COUNT(*) FROM vehicles WHERE deleted_at IS NOT NULL');
$stoppedCount = $stmtStopped->fetchColumn();

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
    <!-- ヘッダー: タイトル + フィルター + 新規登録 -->
    <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#003366]">在庫管理システム</h1>
            <p class="mt-2 text-gray-500 text-sm">車両の登録、編集、公開停止を行えます。</p>
        </div>
        <div class="flex items-center gap-3 ml-auto">
            <!-- 検索バー -->
            <form method="GET" action="/admin/" class="flex items-center gap-2">
                <input type="hidden" name="filter" value="<?= h($filter) ?>">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                    </svg>
                    <input type="text" name="q" value="<?= h($search) ?>" placeholder="車両名で検索..."
                        class="pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-[#003366] focus:outline-none focus:ring-2 focus:ring-[#003366]/20 w-64">
                </div>
                <button type="submit"
                    class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-200 transition-colors">検索</button>
            </form>

            <!-- 出品中 / 公開停止 フィルター -->
            <div class="flex rounded-2xl border border-gray-200 overflow-hidden">
                <a href="/admin/?filter=active<?= $search ? '&q=' . urlencode($search) : '' ?>"
                    class="flex items-center gap-2 px-5 py-3 font-bold text-sm transition-all <?= $filter !== 'stopped' ? 'bg-[#003366] text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?>">
                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                    出品中 <span class="ml-1 text-xs opacity-80">(<?= $activeCount ?>)</span>
                </a>
                <a href="/admin/?filter=stopped<?= $search ? '&q=' . urlencode($search) : '' ?>"
                    class="flex items-center gap-2 px-5 py-3 font-bold text-sm transition-all border-l border-gray-200 <?= $filter === 'stopped' ? 'bg-[#003366] text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?>">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    公開停止 <span class="ml-1 text-xs opacity-80">(<?= $stoppedCount ?>)</span>
                </a>
            </div>
            <!-- 新規登録ボタン -->
            <a href="/admin/new.php"
                class="flex items-center gap-2 rounded-2xl bg-[#003366] px-6 py-3 font-bold text-white transition-all hover:bg-[#002244] hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>車両を新規登録</span>
            </a>
        </div>
    </div>

    <!-- バナー管理セクション（デフォルト閉じ） -->
    <div class="mb-8 rounded-3xl border border-gray-100 bg-white shadow-sm">
        <button type="button" onclick="toggleBannerSection()"
            class="w-full flex items-center justify-between p-5 text-left hover:bg-gray-50 transition-colors rounded-3xl"
            id="banner-toggle-btn">
            <h2 class="text-lg font-bold text-[#003366] flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <path d="M21 15l-5-5L5 21" />
                </svg>
                トップページバナー設定
            </h2>
            <svg id="banner-chevron" class="w-5 h-5 text-gray-400 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="banner-section-body" class="hidden px-6 pb-6">
            <!-- モード選択 -->
            <div class="mb-6">
                <label class="mb-3 block text-sm font-medium text-gray-600">表示モード</label>
                <div class="flex gap-4">
                    <button type="button" id="btn-mode-auto" onclick="setBannerMode('auto')"
                        class="flex flex-1 items-center justify-center gap-3 rounded-xl border-2 p-4 transition-all <?= $bannerMode === 'auto' ? 'border-[#003366] bg-[#003366]/5 text-[#003366]' : 'border-gray-200 text-gray-500 hover:border-gray-300' ?>">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        <div class="text-left">
                            <div class="font-bold">自動（最新車両）</div>
                            <div class="text-xs opacity-70">新規登録した車両の画像を自動表示</div>
                        </div>
                    </button>
                    <button type="button" id="btn-mode-manual" onclick="setBannerMode('manual')"
                        class="flex flex-1 items-center justify-center gap-3 rounded-xl border-2 p-4 transition-all <?= $bannerMode === 'manual' ? 'border-[#003366] bg-[#003366]/5 text-[#003366]' : 'border-gray-200 text-gray-500 hover:border-gray-300' ?>">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
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
                                <img src="<?= h($bannerImageUrl) ?>" alt="Latest vehicle"
                                    class="mt-2 max-h-32 rounded-lg border border-blue-200">
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
                            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" id="banner-file-input"
                                onchange="handleBannerUpload(this)" class="hidden">
                            <label for="banner-file-input"
                                class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 px-4 py-6 transition-colors hover:border-[#003366] hover:bg-gray-50"
                                id="banner-upload-label">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span class="text-gray-500" id="banner-upload-text">クリックして画像を選択</span>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">JPEG, PNG, WebP, GIF (最大10MB)</p>
                        <div id="banner-preview"
                            class="mt-4 <?= empty($bannerImageUrl) || $bannerMode !== 'manual' ? 'hidden' : '' ?>">
                            <p class="mb-2 text-sm text-gray-500">現在の画像:</p>
                            <img id="banner-preview-img" src="<?= h($bannerImageUrl) ?>" alt="Banner preview"
                                class="max-h-40 rounded-xl border border-gray-200 object-cover">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-600">リンクURL</label>
                        <input type="text" id="banner-link-url" value="<?= h($bannerLinkUrl) ?>"
                            placeholder="/stock.php?id=xxxxx または https://..."
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-[#003366] focus:outline-none focus:ring-2 focus:ring-[#003366]/20">
                        <p class="mt-2 text-xs text-gray-400">バナーをクリックした時の遷移先（任意）</p>
                    </div>
                </div>
            </div>

            <!-- 保存ボタン -->
            <button type="button" onclick="saveBanner()" id="banner-save-btn"
                class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-[#003366] px-6 py-3 font-bold text-white transition-all hover:bg-[#002244]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                バナー設定を保存
            </button>
            <p id="banner-message" class="mt-4 text-sm hidden"></p>
        </div>
    </div>

    <?php if ($search): ?>
        <div class="mb-4 text-sm text-gray-500">
            「<strong><?= h($search) ?></strong>」の検索結果: <?= count($vehicles) ?>件
        </div>
    <?php endif; ?>

    <!-- 車両一覧テーブル -->
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-4 w-[148px]">画像</th>
                    <th class="px-4 py-4">管理番号 / 登録日</th>
                    <th class="px-4 py-4">車両名 / 価格</th>
                    <th class="px-4 py-4">ステータス</th>
                    <th class="px-4 py-4 text-right">アクション</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($vehicles)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <?php if ($search): ?>
                                検索条件に一致する車両がありません。
                            <?php elseif ($filter === 'stopped'): ?>
                                公開停止中の車両はありません。
                            <?php else: ?>
                                車両が登録されていません。
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($vehicles as $v):
                        $vImages = jsonDecode($v['images']);
                        $thumbSrc = !empty($vImages) ? $vImages[0] : '';
                        $isDeleted = !empty($v['deleted_at']);
                        ?>
                        <tr class="<?= $isDeleted ? 'bg-gray-50/50' : 'hover:bg-gray-50/50' ?> transition-colors"
                            id="vehicle-row-<?= h($v['id']) ?>">
                            <td>
                                <?php if ($thumbSrc): ?>
                                    <img src="<?= h($thumbSrc) ?>" alt="" style="width: 128px; height: 128px; object-fit: cover;"
                                        class="rounded-lg border border-gray-200">
                                <?php else: ?>
                                    <div style="width: 128px; height: 128px;"
                                        class="rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                            <circle cx="8.5" cy="8.5" r="1.5" />
                                            <path d="M21 15l-5-5L5 21" />
                                        </svg>
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
                            <td class="px-4 py-4">
                                <?php if ($isDeleted): ?>
                                    <span
                                        class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">公開停止</span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">出品中</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="/stock.php?id=<?= h($v['id']) ?>" target="_blank"
                                        class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                                        title="プレビュー">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <button onclick="duplicateVehicle('<?= h($v['id']) ?>')"
                                        class="rounded-xl p-2 text-green-400 hover:bg-green-50 hover:text-green-600 transition-colors"
                                        title="複製">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                                            <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" />
                                        </svg>
                                    </button>
                                    <a href="/admin/edit.php?id=<?= h($v['id']) ?>"
                                        class="rounded-xl p-2 text-blue-400 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                                        title="編集">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <?php if (!$isDeleted): ?>
                                        <button onclick="deleteVehicle('<?= h($v['id']) ?>')"
                                            class="rounded-xl p-2 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                            title="削除">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    <?php else: ?>
                                        <button onclick="restoreVehicle('<?= h($v['id']) ?>')"
                                            class="rounded-xl p-2 text-orange-400 hover:bg-orange-50 hover:text-orange-600 transition-colors"
                                            title="出品に戻す">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h10a5 5 0 010 10H9m4-10l-4-4m4 4l-4 4" />
                                            </svg>
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
<?php
declare(strict_types=1); require_once __DIR__ . '/../includes/admin_footer.php'; ?>