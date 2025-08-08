-- Real Estate Inquiry System Database Schema
-- Created: 2024
-- Description: Secure database schema for real estate inquiry system

CREATE DATABASE IF NOT EXISTS `real_estate` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `real_estate`;

-- Inquiries table
CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `property_type` varchar(50) NOT NULL COMMENT '物件種別',
    `prefecture` varchar(50) NOT NULL COMMENT '都道府県',
    `city` varchar(100) NOT NULL COMMENT '市区町村',
    `town` varchar(100) NOT NULL COMMENT '町名',
    `address_detail` text COMMENT '詳細住所',
    `mansion_name` varchar(200) COMMENT 'マンション名',
    `room_number` varchar(50) COMMENT '号室',
    `layout` varchar(50) COMMENT '間取り',
    `area` varchar(100) COMMENT '専有面積',
    `construction_year` varchar(100) NOT NULL COMMENT '築年',
    `current_status` varchar(100) NOT NULL COMMENT '現状',
    `relationship` varchar(200) NOT NULL COMMENT '売却物件との関係',
    `loan_balance` varchar(50) COMMENT '住宅ローン残高',
    `desired_price` varchar(50) COMMENT '希望買取金額',
    `name` varchar(100) NOT NULL COMMENT 'お名前',
    `furigana` varchar(100) NOT NULL COMMENT 'フリガナ',
    `gender` varchar(20) NOT NULL COMMENT '性別',
    `phone` varchar(20) NOT NULL COMMENT '電話番号',
    `contact_time` varchar(50) COMMENT 'ご希望の連絡時間帯',
    `email` varchar(255) NOT NULL COMMENT 'メールアドレス',
    `contact_method` varchar(100) COMMENT '希望する連絡方法',
    `assessment_method` varchar(100) COMMENT '希望査定方法',
    `ip_address` varchar(45) COMMENT 'IPアドレス',
    `user_agent` text COMMENT 'ユーザーエージェント',
    `status` enum('new','processing','completed','cancelled') DEFAULT 'new' COMMENT 'ステータス',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
    PRIMARY KEY (`id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_status` (`status`),
    KEY `idx_property_type` (`property_type`),
    KEY `idx_prefecture` (`prefecture`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='不動産査定お問い合わせ';

-- Admin users table (for future admin panel)
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL UNIQUE,
    `email` varchar(255) NOT NULL UNIQUE,
    `password_hash` varchar(255) NOT NULL,
    `role` enum('admin','manager','viewer') DEFAULT 'viewer',
    `is_active` tinyint(1) DEFAULT 1,
    `last_login` timestamp NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_username` (`username`),
    KEY `idx_email` (`email`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理者ユーザー';

-- Session management table
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` varchar(128) NOT NULL,
    `data` text NOT NULL,
    `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `ip_address` varchar(45),
    `user_agent` text,
    PRIMARY KEY (`id`),
    KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='セッション管理';

-- Security log table
CREATE TABLE IF NOT EXISTS `security_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `event_type` varchar(50) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `user_agent` text,
    `details` json,
    `severity` enum('low','medium','high','critical') DEFAULT 'low',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_type` (`event_type`),
    KEY `idx_ip_address` (`ip_address`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_severity` (`severity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='セキュリティログ';

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `role`) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE `username` = `username`;