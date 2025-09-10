-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 05/09/2025 às 06:15
-- Versão do servidor: 10.11.10-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u378605157_phoenix999pg`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `account_withdraws`
--

CREATE TABLE `account_withdraws` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `pix_type` varchar(255) NOT NULL,
  `pix_key` varchar(255) NOT NULL,
  `document` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `account_withdraws`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `affiliate_histories`
--

CREATE TABLE `affiliate_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `inviter` int(10) UNSIGNED NOT NULL,
  `commission` decimal(20,2) NOT NULL DEFAULT 0.00,
  `commission_type` varchar(191) DEFAULT NULL,
  `deposited` tinyint(4) DEFAULT 0,
  `deposited_amount` decimal(10,2) DEFAULT 0.00,
  `losses` bigint(20) DEFAULT 0,
  `losses_amount` decimal(10,2) DEFAULT 0.00,
  `commission_paid` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `receita` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `affiliate_histories`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `affiliate_withdraws`
--

CREATE TABLE `affiliate_withdraws` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `proof` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `pix_key` varchar(191) DEFAULT NULL,
  `pix_type` varchar(191) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `bank_info` text DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL,
  `symbol` varchar(50) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `denial_reason` text DEFAULT NULL,
  `review_attachment` varchar(191) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aprove_save_settings`
--

CREATE TABLE `aprove_save_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_password_save` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_requested_at` timestamp NULL DEFAULT NULL,
  `last_request_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aprove_save_settings`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `aprove_withdrawals`
--

CREATE TABLE `aprove_withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_requested_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aprove_withdrawals`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `link` varchar(191) DEFAULT NULL,
  `image` varchar(191) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'home',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mobile_image` varchar(191) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `banners`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `baus`
--

CREATE TABLE `baus` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bau_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `caminho` varchar(255) DEFAULT NULL,
  `dataS` timestamp NULL DEFAULT NULL,
  `aberto_em` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `value_mostrar` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `baus`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `bs_pay_payments`
--

CREATE TABLE `bs_pay_payments` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `withdrawal_id` int(11) DEFAULT NULL,
  `pix_key` varchar(255) DEFAULT NULL,
  `pix_type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `observation` text DEFAULT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_select` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `categories`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `category_game`
--

CREATE TABLE `category_game` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

--
-- Despejando dados para a tabela `category_game`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `configs_playfiver`
--

CREATE TABLE `configs_playfiver` (
  `id` int(10) UNSIGNED NOT NULL,
  `rtp` decimal(5,2) DEFAULT NULL,
  `limit_enable` tinyint(1) DEFAULT 0,
  `limit_amount` decimal(10,2) DEFAULT NULL,
  `limit_hours` int(11) DEFAULT NULL,
  `bonus_enable` tinyint(1) DEFAULT 0,
  `edit` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configs_playfiver`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `code` varchar(3) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `currencies`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `currency_alloweds`
--

