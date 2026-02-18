<?php
declare(strict_types=1);
/**
 * Yahoo!オークション HTMLパーサー
 * Node.js Cheerio版をPHP DOMDocument/DOMXPathで再実装
 */

function parseYahooVehicle(string $html): array {
    // --- 最初に正規表現で __NEXT_DATA__ と pageData を抽出 ---
    // DOMDocument の loadHTML は巨大HTMLや PHP 8.4 のエンコーディング問題で
    // 失敗することがあるため、先に正規表現で主要データを抽出する
    $pageData = null;
    $nextData = null;

    // __NEXT_DATA__ を strpos で確実に抽出（正規表現より高速で巨大HTMLに強い）
    $ndStart = strpos($html, 'id="__NEXT_DATA__"');
    if ($ndStart !== false) {
        $jsonStart = strpos($html, '>', $ndStart);
        if ($jsonStart !== false) {
            $jsonStart++; // '>' の次の文字から
            $jsonEnd = strpos($html, '</script>', $jsonStart);
            if ($jsonEnd !== false) {
                $jsonStr = substr($html, $jsonStart, $jsonEnd - $jsonStart);
                $nextData = json_decode($jsonStr, true);
            }
        }
    }

    // pageData を正規表現で抽出
    if (preg_match('/var pageData = (\{[\s\S]*?\});/', $html, $m)) {
        $pageData = json_decode($m[1], true);
    }

    // DOMDocument でHTMLをロード（フォールバック用）
    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    // PHP 8.4対応: mb_convert_encoding(..., 'HTML-ENTITIES') は削除されたため、
    // UTF-8 metaタグを先頭に付与してloadHTMLに渡す
    $htmlWithMeta = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . $html;
    $dom->loadHTML($htmlWithMeta, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    // DOMからも __NEXT_DATA__ を試す（正規表現で取れなかった場合）
    if ($nextData === null) {
        $nextDataNode = $xpath->query('//script[@id="__NEXT_DATA__"]');
        if ($nextDataNode->length > 0) {
            $nextData = json_decode($nextDataNode->item(0)->textContent, true);
        }
    }

    // DOMからも pageData を試す（正規表現で取れなかった場合）
    if ($pageData === null) {
        $scripts = $xpath->query('//script');
        foreach ($scripts as $script) {
            $content = $script->textContent;
            if (strpos($content, 'var pageData =') !== false) {
                if (preg_match('/var pageData = (\{[\s\S]*?\});/', $content, $m2)) {
                    $pageData = json_decode($m2[1], true);
                }
            }
        }
    }

    // --- タイトル ---
    $title = '';
    $h1 = $xpath->query('//h1');
    if ($h1->length > 0) {
        $title = trim($h1->item(0)->textContent);
    }
    if (empty($title)) {
        $titleTag = $xpath->query('//title');
        if ($titleTag->length > 0) {
            $title = str_replace('- Yahoo!オークション', '', $titleTag->item(0)->textContent);
            $title = trim($title);
        }
    }
    if (isset($pageData['items']['productName'])) {
        $title = $pageData['items']['productName'];
    }

    // --- 価格 ---
    $price = 0;
    $buyoutPrice = null;

    if (isset($pageData['items'])) {
        $price = (int)($pageData['items']['price'] ?? 0);
        $buyoutPrice = isset($pageData['items']['winPrice']) ? (int)$pageData['items']['winPrice'] : null;
    } else {
        $priceNode = $xpath->query('//*[contains(@class, "Price__value")]');
        if ($priceNode->length > 0) {
            preg_match('/\d+/', str_replace(',', '', $priceNode->item(0)->textContent), $m);
            $price = isset($m[0]) ? (int)$m[0] : 0;
        }
    }

    // --- 画像 ---
    $images = [];

    // __NEXT_DATA__ から画像を抽出
    if (isset($nextData['props']['pageProps']['initialState']['item']['detail']['item']['img'])) {
        foreach ($nextData['props']['pageProps']['initialState']['item']['detail']['item']['img'] as $img) {
            if (isset($img['image']) && !in_array($img['image'], $images)) {
                $images[] = $img['image'];
            }
        }
    }

    // ギャラリーからの抽出
    if (empty($images)) {
        $imgNodes = $xpath->query('//img');
        foreach ($imgNodes as $imgNode) {
            $src = $imgNode->getAttribute('src');
            if ($src && strpos($src, 'auctions.c.yimg.jp/images') !== false && strpos($src, 'auc-pctr') === false && !in_array($src, $images)) {
                $images[] = $src;
            }
        }
    }

    // --- 基本情報・詳細情報・装備品 ---
    $basicInfo = [];
    $detailedInfo = [];
    $equipment = [];

    // __NEXT_DATA__ から car 情報を抽出
    $carData = $nextData['props']['pageProps']['initialState']['item']['detail']['item']['car'] ?? null;

    if (isset($carData['spec'])) {
        $spec = $carData['spec'];

        $basicInfo['メーカー名'] = $spec['maker'] ?? '';
        $basicInfo['車種名'] = $spec['type'] ?? '';
        $basicInfo['グレード名'] = $spec['grade'] ?? '';
        $basicInfo['排気量'] = isset($spec['displacement']) ? number_format($spec['displacement']) . ' cc' : '';

        if (isset($spec['firstRegYear'], $spec['firstRegMonth'])) {
            $year = (int)$spec['firstRegYear'];
            if ($year >= 2019) {
                $warekiYear = $year - 2018;
                $basicInfo['年式'] = "令和{$warekiYear}年 ({$year}年) {$spec['firstRegMonth']}月";
            } elseif ($year >= 1989) {
                $warekiYear = $year - 1988;
                $basicInfo['年式'] = "平成{$warekiYear}年 ({$year}年) {$spec['firstRegMonth']}月";
            } else {
                $basicInfo['年式'] = "{$year}年 {$spec['firstRegMonth']}月";
            }
        } else {
            $basicInfo['年式'] = '';
        }

        $basicInfo['輸入車モデル年式'] = '-';
        $basicInfo['走行距離'] = isset($spec['mileage']) ? number_format($spec['mileage']) . ' km' : '';
        $basicInfo['走行距離の状態'] = $spec['mileageStatus'] ?? '';
        $basicInfo['色系統'] = $spec['colorTone'] ?? '';
        $basicInfo['色の名称'] = '-';

        if (isset($spec['expirationYear'], $spec['expirationMonth'])) {
            $basicInfo['車検有効期限'] = "令和 {$spec['expirationYear']}年{$spec['expirationMonth']}月まで";
        } else {
            $basicInfo['車検有効期限'] = '';
        }

        $basicInfo['ミッション'] = $spec['transmission'] ?? '';
        $basicInfo['ボディタイプ'] = $spec['bodyType'] ?? '';
        $basicInfo['型式'] = $spec['form'] ?? '';

        $detailedInfo['ドア数'] = $spec['door'] ?? '';
        $detailedInfo['乗車定員数'] = isset($spec['rideCapacity']) ? $spec['rideCapacity'] . '人乗り' : '';
        $detailedInfo['駆動方式'] = $spec['driveType'] ?? '';
        $detailedInfo['燃料'] = $spec['fuel'] ?? '';
        $detailedInfo['点検記録簿'] = $spec['maintenanceSheet'] ?? '';
        $detailedInfo['修復歴'] = $spec['repairHistory'] ?? '';
        $detailedInfo['車台番号（下3けた）'] = $spec['chassisNumber'] ?? '';
        $detailedInfo['リサイクル預託金'] = isset($spec['recyclingDeposit']) ? $spec['recyclingDeposit'] . ' 円' : '0 円';
        $detailedInfo['輸入経路'] = $spec['importCourse'] ?? '';
        $detailedInfo['ハンドル'] = $spec['steeringWheel'] ?? '';
        $detailedInfo['車歴'] = $spec['career'] ?? '';
        $detailedInfo['所有者歴'] = $spec['ownerHistory'] ?? '';
        $detailedInfo['引き渡し条件'] = $spec['deliveredTerm'] ?? '';

        // 装備品
        if (isset($spec['options']) && is_array($spec['options'])) {
            foreach ($spec['options'] as $opt) {
                if (!empty($opt['isOption'])) {
                    $equipment[] = $opt['name'];
                }
            }
        }
    } else {
        // フォールバック: テーブルからデータを抽出
        $basicKeys = ['メーカー名', '車種名', 'グレード名', '排気量', '年式', '輸入車モデル年式', '走行距離', '走行距離の状態', '色系統', '色の名称', '車検有効期限', 'ミッション', 'ボディタイプ', '型式'];

        $tables = $xpath->query('//table');
        foreach ($tables as $table) {
            $rows = $xpath->query('.//tr', $table);
            foreach ($rows as $row) {
                $th = $xpath->query('.//th', $row);
                $td = $xpath->query('.//td', $row);
                if ($th->length > 0 && $td->length > 0) {
                    $key = trim($th->item(0)->textContent);
                    $val = trim($td->item(0)->textContent);
                    if ($key && $val) {
                        if (in_array($key, $basicKeys)) {
                            $basicInfo[$key] = $val;
                        } else {
                            $detailedInfo[$key] = $val;
                        }
                    }
                }
            }
        }

        // dl/dt/dd 形式のデータ抽出
        $dtNodes = $xpath->query('//dt');
        foreach ($dtNodes as $dt) {
            $label = trim($dt->textContent);
            $dd = $dt->nextSibling;
            while ($dd && $dd->nodeType !== XML_ELEMENT_NODE) {
                $dd = $dd->nextSibling;
            }
            if ($dd && strtolower($dd->nodeName) === 'dd') {
                $value = trim($dd->textContent);
                if ($label && $value) {
                    if (in_array($label, $basicKeys)) {
                        $basicInfo[$label] = $value;
                    } else {
                        $detailedInfo[$label] = $value;
                    }
                }
            }
        }
    }

    // --- 商品説明 ---
    $description = '';

    if (isset($carData['description'])) {
        $description = $carData['description'];
        $description = preg_replace('/<br\s*\/?>/i', "\n", $description);
        $description = strip_tags($description);
        $description = str_replace(['&nbsp;', '&amp;'], [' ', '&'], $description);
        $description = trim($description);
    } else {
        $descNode = $xpath->query('//*[@id="description"]');
        if ($descNode->length > 0) {
            $description = trim($descNode->item(0)->textContent);
        }
    }

    // タイトルから「即決」以下を削除
    $cleanedTitle = cleanTitle($title);

    // 商品説明から最後の「■」以下を削除
    $cleanedDescription = cleanDescription($description);

    return [
        'title' => $cleanedTitle,
        'price' => $price,
        'buyoutPrice' => $buyoutPrice,
        'images' => $images,
        'basicInfo' => $basicInfo,
        'detailedInfo' => $detailedInfo,
        'equipment' => $equipment,
        'description' => $cleanedDescription,
    ];
}
