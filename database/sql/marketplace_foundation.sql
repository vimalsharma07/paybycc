-- Run only if tables/columns do NOT exist yet (e.g. production without migrations).
-- Check first: SHOW TABLES LIKE 'services';  SHOW COLUMNS FROM users LIKE 'role';

ALTER TABLE `users`
  ADD COLUMN `role` VARCHAR(20) NOT NULL DEFAULT 'customer' AFTER `is_admin`,
  ADD COLUMN `company_name` VARCHAR(255) NULL AFTER `pan_name`,
  ADD COLUMN `gstin` VARCHAR(15) NULL AFTER `company_name`,
  ADD COLUMN `address_line1` VARCHAR(255) NULL,
  ADD COLUMN `address_line2` VARCHAR(255) NULL,
  ADD COLUMN `city` VARCHAR(80) NULL,
  ADD COLUMN `state` VARCHAR(80) NULL,
  ADD COLUMN `pincode` VARCHAR(10) NULL,
  ADD COLUMN `accept_only_kyc_customers` TINYINT(1) NOT NULL DEFAULT 0,
  ADD INDEX `users_role_index` (`role`);

CREATE TABLE IF NOT EXISTS `services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_slug_unique` (`slug`),
  KEY `services_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sub_services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_services_service_id_slug_unique` (`service_id`, `slug`),
  KEY `sub_services_is_active_index` (`is_active`),
  CONSTRAINT `sub_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `service_submissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NULL,
  `proposed_service_name` VARCHAR(255) NULL,
  `proposed_sub_service_name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL,
  `admin_note` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `service_submissions_status_index` (`status`),
  CONSTRAINT `service_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_submissions_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `freelancer_services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `sub_service_id` BIGINT UNSIGNED NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `freelancer_services_user_id_sub_service_id_unique` (`user_id`, `sub_service_id`),
  CONSTRAINT `freelancer_services_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `freelancer_services_sub_service_id_foreign` FOREIGN KEY (`sub_service_id`) REFERENCES `sub_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `marketplace_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code` VARCHAR(24) NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `freelancer_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NULL,
  `sub_service_id` BIGINT UNSIGNED NULL,
  `order_amount` DECIMAL(15,2) NOT NULL,
  `platform_fee` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `gst_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `tds_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `tcs_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `net_settlement_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `currency` CHAR(3) NOT NULL DEFAULT 'INR',
  `order_status` VARCHAR(20) NOT NULL DEFAULT 'created',
  `payment_status` VARCHAR(24) NOT NULL DEFAULT 'pending',
  `settlement_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `safe_status` VARCHAR(20) NOT NULL DEFAULT 'pending_review',
  `completed_at` TIMESTAMP NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketplace_orders_order_code_unique` (`order_code`),
  KEY `marketplace_orders_order_status_index` (`order_status`),
  KEY `marketplace_orders_payment_status_index` (`payment_status`),
  KEY `marketplace_orders_settlement_status_index` (`settlement_status`),
  KEY `marketplace_orders_safe_status_index` (`safe_status`),
  KEY `marketplace_orders_customer_id_order_status_index` (`customer_id`, `order_status`),
  KEY `marketplace_orders_freelancer_id_settlement_status_index` (`freelancer_id`, `settlement_status`),
  CONSTRAINT `marketplace_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_orders_freelancer_id_foreign` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_orders_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `marketplace_orders_sub_service_id_foreign` FOREIGN KEY (`sub_service_id`) REFERENCES `sub_services` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `marketplace_settlements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `marketplace_order_id` BIGINT UNSIGNED NOT NULL,
  `freelancer_id` BIGINT UNSIGNED NOT NULL,
  `bank_id` BIGINT UNSIGNED NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `reference` VARCHAR(255) NULL,
  `settled_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `marketplace_settlements_status_index` (`status`),
  CONSTRAINT `marketplace_settlements_marketplace_order_id_foreign` FOREIGN KEY (`marketplace_order_id`) REFERENCES `marketplace_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_settlements_freelancer_id_foreign` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `marketplace_settlements_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