CREATE TABLE `currency_alloweds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Estrutura para tabela `custom_layouts`
--

CREATE TABLE `custom_layouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `font_family_default` varchar(191) DEFAULT NULL,
  `primary_color` varchar(20) NOT NULL DEFAULT '#FFFFFF',
  `title_color` varchar(20) NOT NULL DEFAULT '#ffffff',
  `text_color` varchar(20) NOT NULL DEFAULT '#98A7B5',
  `sub_text_color` varchar(20) NOT NULL DEFAULT '#656E78',
  `placeholder_color` varchar(20) NOT NULL DEFAULT '#FFFFFF',
  `background_color_cassino` varchar(20) NOT NULL DEFAULT '#24262B',
  `background_base` varchar(20) DEFAULT '#ECEFF1',
  `background_base_dark` varchar(20) DEFAULT '#24262B',
  `carousel_banners` varchar(20) DEFAULT '#1E2024',
  `carousel_banners_dark` varchar(20) DEFAULT '#1E2024',
  `sidebar_color` varchar(20) DEFAULT NULL,
  `sidebar_color_dark` varchar(20) DEFAULT NULL,
  `navtop_color` varchar(20) DEFAULT NULL,
  `navtop_color_dark` varchar(20) DEFAULT NULL,
  `side_menu` varchar(20) DEFAULT NULL,
  `side_menu_dark` varchar(20) DEFAULT NULL,
  `footer_color` varchar(20) DEFAULT NULL,
  `footer_color_dark` varchar(20) DEFAULT NULL,
  `border_radius` varchar(20) NOT NULL DEFAULT '.25rem',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `instagram` varchar(191) DEFAULT NULL,
  `facebook` varchar(191) DEFAULT NULL,
  `telegram` varchar(191) DEFAULT NULL,
  `twitter` varchar(191) DEFAULT NULL,
  `whastapp` varchar(191) DEFAULT NULL,
  `youtube` varchar(191) DEFAULT NULL,
  `search_border_color` varchar(20) NOT NULL,
  `Border_bottons_and_selected` varchar(20) NOT NULL,
  `background_bottom_navigation` varchar(20) DEFAULT NULL,
  `background_bottom_navigation_dark` varchar(20) DEFAULT NULL,
  `borders_and_dividers_colors` varchar(20) DEFAULT NULL,
  `search_back` varchar(20) DEFAULT NULL,
  `color_bt_1` varchar(20) DEFAULT NULL,
  `color_bt_2` varchar(20) DEFAULT NULL,
  `color_bt_3` varchar(20) DEFAULT NULL,
  `color_bt_4` varchar(20) DEFAULT NULL,
  `bt_1_link` varchar(191) DEFAULT NULL,
  `bt_2_link` varchar(191) DEFAULT NULL,
  `bt_3_link` varchar(191) DEFAULT NULL,
  `bt_4_link` varchar(191) DEFAULT NULL,
  `bt_5_link` varchar(191) DEFAULT NULL,
  `value_color_jackpot` varchar(20) DEFAULT NULL,
  `value_wallet_navtop` varchar(20) DEFAULT NULL,
  `bonus_color_dep` varchar(20) DEFAULT NULL,
  `colors_deposit_value` varchar(20) DEFAULT NULL,
  `color_players` varchar(20) DEFAULT NULL,
  `modal_termos_register` longtext DEFAULT NULL,
  `modal_termos_cpf` longtext DEFAULT NULL,
  `placeholder_background` varchar(20) DEFAULT NULL,
  `card_transaction` varchar(20) DEFAULT NULL,
  `back_sub_color` varchar(20) DEFAULT NULL,
  `item_sub_color` varchar(20) DEFAULT NULL,
  `text_sub_color` varchar(20) DEFAULT NULL,
  `title_sub_color` varchar(20) DEFAULT NULL,
  `botao_deposito_background_nav` varchar(20) DEFAULT NULL,
  `botao_deposito_text_nav` varchar(20) DEFAULT NULL,
  `botao_login_background_nav` varchar(20) DEFAULT NULL,
  `botao_login_text_nav` varchar(20) DEFAULT NULL,
  `botao_registro_background_nav` varchar(20) DEFAULT NULL,
  `botao_registro_text_nav` varchar(20) DEFAULT NULL,
  `botao_login_background_modal` varchar(20) DEFAULT NULL,
  `botao_login_text_modal` varchar(20) DEFAULT NULL,
  `botao_registro_background_modal` varchar(20) DEFAULT NULL,
  `botao_registro_text_modal` varchar(20) DEFAULT NULL,
  `botao_registro_border_nav` varchar(20) DEFAULT NULL,
  `botao_login_border_nav` varchar(20) DEFAULT NULL,
  `botao_deposito_border_nav` varchar(20) DEFAULT NULL,
  `invert_percentage` decimal(5,2) DEFAULT 50.00,
  `sepia_percentage` decimal(5,2) DEFAULT 5.00,
  `saturate_percentage` decimal(5,2) DEFAULT 500.00,
  `hue_rotate_deg` decimal(5,2) DEFAULT 190.00,
  `brightness_percentage` decimal(5,2) DEFAULT 100.00,
  `contrast_percentage` decimal(5,2) DEFAULT 100.00,
  `botao_deposito_text_dep` varchar(255) DEFAULT NULL,
  `botao_deposito_background_dep` varchar(255) DEFAULT NULL,
  `botao_deposito_border_dep` varchar(255) DEFAULT NULL,
  `botao_deposito_text_saq` varchar(255) DEFAULT NULL,
  `botao_deposito_background_saq` varchar(255) DEFAULT NULL,
  `botao_deposito_border_saq` varchar(255) DEFAULT NULL,
  `text_opacity` decimal(3,2) DEFAULT 1.00 COMMENT 'Opacidade do texto (0 a 1)',
  `background_color_category` varchar(20) DEFAULT NULL,
  `background_color_jackpot` varchar(20) DEFAULT NULL,
  `text_color_footer` varchar(20) DEFAULT NULL,
  `opacity_categories` varchar(20) DEFAULT NULL,
  `opacity_bottom_nav` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `custom_layouts`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `debug`
--

