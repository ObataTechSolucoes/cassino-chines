  `review_notes` text DEFAULT NULL,
  `denial_reason` text DEFAULT NULL,
  `review_attachment` varchar(191) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `denial_reason` text DEFAULT NULL,
  `review_attachment` varchar(191) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
-- --------------------------------------------------------

--
-- Estrutura para tabela `benefits`
--

CREATE TABLE `benefits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `stacking_rules` json DEFAULT NULL,
  `rollover` int(11) NOT NULL DEFAULT 0,
  `cap` decimal(12,2) DEFAULT NULL,
  `conflicts` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `benefit_rules`
--

CREATE TABLE `benefit_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `benefit_id` bigint(20) UNSIGNED NOT NULL,
  `rule_type` varchar(255) NOT NULL,
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `benefit_rules_benefit_id_foreign` (`benefit_id`),
  CONSTRAINT `benefit_rules_benefit_id_foreign` FOREIGN KEY (`benefit_id`) REFERENCES `benefits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_benefits`
--

CREATE TABLE `user_benefits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `benefit_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `rollover_progress` int(11) NOT NULL DEFAULT 0,
  `credited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_benefits_user_id_foreign` (`user_id`),
  KEY `user_benefits_benefit_id_foreign` (`benefit_id`),
  CONSTRAINT `user_benefits_benefit_id_foreign` FOREIGN KEY (`benefit_id`) REFERENCES `benefits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_benefits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `affiliate_plans`
CREATE TABLE `affiliate_plans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('GGR','REV_CPA') NOT NULL,
  `ggr_share` decimal(5,4) DEFAULT NULL,
  `rev_share` decimal(5,4) DEFAULT NULL,
  `cpa_amount` decimal(12,2) DEFAULT NULL,
  `cpa_ftd_min` int(10) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `user_affiliate_settings`
CREATE TABLE `user_affiliate_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `override_type` enum('GGR','REV_CPA') DEFAULT NULL,
  `override_ggr_share` decimal(5,4) DEFAULT NULL,
  `override_rev_share` decimal(5,4) DEFAULT NULL,
  `override_cpa_amount` decimal(12,2) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_affiliate_settings_user_id_unique` (`user_id`),
  KEY `user_affiliate_settings_affiliate_plan_id_foreign` (`affiliate_plan_id`),
  CONSTRAINT `user_affiliate_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_affiliate_settings_affiliate_plan_id_foreign` FOREIGN KEY (`affiliate_plan_id`) REFERENCES `affiliate_plans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura para tabela `affiliate_commission_logs`
CREATE TABLE `affiliate_commission_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `affiliate_user_id` bigint(20) UNSIGNED NOT NULL,
  `referred_user_id` bigint(20) UNSIGNED NOT NULL,
  `period` varchar(255) NOT NULL,
  `calc_type` enum('GGR','REV','CPA') NOT NULL,
  `base_amount` decimal(14,2) NOT NULL,
  `commission_amount` decimal(14,2) NOT NULL,
  `status` enum('pending','processed','paid','failed') NOT NULL DEFAULT 'processed',
  `error_reason` varchar(255) DEFAULT NULL,
  `idempotency_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliate_commission_logs_idempotency_key_unique` (`idempotency_key`),
  KEY `affiliate_commission_logs_affiliate_user_id_foreign` (`affiliate_user_id`),
  KEY `affiliate_commission_logs_referred_user_id_foreign` (`referred_user_id`),
  CONSTRAINT `affiliate_commission_logs_affiliate_user_id_foreign` FOREIGN KEY (`affiliate_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `affiliate_commission_logs_referred_user_id_foreign` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- AUTO_INCREMENT de tabela `benefits`
--
ALTER TABLE `benefits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `benefit_rules`
--
ALTER TABLE `benefit_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `user_benefits`
--
ALTER TABLE `user_benefits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

