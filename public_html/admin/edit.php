<?php
declare(strict_types=1);
/**
 * 管理画面: 車両編集
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: /admin/');
    exit;
}

$db = getDB();
$stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ?');
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    header('Location: /admin/');
    exit;
}

$pageTitle = '車両編集 - ' . SITE_NAME;
require_once __DIR__ . '/../includes/admin_header.php';

$images = jsonDecode($vehicle['images']);
$basicInfo = jsonDecode($vehicle['basic_info']);
$detailedInfo = jsonDecode($vehicle['detailed_info']);
$equipment = jsonDecode($vehicle['equipment']);
?>

<div class="container mx-auto px-4 max-w-5xl text-gray-800">
    <a href="/admin/" class="mb-8 inline-flex items-center gap-2 text-gray-500 hover:text-[#003366]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>管理画面に戻る</span>
    </a>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- 編集フォーム (フル幅) -->
        <div class="lg:col-span-3">
            <form id="vehicle-form" onsubmit="return submitVehicle(event)" class="space-y-8 rounded-3xl border border-gray-100 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 pb-6">
                    <h1 class="text-2xl font-bold text-[#003366]">車両情報の編集</h1>
                    <button type="submit" id="submit-btn" class="flex items-center gap-2 rounded-xl bg-[#003366] px-6 py-2 font-bold text-white transition-all hover:bg-[#002244]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>保存する</span>
                    </button>
                </div>

                <!-- 基本項目 -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-bold">車両タイトル</label>
                        <input type="text" id="field-title" required value="<?= h($vehicle['title']) ?>" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-[#003366] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold">車両本体価格（税込）</label>
                        <input type="number" id="field-price" required value="<?= (int)$vehicle['price'] ?>" class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:border-[#003366] focus:outline-none">
                    </div>
                </div>

                <!-- 画像管理 -->
                <div>
                    <label class="mb-4 block text-sm font-bold">車両画像</label>
                    <div id="image-grid" class="grid grid-cols-3 gap-4 md:grid-cols-5">
                        <?php foreach ($images as $idx => $img): ?>
                        <div class="group relative aspect-square overflow-hidden rounded-xl border border-gray-100 bg-gray-50" data-image="<?= h($img) ?>">
                            <img src="<?= h($img) ?>" alt="" class="h-full w-full object-cover">
                            <button type="button" onclick="removeImage(this.parentElement)" class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white opacity-0 transition-opacity group-hover:opacity-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <?php endforeach; ?>
                        <button type="button" onclick="addImage()" class="flex aspect-square flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-gray-200 text-gray-400 transition-colors hover:border-[#003366] hover:text-[#003366]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-[10px]">追加</span>
                        </button>
                    </div>
                </div>

                <!-- スペック情報 -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold">基本情報</label>
                        <textarea id="field-basicInfo" class="h-64 w-full rounded-xl border border-gray-200 p-4 font-mono text-xs focus:outline-none"><?= h(json_encode($basicInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) ?></textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold">詳細・装備品</label>
                        <textarea id="field-detailedEquipment" class="h-64 w-full rounded-xl border border-gray-200 p-4 font-mono text-xs focus:outline-none"><?= h(json_encode(['detailed' => $detailedInfo, 'equipment' => $equipment], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) ?></textarea>
                    </div>
                </div>

                <!-- 商品説明 -->
                <div>
                    <label class="mb-2 block text-sm font-bold">商品説明</label>
                    <textarea id="field-description" required class="h-80 w-full rounded-xl border border-gray-200 p-4 text-sm focus:border-[#003366] focus:outline-none"><?= h($vehicle['description']) ?></textarea>
                </div>
            </form>
        </div>
    </div>
</div>

<script>var vehicleId = '<?= h($id) ?>';</script>
<script src="/js/admin.js"></script>
<?php
declare(strict_types=1); require_once __DIR__ . '/../includes/admin_footer.php'; ?>