CREATE TABLE `debug` (
  `text` longtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `debug`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `type` varchar(191) NOT NULL,
  `proof` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `paid_at` timestamp NULL DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL,
  `symbol` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `digito_pay`
--

CREATE TABLE `digito_pay` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `withdrawal_id` int(11) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos_plataforma`
--

CREATE TABLE `eventos_plataforma` (
  `id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `caminho` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `site_visits`
--

CREATE TABLE `site_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `games`
--

CREATE TABLE `games` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` int(10) UNSIGNED NOT NULL,
  `game_server_url` varchar(191) DEFAULT 'inativo',
  `game_id` varchar(191) NOT NULL,
  `game_name` varchar(191) NOT NULL,
  `game_code` varchar(191) NOT NULL,
  `game_type` varchar(191) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `cover` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT '0',
  `technology` varchar(191) DEFAULT 'html5',
  `has_lobby` tinyint(4) NOT NULL DEFAULT 0,
  `is_mobile` tinyint(4) NOT NULL DEFAULT 0,
  `has_freespins` tinyint(4) NOT NULL DEFAULT 0,
  `has_tables` tinyint(4) NOT NULL DEFAULT 0,
  `only_demo` tinyint(4) DEFAULT 0,
  `rtp` bigint(20) NOT NULL DEFAULT 0,
  `distribution` varchar(191) NOT NULL DEFAULT 'play_fiver',
  `views` bigint(20) NOT NULL DEFAULT 0,
  `is_featured` tinyint(4) DEFAULT 0,
  `show_home` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `original` tinyint(1) NOT NULL DEFAULT 0,
  `is_influencer_mode` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `games`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `games_keys`
--

CREATE TABLE `games_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `playfiver_url` varchar(191) DEFAULT NULL,
  `playfiver_secret` varchar(191) DEFAULT NULL,
  `playfiver_code` varchar(191) DEFAULT NULL,
  `playfiver_token` varchar(191) DEFAULT NULL,
  `saldo_agente` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `games_keys`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `game_favorites`
--

CREATE TABLE `game_favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Estrutura para tabela `game_likes`
--

CREATE TABLE `game_likes` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Estrutura para tabela `game_reviews`
--

CREATE TABLE `game_reviews` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(191) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `gateways`
--

CREATE TABLE `gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `suitpay_uri` varchar(191) DEFAULT NULL,
  `suitpay_cliente_id` varchar(191) DEFAULT NULL,
  `suitpay_cliente_secret` varchar(191) DEFAULT NULL,
  `stripe_production` tinyint(4) DEFAULT 0,
  `stripe_public_key` varchar(255) DEFAULT NULL,
  `stripe_secret_key` varchar(255) DEFAULT NULL,
  `stripe_webhook_key` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `public_key` varchar(191) DEFAULT NULL,
  `private_key` varchar(191) DEFAULT NULL,
  `ezzebank_uri` varchar(191) DEFAULT NULL,
  `ezzebank_cliente_id` varchar(191) DEFAULT NULL,
  `ezzebank_cliente_secret` varchar(191) DEFAULT NULL,
  `suitpay_split` varchar(191) NOT NULL,
  `suitpay_split_name` varchar(191) NOT NULL,
  `digitopay_uri` varchar(255) DEFAULT NULL,
  `digitopay_cliente_id` varchar(255) DEFAULT NULL,
  `digitopay_cliente_secret` varchar(255) DEFAULT NULL,
  `bspay_uri` varchar(255) DEFAULT NULL,
  `bspay_cliente_id` varchar(255) DEFAULT NULL,
  `bspay_cliente_secret` varchar(255) DEFAULT NULL,
  `cnpay_uri` varchar(255) DEFAULT NULL COMMENT 'URL da API do CNPay',
  `cnpay_public_key` varchar(255) DEFAULT NULL COMMENT 'Chave pública do CNPay (x-public-key)',
  `cnpay_secret_key` varchar(255) DEFAULT NULL COMMENT 'Chave privada do CNPay (x-secret-key)',
  `cnpay_webhook_url` varchar(255) DEFAULT NULL COMMENT 'URL do webhook do CNPay',
  `ezze_user` varchar(255) DEFAULT NULL,
  `ezze_senha` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `gateways`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `ggds_spin_config`
--

CREATE TABLE `ggds_spin_config` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prizes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ggds_spin_runs`
--

CREATE TABLE `ggds_spin_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `nonce` varchar(255) NOT NULL,
  `possibilities` text NOT NULL,
  `prize` varchar(191) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ggr_games`
--

CREATE TABLE `ggr_games` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `provider` varchar(191) NOT NULL,
  `game` varchar(191) NOT NULL,
  `balance_bet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `balance_win` decimal(20,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(50) DEFAULT NULL,
  `aggregator` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ggr_games_drakon`
--

CREATE TABLE `ggr_games_drakon` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `game` varchar(255) NOT NULL,
  `balance_bet` decimal(15,2) NOT NULL,
  `balance_win` decimal(15,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ggr_games_fivers`
--

CREATE TABLE `ggr_games_fivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `provider` varchar(191) NOT NULL,
  `game` varchar(191) NOT NULL,
  `balance_bet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `balance_win` decimal(20,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ggr_games_world_slots`
--

CREATE TABLE `ggr_games_world_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `provider` varchar(191) NOT NULL,
  `game` varchar(191) NOT NULL,
  `balance_bet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `balance_win` decimal(20,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(50) NOT NULL DEFAULT 'BRL',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `liked_user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `migrations`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `missions`
--

CREATE TABLE `missions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `challenge_name` varchar(191) NOT NULL,
  `challenge_description` text NOT NULL,
  `challenge_rules` text NOT NULL,
  `challenge_type` varchar(20) NOT NULL DEFAULT 'game',
  `challenge_link` varchar(191) DEFAULT NULL,
  `challenge_start_date` datetime NOT NULL,
  `challenge_end_date` datetime NOT NULL,
  `challenge_bonus` decimal(20,2) NOT NULL DEFAULT 0.00,
  `challenge_total` bigint(20) NOT NULL DEFAULT 1,
  `challenge_currency` varchar(5) NOT NULL,
  `challenge_provider` varchar(50) DEFAULT NULL,
  `challenge_gameid` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `recargas_cumulativas` decimal(10,2) DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mission_deposit`
--

CREATE TABLE `mission_deposit` (
  `id` bigint(20) NOT NULL,
  `bonus_amount` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `name_mission` varchar(255) DEFAULT NULL,
  `deposit_acumulated_necessario` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mission_deposit`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `mission_deposit_user`
--

CREATE TABLE `mission_deposit_user` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mission_deposit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `wallet_bonus` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mission_deposit_user`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `mission_users`
--

CREATE TABLE `mission_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `mission_id` int(10) UNSIGNED NOT NULL,
  `rounds` bigint(20) DEFAULT 0,
  `rewards` decimal(10,2) DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wallet_mission` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Estrutura para tabela `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

--
-- Despejando dados para a tabela `model_has_roles`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `music`
--

CREATE TABLE `music` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `music`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `game` varchar(191) NOT NULL,
  `game_uuid` varchar(191) NOT NULL,
  `type` varchar(50) NOT NULL,
  `type_money` varchar(50) NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `providers` varchar(191) NOT NULL,
  `refunded` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `round_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hash` varchar(191) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `orders`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

--
-- Despejando dados para a tabela `permissions`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `post_notifications`
--

CREATE TABLE `post_notifications` (
  `id` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `post_notifications`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `providers`
--

CREATE TABLE `providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `rtp` bigint(20) DEFAULT 90,
  `views` bigint(20) DEFAULT 1,
  `distribution` varchar(50) DEFAULT 'play_fiver',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `providers`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `proxy_settings`
--

CREATE TABLE `proxy_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `proxy_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `proxy_host` varchar(255) DEFAULT NULL,
  `proxy_port` int(11) DEFAULT NULL,
  `proxy_username` varchar(255) DEFAULT NULL,
  `proxy_password` varchar(255) DEFAULT NULL,
  `proxy_type` enum('http','https','socks4','socks5') DEFAULT 'http',
  `proxy_verify_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `proxy_settings`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

--
-- Despejando dados para a tabela `roles`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sen_saques`
--

CREATE TABLE `sen_saques` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `valid_saque` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sen_saques`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `software_name` varchar(255) DEFAULT NULL,
  `software_description` text DEFAULT NULL,
  `software_favicon` varchar(255) DEFAULT NULL,
  `software_logo_white` varchar(255) DEFAULT NULL,
  `software_logo_black` varchar(255) DEFAULT NULL,
  `software_background` varchar(255) DEFAULT NULL,
  `currency_code` varchar(191) NOT NULL DEFAULT 'BRL',
  `decimal_format` varchar(20) NOT NULL DEFAULT 'dot',
  `currency_position` varchar(20) NOT NULL DEFAULT 'left',
  `revshare_percentage` bigint(20) DEFAULT 20,
  `ngr_percent` bigint(20) DEFAULT 20,
  `soccer_percentage` bigint(20) DEFAULT 30,
  `prefix` varchar(191) NOT NULL DEFAULT 'R$',
  `storage` varchar(191) NOT NULL DEFAULT 'local',
  `initial_bonus` bigint(20) DEFAULT 0,
  `min_deposit` decimal(10,2) DEFAULT 20.00,
  `max_deposit` decimal(10,2) DEFAULT 0.00,
  `min_withdrawal` decimal(10,2) DEFAULT 20.00,
  `max_withdrawal` decimal(10,2) DEFAULT 0.00,
  `rollover` bigint(20) DEFAULT 10,
  `rollover_deposit` bigint(20) DEFAULT 1,
  `suitpay_is_enable` tinyint(4) DEFAULT 0,
  `stripe_is_enable` tinyint(4) DEFAULT 0,
  `bspay_is_enable` tinyint(4) DEFAULT 0,
  `sharkpay_is_enable` tinyint(4) DEFAULT 0,
  `turn_on_football` tinyint(4) DEFAULT 1,
  `revshare_reverse` tinyint(1) DEFAULT 1,
  `bonus_vip` bigint(20) DEFAULT 100,
  `activate_vip_bonus` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_jackpot` varchar(255) DEFAULT NULL,
  `maintenance_mode` tinyint(4) DEFAULT 0,
  `withdrawal_limit` bigint(20) DEFAULT NULL,
  `withdrawal_period` varchar(30) DEFAULT NULL,
  `disable_spin` tinyint(1) NOT NULL DEFAULT 0,
  `perc_sub_lv1` bigint(20) NOT NULL DEFAULT 4,
  `perc_sub_lv2` bigint(20) NOT NULL DEFAULT 2,
  `perc_sub_lv3` bigint(20) NOT NULL DEFAULT 3,
  `disable_rollover` tinyint(4) DEFAULT 0,
  `rollover_protection` bigint(20) NOT NULL DEFAULT 1,
  `cpa_baseline` decimal(10,2) DEFAULT NULL,
  `cpa_value` decimal(10,2) DEFAULT NULL,
  `cpa_percentage_baseline` varchar(14) DEFAULT NULL,
  `cpa_percentage_n1` varchar(14) DEFAULT NULL,
  `cpa_percentage_n2` varchar(14) DEFAULT NULL,
  `ezzepay_is_enable` tinyint(4) DEFAULT 0,
  `digitopay_is_enable` tinyint(4) NOT NULL DEFAULT 0,
  `default_gateway` varchar(191) NOT NULL DEFAULT 'cnpay',
  `image_cassino_sidebar` varchar(255) DEFAULT NULL,
  `image_favoritos_sidebar` varchar(255) DEFAULT NULL,
  `image_wallet_sidebar` varchar(255) DEFAULT NULL,
  `image_suporte_sidebar` varchar(255) DEFAULT NULL,
  `image_promotions_sidebar` varchar(255) DEFAULT NULL,
  `image_indique_sidebar` varchar(255) DEFAULT NULL,
  `image_home_bottom` varchar(255) DEFAULT NULL,
  `image_cassino_bottom` varchar(255) DEFAULT NULL,
  `image_deposito_bottom` varchar(255) DEFAULT NULL,
  `image_convidar_bottom` varchar(255) DEFAULT NULL,
  `image_wallet_bottom` varchar(255) DEFAULT NULL,
  `image_user_nav` varchar(255) DEFAULT NULL,
  `image_home_sidebar` varchar(255) DEFAULT NULL,
  `image_menu_nav` varchar(255) NOT NULL,
  `message_home_page` varchar(255) DEFAULT NULL,
  `valor_por_bau` decimal(10,0) DEFAULT NULL,
  `deposito_minimo_bau` decimal(10,0) DEFAULT NULL,
  `cirus_baseline` decimal(20,2) NOT NULL DEFAULT 20.00,
  `cirus_aposta` decimal(20,2) NOT NULL DEFAULT 20.00,
  `cirus_valor` decimal(20,2) NOT NULL DEFAULT 20.00,
  `icon_bt_1` varchar(255) DEFAULT NULL,
  `icon_bt_2` varchar(255) DEFAULT NULL,
  `icon_bt_3` varchar(255) DEFAULT NULL,
  `icon_bt_4` varchar(255) DEFAULT NULL,
  `icon_bt_5` varchar(255) DEFAULT NULL,
  `name_bt_1` varchar(255) DEFAULT NULL,
  `name_bt_2` varchar(255) DEFAULT NULL,
  `name_bt_3` varchar(255) DEFAULT NULL,
  `name_bt_4` varchar(255) DEFAULT NULL,
  `img_bg_1` varchar(255) DEFAULT NULL,
  `icon_bt_6` varchar(255) DEFAULT NULL,
  `icon_bt_7` varchar(255) DEFAULT NULL,
  `icon_bt_8` varchar(255) DEFAULT NULL,
  `icon_wt_1` varchar(255) DEFAULT NULL,
  `icon_wt_2` varchar(255) DEFAULT NULL,
  `icon_wt_3` varchar(255) DEFAULT NULL,
  `icon_wt_4` varchar(255) DEFAULT NULL,
  `icon_wt_5` varchar(255) DEFAULT NULL,
  `saldo_ini` decimal(10,2) DEFAULT NULL,
  `ezzebank_is_enable` tinyint(4) NOT NULL DEFAULT 0,
  `modal_pop_up` text NOT NULL,
  `img_modal_pop` varchar(255) DEFAULT NULL,
  `modal_active` tinyint(4) NOT NULL DEFAULT 0,
  `icon_wt_6` varchar(255) DEFAULT NULL,
  `icon_wt_7` varchar(255) DEFAULT NULL,
  `icon_wt_8` varchar(255) DEFAULT NULL,
  `software_loading` varchar(255) DEFAULT NULL,
  `image_home_bottom_hover` varchar(255) DEFAULT NULL,
  `image_cassino_bottom_hover` varchar(255) DEFAULT NULL,
  `image_deposito_bottom_hover` varchar(255) DEFAULT NULL,
  `image_convidar_bottom_hover` varchar(255) DEFAULT NULL,
  `image_wallet_bottom_hover` varchar(255) DEFAULT NULL,
  `icon_wt_9` varchar(255) DEFAULT NULL,
  `background_perfil_top` varchar(255) DEFAULT NULL,
  `sub_background_perfil_top` varchar(255) DEFAULT NULL,
  `icon_wt_10` varchar(255) DEFAULT NULL,
  `collum_games` varchar(255) NOT NULL DEFAULT '3',
  `disable_rollover_cadastro` tinyint(4) NOT NULL DEFAULT 0,
  `rollover_cadastro` bigint(20) DEFAULT NULL,
  `disable_deposit_min` tinyint(4) NOT NULL DEFAULT 0,
  `deposit_min_saque` decimal(10,2) NOT NULL DEFAULT 20.00,
  `icon_nav_bottom_left` varchar(255) DEFAULT NULL,
  `icon_nav_bottom_right` varchar(255) DEFAULT NULL,
  `icon_bottom_right` varchar(255) DEFAULT NULL,
  `icon_bottom_left` varchar(255) DEFAULT NULL,
  `icon_wt_11` varchar(255) NOT NULL,
  `icon_wt_12` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `settings`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `setting_mails`
--

CREATE TABLE `setting_mails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `software_smtp_type` varchar(30) DEFAULT NULL,
  `software_smtp_mail_host` varchar(100) DEFAULT NULL,
  `software_smtp_mail_port` varchar(30) DEFAULT NULL,
  `software_smtp_mail_username` varchar(191) DEFAULT NULL,
  `software_smtp_mail_password` varchar(100) DEFAULT NULL,
  `software_smtp_mail_encryption` varchar(30) DEFAULT NULL,
  `software_smtp_mail_from_address` varchar(191) DEFAULT NULL,
  `software_smtp_mail_from_name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `setting_mails`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `slider_texts`
--

CREATE TABLE `slider_texts` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `slider_texts`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `sub_affiliates`
--

CREATE TABLE `sub_affiliates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `affiliate_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Estrutura para tabela `suit_pay_payments`
--

CREATE TABLE `suit_pay_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `withdrawal_id` bigint(20) UNSIGNED NOT NULL,
  `pix_key` varchar(191) DEFAULT NULL,
  `pix_type` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observation` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `suit_pay_payments`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `system_wallets`
--

CREATE TABLE `system_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` char(32) NOT NULL,
  `balance` decimal(27,12) NOT NULL DEFAULT 0.000000000000,
  `balance_min` decimal(27,12) NOT NULL DEFAULT 10000.100000000000,
  `pay_upto_percentage` decimal(4,2) NOT NULL DEFAULT 45.00,
  `mode` enum('balance_min','percentage') NOT NULL DEFAULT 'percentage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

--
-- Despejando dados para a tabela `system_wallets`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(100) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `price` decimal(20,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(20) NOT NULL DEFAULT 'usd',
  `status` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `accept_bonus` tinyint(1) NOT NULL DEFAULT 1,
  `token` varchar(32) DEFAULT NULL,
  `gateway_identifier` varchar(255) DEFAULT NULL,
  `gateway_transaction_id` varchar(255) DEFAULT NULL,
  `gateway_name` varchar(191) DEFAULT NULL COMMENT 'Nome do gateway usado na transação',
  `idUnico` varchar(191) DEFAULT NULL COMMENT 'ID único da transação',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Resposta completa do gateway' CHECK (json_valid(`gateway_response`))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `transactions`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `oauth_id` varchar(191) DEFAULT NULL,
  `oauth_type` varchar(191) DEFAULT NULL,
  `avatar` varchar(191) DEFAULT 'uploads/bored_ape.png',
  `last_name` varchar(191) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `logged_in` tinyint(4) NOT NULL DEFAULT 0,
  `banned` tinyint(4) NOT NULL DEFAULT 0,
  `inviter` int(11) DEFAULT NULL,
  `inviter_code` varchar(25) DEFAULT NULL,
  `affiliate_revenue_share` bigint(20) NOT NULL DEFAULT 2,
  `affiliate_revenue_share_fake` bigint(20) DEFAULT NULL,
  `affiliate_cpa` decimal(20,2) NOT NULL DEFAULT 10.00,
  `affiliate_bau_baseline` decimal(20,2) NOT NULL DEFAULT 20.00,
  `affiliate_bau_value` decimal(20,2) NOT NULL DEFAULT 20.00,
  `affiliate_bau_aposta` decimal(20,2) NOT NULL DEFAULT 20.00,
  `affiliate_baseline` decimal(20,2) NOT NULL DEFAULT 40.00,
  `is_demo_agent` tinyint(4) NOT NULL DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `language` varchar(191) NOT NULL DEFAULT 'pt_BR',
  `role_id` int(11) DEFAULT 3,
  `influencer_mode` tinyint(4) NOT NULL DEFAULT 0,
  `facebook_id` varchar(191) NOT NULL,
  `whatsapp_id` varchar(191) NOT NULL,
  `telegram_id` varchar(191) NOT NULL,
  `aniversario` varchar(191) NOT NULL,
  `utilizou_bonus_cadastro` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `users`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `vips`
--

CREATE TABLE `vips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bet_symbol` varchar(255) NOT NULL,
  `bet_level` bigint(20) NOT NULL DEFAULT 1,
  `bet_required` bigint(20) DEFAULT NULL,
  `bet_period` varchar(191) DEFAULT NULL,
  `bet_bonus` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `vips`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `vip_users`
--

CREATE TABLE `vip_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `vip_id` int(10) UNSIGNED NOT NULL,
  `level` bigint(20) NOT NULL,
  `points` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=FIXED;

--
-- Despejando dados para a tabela `vip_users`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `currency` varchar(20) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `balance` decimal(20,2) NOT NULL DEFAULT 0.00,
  `balance_bonus_rollover` decimal(10,2) DEFAULT 0.00,
  `balance_deposit_rollover` decimal(10,2) DEFAULT 0.00,
  `balance_withdrawal` decimal(10,2) DEFAULT 0.00,
  `balance_bonus` decimal(20,2) NOT NULL DEFAULT 0.00,
  `balance_cryptocurrency` decimal(20,8) NOT NULL DEFAULT 0.00000000,
  `balance_demo` decimal(20,8) DEFAULT 1.00000000,
  `refer_rewards` decimal(20,2) NOT NULL DEFAULT 0.00,
  `hide_balance` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_bet` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_won` bigint(20) NOT NULL DEFAULT 0,
  `total_lose` bigint(20) NOT NULL DEFAULT 0,
  `last_won` bigint(20) NOT NULL DEFAULT 0,
  `last_lose` bigint(20) NOT NULL DEFAULT 0,
  `vip_level` bigint(20) DEFAULT 0,
  `vip_points` bigint(20) DEFAULT 0,
  `vip_wallet` decimal(20,2) DEFAULT 0.00,
  `mission_deposit_wallet` decimal(20,2) NOT NULL DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Despejando dados para a tabela `wallets`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `wallet_changes`
--

CREATE TABLE `wallet_changes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `change` varchar(50) DEFAULT NULL,
  `value_bonus` decimal(20,2) NOT NULL DEFAULT 0.00,
  `value_total` decimal(20,2) NOT NULL DEFAULT 0.00,
  `value_roi` decimal(20,2) NOT NULL DEFAULT 0.00,
  `value_entry` decimal(20,2) NOT NULL DEFAULT 0.00,
  `refer_rewards` decimal(20,2) NOT NULL DEFAULT 0.00,
  `game` varchar(191) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hash` varchar(32) NOT NULL,
  `gateway` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `webhook_logs`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `websockets_statistics_entries`
--

CREATE TABLE `websockets_statistics_entries` (
  `id` int(10) UNSIGNED NOT NULL,
  `app_id` varchar(191) NOT NULL,
  `peak_connection_count` int(11) NOT NULL,
  `websocket_message_count` int(11) NOT NULL,
  `api_message_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estrutura para tabela `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `type` varchar(191) DEFAULT NULL,
  `proof` varchar(191) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `paid_at` timestamp NULL DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `pix_key` varchar(191) DEFAULT NULL,
  `pix_type` varchar(191) DEFAULT NULL,
  `bank_info` text DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL,
  `symbol` varchar(50) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `denial_reason` text DEFAULT NULL,
  `review_attachment` varchar(191) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(50) NOT NULL,
  `module` varchar(120) DEFAULT NULL,
  `target_type` varchar(160) DEFAULT NULL,
  `target_id` varchar(64) DEFAULT NULL,
  `route` varchar(191) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `request` longtext DEFAULT NULL,
  `before` longtext DEFAULT NULL,
  `after` longtext DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `account_withdraws`
--
ALTER TABLE `account_withdraws`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `affiliate_histories`
--
ALTER TABLE `affiliate_histories`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `affiliate_histories_user_id_index` (`user_id`) USING BTREE,
  ADD KEY `affiliate_histories_inviter_index` (`inviter`) USING BTREE;

--
-- Índices de tabela `affiliate_withdraws`
--
ALTER TABLE `affiliate_withdraws`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `affiliate_withdraws_user_id_foreign` (`user_id`) USING BTREE;

--
-- Índices de tabela `aprove_save_settings`
--
ALTER TABLE `aprove_save_settings`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `aprove_withdrawals`
--
ALTER TABLE `aprove_withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `baus`
--
ALTER TABLE `baus`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `bs_pay_payments`
--
ALTER TABLE `bs_pay_payments`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `casino_categories_slug_unique` (`slug`) USING BTREE;

--
-- Índices de tabela `category_game`
--
ALTER TABLE `category_game`
  ADD KEY `category_games_category_id_foreign` (`category_id`) USING BTREE,
  ADD KEY `category_games_game_id_foreign` (`game_id`) USING BTREE;

--
-- Índices de tabela `configs_playfiver`
--
ALTER TABLE `configs_playfiver`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `currency_alloweds`
--
ALTER TABLE `currency_alloweds`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `currency_alloweds_currency_id_foreign` (`currency_id`) USING BTREE;

--
-- Índices de tabela `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `deposits_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `deposits_status_created_idx` (`status`, `created_at`) USING BTREE,
  ADD KEY `deposits_user_status_idx` (`user_id`, `status`) USING BTREE,
  ADD KEY `deposits_paid_at_idx` (`paid_at`) USING BTREE;

--
-- Índices de tabela `digito_pay`
--
ALTER TABLE `digito_pay`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `eventos_plataforma`
--
ALTER TABLE `eventos_plataforma`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `site_visits`
--
ALTER TABLE `site_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_visits_visited_at_idx` (`visited_at`),
  ADD KEY `site_visits_user_id_idx` (`user_id`);

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_idx` (`user_id`),
  ADD KEY `audit_logs_event_idx` (`event`),
  ADD KEY `audit_logs_module_idx` (`module`),
  ADD KEY `audit_logs_target_id_idx` (`target_id`),
  ADD KEY `audit_logs_created_at_idx` (`created_at`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`) USING BTREE;

--
-- Índices de tabela `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD KEY `games_provider_id_index` (`provider_id`),
  ADD KEY `games_game_code_index` (`game_code`);

--
-- Índices de tabela `games_keys`
--
ALTER TABLE `games_keys`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `game_favorites`
--
ALTER TABLE `game_favorites`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `game_favorites_user_id_game_id_unique` (`user_id`,`game_id`) USING BTREE,
  ADD KEY `game_favorites_game_id_foreign` (`game_id`) USING BTREE;

--
-- Índices de tabela `game_likes`
--
ALTER TABLE `game_likes`
  ADD UNIQUE KEY `game_likes_user_id_game_id_unique` (`user_id`,`game_id`) USING BTREE,
  ADD KEY `game_likes_game_id_foreign` (`game_id`) USING BTREE;

--
-- Índices de tabela `game_reviews`
--
ALTER TABLE `game_reviews`
  ADD UNIQUE KEY `game_reviews_user_id_game_id_unique` (`user_id`,`game_id`) USING BTREE,
  ADD KEY `game_reviews_game_id_foreign` (`game_id`) USING BTREE;

--
-- Índices de tabela `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `ggds_spin_config`
--
ALTER TABLE `ggds_spin_config`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `ggds_spin_runs`
--
ALTER TABLE `ggds_spin_runs`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `ggr_games`
--
ALTER TABLE `ggr_games`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `ggr_games_fivers_user_id_index` (`user_id`) USING BTREE;

--
-- Índices de tabela `ggr_games_drakon`
--
ALTER TABLE `ggr_games_drakon`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ggr_games_world_slots`
--
ALTER TABLE `ggr_games_world_slots`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `ggr_games_world_slots_user_id_index` (`user_id`) USING BTREE;

--
-- Índices de tabela `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `likes_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `likes_liked_user_id_foreign` (`liked_user_id`) USING BTREE;

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `mission_deposit`
--
ALTER TABLE `mission_deposit`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mission_deposit_user`
--
ALTER TABLE `mission_deposit_user`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mission_users`
--
ALTER TABLE `mission_users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `mission_users_user_id_index` (`user_id`) USING BTREE,
  ADD KEY `mission_users_mission_id_index` (`mission_id`) USING BTREE;

--
-- Índices de tabela `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Índices de tabela `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`) USING BTREE,
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`) USING BTREE;

--
-- Índices de tabela `music`
--
ALTER TABLE `music`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`) USING BTREE;

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `orders_user_id_foreign` (`user_id`) USING BTREE;

--
-- Índices de tabela `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`) USING BTREE;

--
-- Índices de tabela `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Índices de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`) USING BTREE,
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`) USING BTREE;

--
-- Índices de tabela `post_notifications`
--
ALTER TABLE `post_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`) USING BTREE;

--
-- Índices de tabela `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`) USING BTREE,
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`) USING BTREE;

--
-- Índices de tabela `sen_saques`
--
ALTER TABLE `sen_saques`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `setting_mails`
--
ALTER TABLE `setting_mails`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `slider_texts`
--
ALTER TABLE `slider_texts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sub_affiliates`
--
ALTER TABLE `sub_affiliates`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `sub_affiliates_affiliate_id_index` (`affiliate_id`) USING BTREE,
  ADD KEY `sub_affiliates_user_id_index` (`user_id`) USING BTREE;

--
-- Índices de tabela `suit_pay_payments`
--
ALTER TABLE `suit_pay_payments`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `suit_pay_payments_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `suit_pay_payments_withdrawal_id_foreign` (`withdrawal_id`) USING BTREE;

--
-- Índices de tabela `system_wallets`
--
ALTER TABLE `system_wallets`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `transactions_user_id_index` (`user_id`) USING BTREE,
  ADD KEY `idx_gateway_name` (`gateway_name`),
  ADD KEY `idx_id_unico` (`idUnico`),
  ADD KEY `idx_gateway_identifier` (`gateway_identifier`(250)),
  ADD KEY `idx_gateway_transaction_id` (`gateway_transaction_id`(250)),
  ADD KEY `transactions_status_created_idx` (`status`, `created_at`) USING BTREE,
  ADD KEY `transactions_user_status_idx` (`user_id`, `status`) USING BTREE,
  ADD KEY `transactions_payment_id_idx` (`payment_id`) USING BTREE;

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_email_unique` (`email`) USING BTREE,
  ADD UNIQUE KEY `users_phone_unique` (`phone`) USING BTREE;

--
-- Índices de tabela `vips`
--
ALTER TABLE `vips`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `vip_users`
--
ALTER TABLE `vip_users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `vip_users_user_id_index` (`user_id`) USING BTREE,
  ADD KEY `vip_users_vip_id_index` (`vip_id`) USING BTREE;

--
-- Índices de tabela `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `wallets_user_id_index` (`user_id`) USING BTREE;

--
-- Índices de tabela `wallet_changes`
--
ALTER TABLE `wallet_changes`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `wallet_changes_user_id_foreign` (`user_id`) USING BTREE;

--
-- Índices de tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `webhook_logs_hash_unique` (`hash`),
  ADD KEY `webhook_logs_hash_index` (`hash`),
  ADD KEY `webhook_logs_gateway_index` (`gateway`),
  ADD KEY `webhook_logs_gateway_created_at_index` (`gateway`,`created_at`),
  ADD KEY `webhook_logs_created_at_index` (`created_at`);

--
-- Índices de tabela `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices de tabela `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `withdrawals_user_id_foreign` (`user_id`) USING BTREE,
  ADD KEY `withdrawals_status_created_idx` (`status`, `created_at`) USING BTREE,
  ADD KEY `withdrawals_user_status_idx` (`user_id`, `status`) USING BTREE,
  ADD KEY `withdrawals_paid_at_idx` (`paid_at`) USING BTREE;

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `account_withdraws`
--
ALTER TABLE `account_withdraws`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `affiliate_histories`
--
ALTER TABLE `affiliate_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT de tabela `affiliate_withdraws`
--
ALTER TABLE `affiliate_withdraws`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aprove_withdrawals`
--
ALTER TABLE `aprove_withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de tabela `baus`
--
ALTER TABLE `baus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de tabela `bs_pay_payments`
--
ALTER TABLE `bs_pay_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `configs_playfiver`
--
ALTER TABLE `configs_playfiver`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de tabela `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de tabela `currency_alloweds`
--
ALTER TABLE `currency_alloweds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de tabela `digito_pay`
--
ALTER TABLE `digito_pay`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `eventos_plataforma`
--
ALTER TABLE `eventos_plataforma`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `games`
--
ALTER TABLE `games`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1449;

--
-- AUTO_INCREMENT de tabela `games_keys`
--
ALTER TABLE `games_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `game_favorites`
--
ALTER TABLE `game_favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `ggds_spin_config`
--
ALTER TABLE `ggds_spin_config`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ggds_spin_runs`
--
ALTER TABLE `ggds_spin_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ggr_games`
--
ALTER TABLE `ggr_games`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ggr_games_drakon`
--
ALTER TABLE `ggr_games_drakon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ggr_games_world_slots`
--
ALTER TABLE `ggr_games_world_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de tabela `missions`
--
ALTER TABLE `missions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mission_deposit`
--
ALTER TABLE `mission_deposit`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `mission_deposit_user`
--
ALTER TABLE `mission_deposit_user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT de tabela `mission_users`
--
ALTER TABLE `mission_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `music`
--
ALTER TABLE `music`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1129;

--
-- AUTO_INCREMENT de tabela `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `post_notifications`
--
ALTER TABLE `post_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `providers`
--
ALTER TABLE `providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `sen_saques`
--
ALTER TABLE `sen_saques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `setting_mails`
--
ALTER TABLE `setting_mails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `slider_texts`
--
ALTER TABLE `slider_texts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `sub_affiliates`
--
ALTER TABLE `sub_affiliates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `suit_pay_payments`
--
ALTER TABLE `suit_pay_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `system_wallets`
--
ALTER TABLE `system_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=233;

--
-- AUTO_INCREMENT de tabela `vips`
--
ALTER TABLE `vips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `vip_users`
--
ALTER TABLE `vip_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- AUTO_INCREMENT de tabela `wallet_changes`
--
ALTER TABLE `wallet_changes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `websockets_statistics_entries`
--
ALTER TABLE `websockets_statistics_entries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `site_visits`
--
ALTER TABLE `site_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
