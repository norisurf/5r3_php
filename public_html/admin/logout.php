<?php
/**
 * ログアウト処理
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

initSession();
logout();
header('Location: /admin/login.php');
exit;
