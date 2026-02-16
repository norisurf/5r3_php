-- 5R3 CARS PHP版 データベーススキーマ
-- RSサーバーのphpMyAdminで実行してください

CREATE TABLE IF NOT EXISTS `vehicles` (
    `id` VARCHAR(30) NOT NULL,
    `manage_number` VARCHAR(20) NOT NULL,
    `title` VARCHAR(500) NOT NULL DEFAULT '',
    `price` INT NOT NULL DEFAULT 0,
    `images` TEXT,
    `basic_info` TEXT,
    `detailed_info` TEXT,
    `equipment` TEXT,
    `description` TEXT,
    `display_on_lp` TINYINT(1) NOT NULL DEFAULT 0,
    `deleted_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_manage_number` (`manage_number`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_display_on_lp` (`display_on_lp`),
    KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `banners` (
    `id` VARCHAR(30) NOT NULL,
    `mode` VARCHAR(10) NOT NULL DEFAULT 'manual',
    `image_url` VARCHAR(500) NOT NULL DEFAULT '',
    `link_url` VARCHAR(500) NOT NULL DEFAULT '',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 初期管理者アカウント (パスワード: admin5r3)
-- 本番環境では必ず変更してください
INSERT INTO `admins` (`username`, `password_hash`) VALUES
('admin', '$2y$10$dummy_hash_replace_on_setup');
