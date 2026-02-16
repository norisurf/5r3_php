-- 車両テーブルにソフトデリート用カラムを追加
-- 本番環境のphpMyAdminで実行してください

ALTER TABLE `vehicles` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL AFTER `display_on_lp`;
ALTER TABLE `vehicles` ADD KEY `idx_deleted_at` (`deleted_at`);
