<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
@ini_set('memory_limit', '256M');

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/parser.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST時はtext/plainで結果を返す
    header('Content-Type: text/plain; charset=utf-8');

    $rawInput = file_get_contents('php://input');
    echo "=== Parse Debug Tool ===\n";
    echo "PHP Version: " . PHP_VERSION . "\n\n";

    echo "=== Raw Input ===\n";
    echo "Raw input length: " . strlen($rawInput) . " bytes\n";
    echo "Raw input first 200 chars: " . mb_substr($rawInput, 0, 200) . "\n\n";

    $body = json_decode($rawInput, true);
    if ($body === null) {
        echo "JSON decode of POST body FAILED: " . json_last_error_msg() . "\n";
        exit;
    }
    echo "JSON decode of POST body: OK\n";

    $html = $body['html'] ?? '';
    echo "HTML length: " . strlen($html) . " bytes\n\n";

    if (empty($html)) {
        echo "HTML is empty!\n";
        echo "POST body keys: " . implode(', ', array_keys($body)) . "\n";
        exit;
    }

    echo "=== __NEXT_DATA__ Search ===\n";
    $pos1 = strpos($html, '__NEXT_DATA__');
    echo "strpos '__NEXT_DATA__': " . ($pos1 !== false ? $pos1 : 'NOT FOUND') . "\n";

    $pos2 = strpos($html, 'id="__NEXT_DATA__"');
    echo "strpos 'id=\"__NEXT_DATA__\"': " . ($pos2 !== false ? $pos2 : 'NOT FOUND') . "\n";

    if ($pos1 !== false) {
        $contextStart = max(0, $pos1 - 50);
        $context = substr($html, $contextStart, 200);
        echo "\n__NEXT_DATA__ context:\n" . $context . "\n";
    }

    echo "\n=== JSON Extraction ===\n";
    $ndStart = strpos($html, 'id="__NEXT_DATA__"');
    if ($ndStart !== false) {
        $jsonStart = strpos($html, '>', $ndStart);
        if ($jsonStart !== false) {
            $jsonStart++;
            $jsonEnd = strpos($html, '</script>', $jsonStart);
            if ($jsonEnd !== false) {
                $jsonStr = substr($html, $jsonStart, $jsonEnd - $jsonStart);
                echo "Extracted JSON length: " . strlen($jsonStr) . "\n";
                echo "JSON first 200 chars: " . mb_substr($jsonStr, 0, 200) . "\n";
                echo "JSON last 100 chars: " . mb_substr($jsonStr, -100) . "\n";

                $decoded = json_decode($jsonStr, true);
                if ($decoded) {
                    echo "JSON decode: SUCCESS\n";
                    $car = $decoded['props']['pageProps']['initialState']['item']['detail']['item']['car'] ?? null;
                    echo "car data: " . ($car ? "FOUND" : "NOT FOUND") . "\n";
                    if (!$car) {
                        echo "Top keys: " . implode(', ', array_keys($decoded)) . "\n";
                        if (isset($decoded['props'])) echo "props keys: " . implode(', ', array_keys($decoded['props'])) . "\n";
                        if (isset($decoded['props']['pageProps'])) echo "pageProps keys: " . implode(', ', array_keys($decoded['props']['pageProps'])) . "\n";
                    }
                } else {
                    echo "JSON decode FAILED: " . json_last_error_msg() . "\n";
                    echo "JSON first 20 bytes hex: " . bin2hex(substr($jsonStr, 0, 20)) . "\n";
                }
            } else {
                echo "closing </script> not found\n";
            }
        }
    } else {
        echo "id=\"__NEXT_DATA__\" NOT FOUND in HTML\n";
    }

    echo "\n=== parseYahooVehicle Result ===\n";
    try {
        $result = parseYahooVehicle($html);
        echo "Title: " . ($result['title'] ?? 'EMPTY') . "\n";
        echo "Price: " . ($result['price'] ?? '0') . "\n";
        echo "Images: " . count($result['images'] ?? []) . "\n";
        echo "BasicInfo count: " . count($result['basicInfo'] ?? []) . "\n";
        if (!empty($result['basicInfo'])) {
            foreach ($result['basicInfo'] as $k => $v) echo "  $k = $v\n";
        }
    } catch (\Throwable $e) {
        echo "ERROR: " . $e->getMessage() . " (line " . $e->getLine() . ")\n";
    }

    exit;
}

// GET時はHTMLフォームを表示
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Parse Test</title></head>
<body>
<h2>Parse Test Form</h2>
<p>Yahoo!オークションのHTMLソースを貼り付けて「Test Parse」を押してください</p>
<textarea id="html-input" rows="10" cols="100" placeholder="ここにHTMLを貼り付け..."></textarea><br><br>
<button onclick="testParse()">Test Parse</button>
<pre id="result" style="background:#f0f0f0;padding:10px;margin-top:10px;white-space:pre-wrap;"></pre>
<script>
function testParse() {
    var html = document.getElementById('html-input').value;
    if (!html) { alert('HTMLを入力してください'); return; }
    document.getElementById('result').textContent = 'Sending... (size: ' + html.length + ' chars)';
    fetch('/admin/api/test_parse.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ html: html })
    })
    .then(function(res) { return res.text(); })
    .then(function(text) { document.getElementById('result').textContent = text; })
    .catch(function(err) { document.getElementById('result').textContent = 'Error: ' + err; });
}
</script>
</body>
</html>
