<?php

declare(strict_types=1);
/**
 * プライスボード表示 + PDF印刷
 * URL例: /price_bord.php?id=VEHICLE_ID
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = trim($_GET['id'] ?? '');
if (!$id) {
    http_response_code(400);
    die('車両IDが指定されていません。');
}

$db = getDB();
$stmt = $db->prepare('SELECT * FROM vehicles WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    http_response_code(404);
    die('車両が見つかりません。');
}

$basicInfo = jsonDecode($vehicle['basic_info'], []);

// タイトルから「即決〇〇万円」以降を除去して表示
$carName = trim(preg_replace('/即決.*$/u', '', trim($vehicle['title'] ?? '')));

// 車両本体価格を万円（小数1桁）に変換（948,000円 → 94.8）
$rawPrice = (int)$vehicle['price'];
$priceFormatted = $rawPrice > 0 ? number_format($rawPrice / 10000, 1) : '0.0';
[$priceInt, $priceDec] = explode('.', $priceFormatted);

$nenShiki = $basicInfo['年式']         ?? '';
$shaken   = $basicInfo['車検有効期限'] ?? '';
$mileage  = $basicInfo['走行距離']     ?? '';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>プライスボード - <?= h($carName) ?></title>
    <style>
        /* ========== 画面表示 ========== */
        body {
            margin: 0;
            padding: 30px;
            background: #f0f0f0;
            font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .print-bar {
            width: 820px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s;
        }

        .btn-pdf {
            background: #003366;
            color: #fff;
        }

        .btn-pdf:hover {
            background: #002244;
        }

        .btn-back {
            background: #555555;
            color: #fff;
        }

        .btn-back:hover {
            background: #333333;
        }

        /* ========== ボード本体 ========== */
        :root {
            --yellow: #e6f14a;
        }

        .price-board {
            width: 800px;
            height: 560px;
            background-color: var(--yellow);
            border: 10px solid #000;
            position: relative;
            font-weight: bold;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ----------------------------------------
           上部コンテンツエリア（車名 + 車両価格）
        ---------------------------------------- */
        .board-top {
            position: absolute;
            top: 16px;
            left: 20px;
            right: 20px;
            bottom: 150px;
            /* maintenance-info の上まで */
            display: flex;
            flex-direction: column;
        }

        /* --- 車名行 --- */
        .car-name-row {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            padding-bottom: 8px;
            border-bottom: 3px solid #000;
            flex-shrink: 0;
        }

        .label-car {
            font-size: 36px;
            line-height: 1.2;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: 112px;
            /* "車名" の幅を確保 */
        }

        .car-name-value {
            font-size: 28px;
            line-height: 1.35;
            word-break: break-word;
            white-space: normal;
        }

        /* --- 車両価格ラベル行（独立行） --- */
        .price-label-row {
            flex-shrink: 0;
            padding-top: 6px;
        }

        .label-price {
            font-size: 36px;
            line-height: 1.2;
            white-space: nowrap;
        }

        /* --- 金額行（右寄せ） --- */
        .price-value-row {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            gap: 6px;
            flex: 1;
            min-height: 0;
        }

        .price-number {
            font-size: 250px;
            line-height: 0.9;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }

        /* CSS で正円を描画（フォントに依存しない丸ドット） */
        .price-dot {
            display: inline-block;
            width: 0.09em;
            height: 0.09em;
            background-color: currentColor;
            border-radius: 50%;
            vertical-align: 0;
            /* 数字の底ラインに合わせる */
            margin: 0 0.03em;
        }

        .price-unit {
            font-size: 58px;
            line-height: 1;
            margin-bottom: 8px;
            flex-shrink: 0;
        }

        .tax-note {
            font-size: 13px;
            writing-mode: vertical-rl;
            margin-bottom: 10px;
            flex-shrink: 0;
        }

        /* ----------------------------------------
           URL / TEL エリア
        ---------------------------------------- */
        .maintenance-info {
            position: absolute;
            bottom: 82px;
            left: 20px;
            right: 20px;
            height: 60px;
            border: 2px solid #000;
            display: flex;
        }

        .info-cell {
            flex: 1;
            border-right: 2px solid #000;
            padding: 3px 6px;
            font-size: 14px;
            overflow: hidden;
        }

        .info-cell:last-child {
            border-right: none;
        }

        .info-cell p {
            margin: 0;
            line-height: 1.15;
        }

        .info-cell .url-text {
            font-size: 32px;
            word-break: break-all;
        }

        /* ----------------------------------------
           フッター（年式・車検・走行距離）
        ---------------------------------------- */
        .footer-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .footer-item {
            background: #000;
            color: #fff;
            padding: 5px 14px;
            border-radius: 10px;
            font-size: 22px;
            white-space: nowrap;
        }

        .footer-value {
            font-size: 14px;
            border-bottom: 2px solid #000;
            min-width: 80px;
            text-align: center;
            white-space: nowrap;
        }

        /* ========== 印刷 / PDF ========== */
        @media print {
            @page {
                size: A3 landscape;
                /* 420mm × 297mm */
                margin: 0;
                /* 縁なし余白ゼロ */
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background: none;
            }

            /* 印刷ボタン非表示 */
            .print-bar {
                display: none !important;
            }

            .price-board {
                /* 黄色背景を印刷しない（白紙に黒線のみ） */
                background-color: #fff !important;
                border-color: #000;
                /* 800px → A3横(420mm≒1587px CSS-px) にスケール
                   zoom: 1587 / 800 ≈ 1.984                        */
                zoom: 1.984;
                /* ボーダーを余白なしで端まで出す */
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <!-- 印刷ボタン -->
    <div class="print-bar">
        <button class="btn btn-pdf" onclick="window.print()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            PDFで印刷 (A3)
        </button>
        <a href="/admin/" class="btn btn-back">← 管理画面に戻る</a>
    </div>

    <!-- プライスボード本体 -->
    <div class="price-board">

        <div class="board-top">

            <!-- 車名（左ラベル固定 + 右に実際の車名） -->
            <div class="car-name-row">
                <span class="label-car">車名</span>
                <span class="car-name-value"><?= h($carName) ?></span>
            </div>

            <!-- 車両価格ラベル（独立行） -->
            <div class="price-label-row">
                <span class="label-price">車両価格</span>
            </div>

            <!-- 金額（右寄せ） -->
            <div class="price-value-row">
                <span class="price-number"><?= h($priceInt) ?><span class="price-dot"></span><?= h($priceDec) ?></span>
                <span class="price-unit">万円</span>
                <span class="tax-note">消費税込</span>
            </div>

        </div>

        <!-- WEBサイトURL / お問い合せTEL -->
        <div class="maintenance-info">
            <div class="info-cell">
                <p>■WEBサイトURL</p>
                <p class="url-text">https://5r3.co.jp</p>
            </div>
            <div class="info-cell">
                <p>■お問い合せTEL</p>
                <p class="url-text"><?= h(SITE_PHONE) ?></p>
            </div>
        </div>

        <!-- 年式 / 車検有効期限 / 走行距離 -->
        <div class="footer-info">
            <div class="footer-item">年式</div>
            <div class="footer-value"><?= h($nenShiki) ?></div>
            <div class="footer-item">車検有効期限</div>
            <div class="footer-value"><?= h($shaken) ?></div>
            <div class="footer-item">走行距離</div>
            <div class="footer-value"><?= h($mileage) ?></div>
        </div>

    </div>

</body>

</html>