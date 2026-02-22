    </main>
<?php
$_adminJsPath = __DIR__ . '/../js/admin.js';
$_adminJsVer  = is_file($_adminJsPath) ? filemtime($_adminJsPath) : '1';
?>
<script src="/js/admin.js?v=<?= $_adminJsVer ?>"></script>
</body>
</html>
