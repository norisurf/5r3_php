$files = @(
  'D:/5r3/5r3_php/public_html/sitemap.php',
  'D:/5r3/5r3_php/public_html/includes/config.php',
  'D:/5r3/5r3_php/public_html/includes/db.php',
  'D:/5r3/5r3_php/public_html/includes/functions.php'
)
foreach ($f in $files) {
  $bytes = [System.IO.File]::ReadAllBytes($f)
  $hasBom = ($bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF)
  $firstHex = ($bytes[0..5] | ForEach-Object { '{0:X2}' -f $_ }) -join ' '
  Write-Host "$f : BOM=$hasBom first=$firstHex"
}
