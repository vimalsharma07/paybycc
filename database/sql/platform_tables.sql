-- Platform catalog & orders (MySQL). Prefer: php artisan migrate

-- services, subservices, seller_subservices, orders, settlements

CREATE TABLE IF NOT EXISTS `services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `services_slug_unique` (`slug`),
  KEY `services_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `subservices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `subservices_service_id_slug_unique` (`service_id`, `slug`),
  KEY `subservices_is_active_index` (`is_active`),
  CONSTRAINT `subservices_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `seller_subservices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `subservice_id` BIGINT UNSIGNED NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `seller_subservices_user_id_subservice_id_unique` (`user_id`, `subservice_id`),
  CONSTRAINT `seller_subservices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_subservices_subservice_id_foreign` FOREIGN KEY (`subservice_id`) REFERENCES `subservices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(24) NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `freelancer_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED NULL,
  `subservice_id` BIGINT UNSIGNED NULL,
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
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_freelancer_id_foreign` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_subservice_id_foreign` FOREIGN KEY (`subservice_id`) REFERENCES `subservices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `settlements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `freelancer_id` BIGINT UNSIGNED NOT NULL,
  `bank_id` BIGINT UNSIGNED NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `reference` VARCHAR(255) NULL,
  `settled_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `settlements_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `settlements_freelancer_id_foreign` FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `settlements_bank_id_foreign` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
