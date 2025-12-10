-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 06:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assessment_platform1`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics_test_summary`
--

CREATE TABLE `analytics_test_summary` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `candidates_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `avg_score` decimal(10,2) DEFAULT NULL,
  `p90_score` decimal(10,2) DEFAULT NULL,
  `p10_score` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `resource_type` varchar(150) NOT NULL,
  `resource_id` varchar(100) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `external_id` varchar(100) DEFAULT NULL,
  `status` enum('invited','active','completed','archived') DEFAULT 'invited',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_groups`
--

CREATE TABLE `candidate_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_group_members`
--

CREATE TABLE `candidate_group_members` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `integrations`
--

CREATE TABLE `integrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `type` enum('sso','hris','ats') NOT NULL,
  `provider` varchar(100) NOT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `status` enum('connected','disconnected','error') DEFAULT 'disconnected',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `subscription_id` int(10) UNSIGNED NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `period_start` datetime NOT NULL,
  `period_end` datetime NOT NULL,
  `status` enum('draft','open','paid','void') DEFAULT 'open',
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `stem` text NOT NULL,
  `type` enum('single_choice','multiple_choice','text','numeric','likert','matrix') NOT NULL DEFAULT 'single_choice',
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `default_weight` decimal(8,2) DEFAULT 1.00,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `tenant_id`, `stem`, `type`, `difficulty`, `default_weight`, `metadata`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(2, 1, 'aaaaa', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-09 13:18:33', '2025-12-09 13:33:20'),
(3, 1, 'qqq', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-09 13:33:09', '2025-12-09 13:33:09'),
(4, 1, 'qqq', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(5, 1, 'qwerty', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(6, 1, 'asdfgh', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(7, 1, 'qwerty', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(8, 1, 'asdfgh', 'single_choice', 'medium', 1.00, NULL, 1, 1, '2025-12-10 04:58:55', '2025-12-10 04:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `item_choices`
--

CREATE TABLE `item_choices` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(10) DEFAULT NULL,
  `text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `score` decimal(8,2) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_choices`
--

INSERT INTO `item_choices` (`id`, `item_id`, `label`, `text`, `is_correct`, `score`, `sort_order`, `created_at`, `updated_at`) VALUES
(13, 3, NULL, 'eee', 0, NULL, 3, '2025-12-09 13:33:09', '2025-12-09 13:33:09'),
(14, 3, NULL, 'rrrr', 0, NULL, 4, '2025-12-09 13:33:09', '2025-12-09 13:33:09'),
(15, 3, NULL, 'www', 0, NULL, 1, '2025-12-09 13:33:09', '2025-12-09 13:33:09'),
(16, 3, NULL, 'qqq', 0, NULL, 2, '2025-12-09 13:33:09', '2025-12-09 13:33:09'),
(17, 2, NULL, 'aaa', 0, NULL, 1, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(18, 2, NULL, 'sss', 0, NULL, 2, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(19, 2, NULL, 'ddd', 0, NULL, 3, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(20, 2, NULL, 'fff', 0, NULL, 4, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(21, 4, NULL, 'eee', 0, NULL, 3, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(22, 4, NULL, 'rrrr', 0, NULL, 4, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(23, 4, NULL, 'www', 0, NULL, 1, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(24, 4, NULL, 'qqq', 0, NULL, 2, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(25, 5, NULL, 'eee', 0, NULL, 3, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(26, 5, NULL, 'rrr', 0, NULL, 4, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(27, 5, NULL, 'qqqq', 0, NULL, 1, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(28, 5, NULL, 'www', 0, NULL, 2, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(29, 6, NULL, 'dddd', 0, NULL, 3, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(30, 6, NULL, 'ffff', 0, NULL, 4, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(31, 6, NULL, 'aaa', 0, NULL, 1, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(32, 6, NULL, 'sss', 0, NULL, 2, '2025-12-10 04:58:33', '2025-12-10 04:58:33'),
(33, 7, NULL, 'eee', 0, NULL, 3, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(34, 7, NULL, 'rrr', 0, NULL, 4, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(35, 7, NULL, 'qqqq', 0, NULL, 1, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(36, 7, NULL, 'www', 0, NULL, 2, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(37, 8, NULL, 'dddd', 0, NULL, 3, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(38, 8, NULL, 'ffff', 0, NULL, 4, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(39, 8, NULL, 'aaa', 0, NULL, 1, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(40, 8, NULL, 'sss', 0, NULL, 2, '2025-12-10 04:58:55', '2025-12-10 04:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `item_media`
--

CREATE TABLE `item_media` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `type` enum('image','audio','video') NOT NULL,
  `url` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_tags`
--

CREATE TABLE `item_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_tag_pivot`
--

CREATE TABLE `item_tag_pivot` (
  `item_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_roles`
--

CREATE TABLE `job_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `template_id` int(10) UNSIGNED DEFAULT NULL,
  `channel` enum('email','sms') NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `status` enum('queued','sent','failed') DEFAULT 'queued',
  `error_message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `channel` enum('email','sms') NOT NULL DEFAULT 'email',
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `module`, `action`, `description`) VALUES
(1, 'dashboard.view', 'dashboard', 'view', 'View dashboard'),
(2, 'dashboard.create', 'dashboard', 'create', 'Create dashboard'),
(3, 'dashboard.edit', 'dashboard', 'edit', 'Edit dashboard'),
(4, 'dashboard.delete', 'dashboard', 'delete', 'Delete dashboard'),
(5, 'tests.view', 'tests', 'view', 'View tests'),
(6, 'tests.create', 'tests', 'create', 'Create tests'),
(7, 'tests.edit', 'tests', 'edit', 'Edit tests'),
(8, 'tests.delete', 'tests', 'delete', 'Delete tests'),
(9, 'items.view', 'items', 'view', 'View items'),
(10, 'items.create', 'items', 'create', 'Create items'),
(11, 'items.edit', 'items', 'edit', 'Edit items'),
(12, 'items.delete', 'items', 'delete', 'Delete items'),
(13, 'psychometrics.view', 'psychometrics', 'view', 'View psychometrics'),
(14, 'psychometrics.create', 'psychometrics', 'create', 'Create psychometrics'),
(15, 'psychometrics.edit', 'psychometrics', 'edit', 'Edit psychometrics'),
(16, 'psychometrics.delete', 'psychometrics', 'delete', 'Delete psychometrics'),
(17, 'proctoring.view', 'proctoring', 'view', 'View proctoring'),
(18, 'proctoring.create', 'proctoring', 'create', 'Create proctoring'),
(19, 'proctoring.edit', 'proctoring', 'edit', 'Edit proctoring'),
(20, 'proctoring.delete', 'proctoring', 'delete', 'Delete proctoring'),
(21, 'grading.view', 'grading', 'view', 'View grading'),
(22, 'grading.create', 'grading', 'create', 'Create grading'),
(23, 'grading.edit', 'grading', 'edit', 'Edit grading'),
(24, 'grading.delete', 'grading', 'delete', 'Delete grading'),
(25, 'reports.view', 'reports', 'view', 'View reports'),
(26, 'reports.create', 'reports', 'create', 'Create reports'),
(27, 'reports.edit', 'reports', 'edit', 'Edit reports'),
(28, 'reports.delete', 'reports', 'delete', 'Delete reports'),
(29, 'users.view', 'users', 'view', 'View users'),
(30, 'users.create', 'users', 'create', 'Create users'),
(31, 'users.edit', 'users', 'edit', 'Edit users'),
(32, 'users.delete', 'users', 'delete', 'Delete users'),
(33, 'roles.view', 'roles', 'view', 'View roles'),
(34, 'roles.create', 'roles', 'create', 'Create roles'),
(35, 'roles.edit', 'roles', 'edit', 'Edit roles'),
(36, 'roles.delete', 'roles', 'delete', 'Delete roles'),
(37, 'settings.view', 'settings', 'view', 'View settings'),
(38, 'settings.create', 'settings', 'create', 'Create settings'),
(39, 'settings.edit', 'settings', 'edit', 'Edit settings'),
(40, 'settings.delete', 'settings', 'delete', 'Delete settings');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api-token', 'd9f793c2859e09835b8a8f46314e7bdf201b3963f5236b7df3a73470f75e146e', '[\"*\"]', NULL, NULL, '2025-12-08 02:00:21', '2025-12-08 02:00:21'),
(2, 'App\\Models\\User', 1, 'api-token', '4d05ec47ffae41750d030256e0f4a542dea587b636b97eaa1cb6d191b02cad7c', '[\"*\"]', NULL, NULL, '2025-12-08 02:36:08', '2025-12-08 02:36:08'),
(3, 'App\\Models\\User', 1, 'api-token', '69d4ebad09b1fb4f357e84489ff99026fe311220ff435267dd413cf0f532a967', '[\"*\"]', '2025-12-08 08:02:06', NULL, '2025-12-08 02:39:35', '2025-12-08 08:02:06'),
(4, 'App\\Models\\User', 1, 'api-token', '2f67b011538079467ad0dd94079057126d773570ebd1c0b81a460bf3dbea8c49', '[\"*\"]', NULL, NULL, '2025-12-09 01:08:40', '2025-12-09 01:08:40'),
(5, 'App\\Models\\User', 1, 'api-token', '1edfb75d9480a9eb09cda8da7fe5f8d57eb3270f9a45af1ff23821488e751b6b', '[\"*\"]', '2025-12-09 03:41:25', NULL, '2025-12-09 01:09:22', '2025-12-09 03:41:25'),
(6, 'App\\Models\\User', 1, 'api-token', 'a53db9a332177438a993807e2b96596a2c3009cdf7457e2c37bb0fa1c6d421da', '[\"*\"]', '2025-12-09 08:06:08', NULL, '2025-12-09 04:00:01', '2025-12-09 08:06:08'),
(7, 'App\\Models\\User', 1, 'api-token', '965fd73e135703cd14a5fba541d15ee45ab24b5ec1d56f1668cd75e53d38f986', '[\"*\"]', '2025-12-09 23:41:47', NULL, '2025-12-09 23:27:29', '2025-12-09 23:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price_monthly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_yearly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_seats` int(10) UNSIGNED DEFAULT NULL,
  `max_storage_gb` int(10) UNSIGNED DEFAULT NULL,
  `max_proctoring_hours` int(10) UNSIGNED DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proctoring_flags`
--

CREATE TABLE `proctoring_flags` (
  `id` int(10) UNSIGNED NOT NULL,
  `proctoring_session_id` int(10) UNSIGNED NOT NULL,
  `flag_type` varchar(100) NOT NULL,
  `severity` enum('low','medium','high') DEFAULT 'low',
  `timestamp_sec` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proctoring_messages`
--

CREATE TABLE `proctoring_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `proctoring_session_id` int(10) UNSIGNED NOT NULL,
  `sender_type` enum('proctor','candidate','system') NOT NULL,
  `sender_id` int(10) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proctoring_sessions`
--

CREATE TABLE `proctoring_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `test_attempt_id` int(10) UNSIGNED NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `status` enum('live','completed','terminated','paused') DEFAULT 'live',
  `started_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `flags_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `video_url` varchar(500) DEFAULT NULL,
  `device_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_info`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psychometric_items`
--

CREATE TABLE `psychometric_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `scale_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `reverse_scored` tinyint(1) NOT NULL DEFAULT 0,
  `weight` decimal(8,2) DEFAULT 1.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psychometric_models`
--

CREATE TABLE `psychometric_models` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','active','archived') DEFAULT 'draft',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psychometric_norm_groups`
--

CREATE TABLE `psychometric_norm_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `model_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `population` varchar(255) DEFAULT NULL,
  `sample_size` int(10) UNSIGNED DEFAULT NULL,
  `stats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stats`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `psychometric_scales`
--

CREATE TABLE `psychometric_scales` (
  `id` int(10) UNSIGNED NOT NULL,
  `model_id` int(10) UNSIGNED NOT NULL,
  `parent_scale_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `tenant_id`, `name`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', 'Full Access', 0, '2025-12-09 07:17:47', '2025-12-09 07:17:47'),
(2, 1, 'Manager', 'No delete Assess', 0, '2025-12-09 07:21:18', '2025-12-09 10:01:05'),
(5, 1, 'User', 'User', 0, '2025-12-09 09:30:22', '2025-12-09 09:30:22'),
(6, 1, 'User 11', 'User', 0, '2025-12-09 09:30:45', '2025-12-09 09:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 6),
(2, 7),
(2, 9),
(2, 10),
(2, 11),
(2, 13),
(2, 14),
(2, 15),
(2, 17),
(2, 18),
(2, 19),
(2, 21),
(2, 22),
(2, 23),
(2, 25),
(2, 26),
(2, 27),
(2, 29),
(2, 30),
(2, 31),
(2, 33),
(2, 34),
(2, 35),
(2, 37),
(2, 38),
(2, 39);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `status` enum('trial','active','past_due','cancelled') DEFAULT 'trial',
  `billing_cycle` enum('monthly','yearly') DEFAULT 'monthly',
  `started_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended','trial') DEFAULT 'active',
  `plan` varchar(100) DEFAULT NULL,
  `feature_flags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`feature_flags`)),
  `sso_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sso_config`)),
  `logo_url` varchar(500) DEFAULT NULL,
  `primary_color` varchar(20) DEFAULT NULL,
  `secondary_color` varchar(20) DEFAULT NULL,
  `billing_plan_id` int(10) UNSIGNED DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `name`, `domain`, `status`, `plan`, `feature_flags`, `sso_config`, `logo_url`, `primary_color`, `secondary_color`, `billing_plan_id`, `created_on`, `created_at`, `updated_at`) VALUES
(1, 'Default Tenant', 'localhost', 'active', 'Starter', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08 12:41:37', '2025-12-08 07:11:37', '2025-12-08 14:54:18'),
(3, 'bbbbaa', 'ab.assesshub.com', 'active', 'Professional', '[{\"id\":\"proctoring\",\"name\":\"AI Proctoring\",\"enabled\":true},{\"id\":\"psychometrics\",\"name\":\"Psychometric Assessments\",\"enabled\":true},{\"id\":\"integrations\",\"name\":\"Third-party Integrations\",\"enabled\":false},{\"id\":\"customBranding\",\"name\":\"Custom Branding\",\"enabled\":true},{\"id\":\"advancedReports\",\"name\":\"Advanced Analytics\",\"enabled\":false},{\"id\":\"apiAccess\",\"name\":\"API Access\",\"enabled\":false}]', '{\"provider\":\"saml\",\"issuer_url\":null,\"client_id\":null,\"client_secret\":null,\"redirect_url\":\"https:\\/\\/ab.assesshub.com\\/auth\\/callback\"}', NULL, '#044b5d', '#06b6d4', NULL, '2025-12-08 14:57:58', '2025-12-08 09:27:58', '2025-12-08 10:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_settings`
--

CREATE TABLE `tenant_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `password_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`password_policy`)),
  `smtp_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`smtp_config`)),
  `storage_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`storage_config`)),
  `data_retention` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_retention`)),
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `job_role_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('job','psychometric') NOT NULL DEFAULT 'job',
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `total_duration` int(10) UNSIGNED DEFAULT NULL,
  `total_weight` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `last_modified` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `tenant_id`, `job_role_id`, `name`, `description`, `type`, `status`, `total_duration`, `total_weight`, `created_by`, `updated_by`, `last_modified`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'aaaa', 'ssss', 'job', 'published', NULL, NULL, 1, 1, '2025-12-09 13:28:57', '2025-12-09 11:19:12', '2025-12-09 13:28:57'),
(2, 1, NULL, 'dddd', NULL, 'job', 'published', NULL, NULL, 1, 1, '2025-12-10 04:58:55', '2025-12-09 12:38:40', '2025-12-10 04:58:55'),
(3, 1, NULL, 'ss', NULL, 'job', 'published', NULL, NULL, 1, 1, '2025-12-09 13:33:20', '2025-12-09 12:40:23', '2025-12-09 13:33:20'),
(4, 1, NULL, 'ffff', NULL, 'job', 'draft', NULL, NULL, 1, 1, '2025-12-10 05:01:05', '2025-12-10 05:01:05', '2025-12-10 05:01:05'),
(5, 1, NULL, 'ffff', NULL, 'job', 'draft', NULL, NULL, 1, 1, '2025-12-10 05:01:19', '2025-12-10 05:01:19', '2025-12-10 05:01:19'),
(6, 1, NULL, 'ffff', NULL, 'job', 'draft', NULL, NULL, 1, 1, '2025-12-10 05:04:37', '2025-12-10 05:04:37', '2025-12-10 05:04:37'),
(7, 1, NULL, 'ffff', NULL, 'job', 'draft', NULL, NULL, 1, 1, '2025-12-10 05:08:14', '2025-12-10 05:08:14', '2025-12-10 05:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `test_assignments`
--

CREATE TABLE `test_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `candidate_group_id` int(10) UNSIGNED DEFAULT NULL,
  `assigned_by` int(10) UNSIGNED DEFAULT NULL,
  `due_at` datetime DEFAULT NULL,
  `status` enum('assigned','in_progress','completed','expired','cancelled') DEFAULT 'assigned',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_attempts`
--

CREATE TABLE `test_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_assignment_id` int(10) UNSIGNED NOT NULL,
  `attempt_number` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `status` enum('not_started','in_progress','completed','abandoned') DEFAULT 'not_started',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `total_time_sec` int(10) UNSIGNED DEFAULT NULL,
  `raw_score` decimal(10,2) DEFAULT NULL,
  `scaled_score` decimal(10,2) DEFAULT NULL,
  `flags_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_attempt_items`
--

CREATE TABLE `test_attempt_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_attempt_id` int(10) UNSIGNED NOT NULL,
  `test_section_item_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `response_text` text DEFAULT NULL,
  `response_choice_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_choice_ids`)),
  `response_numeric` decimal(10,2) DEFAULT NULL,
  `requires_manual_grading` tinyint(1) NOT NULL DEFAULT 0,
  `graded_by` int(10) UNSIGNED DEFAULT NULL,
  `graded_at` datetime DEFAULT NULL,
  `grader_comment` text DEFAULT NULL,
  `raw_score` decimal(10,2) DEFAULT NULL,
  `final_score` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_sections`
--

CREATE TABLE `test_sections` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(10) UNSIGNED DEFAULT NULL,
  `weightage` int(10) UNSIGNED DEFAULT NULL,
  `shuffle_items` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`questions`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_sections`
--

INSERT INTO `test_sections` (`id`, `test_id`, `title`, `description`, `duration`, `weightage`, `shuffle_items`, `sort_order`, `questions`, `created_at`, `updated_at`) VALUES
(1, 1, 'Section 1', NULL, 30, 0, 0, 1, NULL, '2025-12-09 11:36:44', '2025-12-09 11:36:44'),
(4, 3, 'Section 1', NULL, 30, 0, 0, 1, NULL, '2025-12-09 13:18:33', '2025-12-09 13:18:33'),
(6, 2, 'Section 1', NULL, 30, 0, 1, 1, NULL, '2025-12-10 04:58:55', '2025-12-10 04:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `test_section_items`
--

CREATE TABLE `test_section_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `test_section_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(10) UNSIGNED NOT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `negative_marking` decimal(8,2) DEFAULT NULL,
  `partial_scoring_rule` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `requires_manual_grading` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `test_section_items`
--

INSERT INTO `test_section_items` (`id`, `test_section_id`, `item_id`, `weight`, `negative_marking`, `partial_scoring_rule`, `sort_order`, `requires_manual_grading`, `updated_at`, `created_at`) VALUES
(1, 4, 2, NULL, NULL, NULL, 1, 0, '2025-12-09 13:18:33', '2025-12-09 13:18:33'),
(3, 4, 4, NULL, NULL, NULL, 2, 0, '2025-12-09 13:33:20', '2025-12-09 13:33:20'),
(6, 6, 7, NULL, NULL, NULL, 1, 0, '2025-12-10 04:58:55', '2025-12-10 04:58:55'),
(7, 6, 8, NULL, NULL, NULL, 2, 0, '2025-12-10 04:58:55', '2025-12-10 04:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_tokens`
--

CREATE TABLE `two_factor_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `consumed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_at` datetime DEFAULT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active',
  `groups` longtext DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `password`, `role`, `phone`, `is_super_admin`, `last_login_at`, `status`, `groups`, `two_factor_enabled`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'admin@example.com', '$2y$12$.tqbUJ59N9Nq0Q00rqp6YeF7qZoU3SFbwboLrq2jFkI3BUjuuF/TC', 'Admin', NULL, 1, NULL, 'active', '[]', 0, '2025-12-08 07:16:11', '2025-12-08 11:07:38'),
(3, 3, 'Tenant Admin', 'ab@gmail.com', '$2y$12$XBy8V6Kf0AXZBMeTvWMHFOYhz7XkwnwyE/knLR6w3hXNEwY/elEjO', NULL, NULL, 0, NULL, 'active', NULL, 0, '2025-12-08 09:27:59', '2025-12-08 09:27:59'),
(4, 1, 'test', 'test@gmail.com', '$2y$12$hbPxbEpxm0uOBOkSlrXzwO0fidkMkh7Ey1U2zom8PAvnaQ6/adQtS', 'Manager', '22222', 0, NULL, 'pending', '[]', 0, '2025-12-08 11:26:19', '2025-12-08 13:28:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhooks`
--

CREATE TABLE `webhooks` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenant_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `endpoint_url` varchar(500) NOT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`events`)),
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `webhook_id` int(10) UNSIGNED NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `http_code` int(10) UNSIGNED DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `sent_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics_test_summary`
--
ALTER TABLE `analytics_test_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ats_test_date` (`test_id`,`date`),
  ADD KEY `idx_ats_tenant` (`tenant_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_tenant` (`tenant_id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_candidates_email_tenant` (`tenant_id`,`email`),
  ADD KEY `idx_candidates_tenant` (`tenant_id`);

--
-- Indexes for table `candidate_groups`
--
ALTER TABLE `candidate_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_candidate_groups_tenant` (`tenant_id`);

--
-- Indexes for table `candidate_group_members`
--
ALTER TABLE `candidate_group_members`
  ADD PRIMARY KEY (`group_id`,`candidate_id`),
  ADD KEY `fk_cgm_candidate` (`candidate_id`);

--
-- Indexes for table `integrations`
--
ALTER TABLE `integrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_integrations_tenant` (`tenant_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_invoice_number` (`invoice_number`),
  ADD KEY `idx_invoices_subscription` (`subscription_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_items_tenant` (`tenant_id`),
  ADD KEY `fk_items_created_by` (`created_by`),
  ADD KEY `fk_items_updated_by` (`updated_by`);

--
-- Indexes for table `item_choices`
--
ALTER TABLE `item_choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item_choices_item` (`item_id`);

--
-- Indexes for table `item_media`
--
ALTER TABLE `item_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item_media_item` (`item_id`);

--
-- Indexes for table `item_tags`
--
ALTER TABLE `item_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_item_tags_name_tenant` (`tenant_id`,`name`),
  ADD KEY `idx_item_tags_tenant` (`tenant_id`);

--
-- Indexes for table `item_tag_pivot`
--
ALTER TABLE `item_tag_pivot`
  ADD PRIMARY KEY (`item_id`,`tag_id`),
  ADD KEY `fk_itp_tag` (`tag_id`);

--
-- Indexes for table `job_roles`
--
ALTER TABLE `job_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_job_roles_tenant` (`tenant_id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nl_tenant` (`tenant_id`),
  ADD KEY `idx_nl_template` (`template_id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_nt_name_tenant_channel` (`tenant_id`,`name`,`channel`),
  ADD KEY `idx_nt_tenant` (`tenant_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_email` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_permissions_name` (`name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proctoring_flags`
--
ALTER TABLE `proctoring_flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pf_session` (`proctoring_session_id`),
  ADD KEY `fk_pf_created_by` (`created_by`);

--
-- Indexes for table `proctoring_messages`
--
ALTER TABLE `proctoring_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pm_session` (`proctoring_session_id`);

--
-- Indexes for table `proctoring_sessions`
--
ALTER TABLE `proctoring_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ps_tenant` (`tenant_id`),
  ADD KEY `idx_ps_attempt` (`test_attempt_id`),
  ADD KEY `fk_ps_candidate` (`candidate_id`);

--
-- Indexes for table `psychometric_items`
--
ALTER TABLE `psychometric_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ps_item_scale` (`scale_id`,`item_id`),
  ADD KEY `idx_ps_item_item` (`item_id`);

--
-- Indexes for table `psychometric_models`
--
ALTER TABLE `psychometric_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pm_tenant` (`tenant_id`),
  ADD KEY `fk_pm_created_by` (`created_by`),
  ADD KEY `fk_pm_updated_by` (`updated_by`);

--
-- Indexes for table `psychometric_norm_groups`
--
ALTER TABLE `psychometric_norm_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_png_model` (`model_id`);

--
-- Indexes for table `psychometric_scales`
--
ALTER TABLE `psychometric_scales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ps_model` (`model_id`),
  ADD KEY `fk_ps_parent` (`parent_scale_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_name_tenant` (`tenant_id`,`name`),
  ADD KEY `idx_roles_tenant` (`tenant_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_perm` (`permission_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subs_tenant` (`tenant_id`),
  ADD KEY `fk_subs_plan` (`plan_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `fk_tenants_billing_plan` (`billing_plan_id`);

--
-- Indexes for table `tenant_settings`
--
ALTER TABLE `tenant_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tenant_settings` (`tenant_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tests_tenant` (`tenant_id`),
  ADD KEY `idx_tests_job_role` (`job_role_id`),
  ADD KEY `fk_tests_created_by` (`created_by`),
  ADD KEY `fk_tests_updated_by` (`updated_by`);

--
-- Indexes for table `test_assignments`
--
ALTER TABLE `test_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ta_tenant` (`tenant_id`),
  ADD KEY `idx_ta_test` (`test_id`),
  ADD KEY `idx_ta_candidate` (`candidate_id`),
  ADD KEY `fk_ta_group` (`candidate_group_id`),
  ADD KEY `fk_ta_assigned_by` (`assigned_by`);

--
-- Indexes for table `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_attempt_per_assignment` (`test_assignment_id`,`attempt_number`),
  ADD KEY `idx_ta_assignment` (`test_assignment_id`);

--
-- Indexes for table `test_attempt_items`
--
ALTER TABLE `test_attempt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tai_attempt` (`test_attempt_id`),
  ADD KEY `idx_tai_item` (`item_id`),
  ADD KEY `fk_tai_tsi` (`test_section_item_id`),
  ADD KEY `fk_tai_section` (`section_id`),
  ADD KEY `fk_tai_graded_by` (`graded_by`);

--
-- Indexes for table `test_sections`
--
ALTER TABLE `test_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_test_sections_test` (`test_id`);

--
-- Indexes for table `test_section_items`
--
ALTER TABLE `test_section_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tsi_section` (`test_section_id`),
  ADD KEY `idx_tsi_item` (`item_id`);

--
-- Indexes for table `two_factor_tokens`
--
ALTER TABLE `two_factor_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_twofa_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_tenant` (`tenant_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_ur_role` (`role_id`);

--
-- Indexes for table `webhooks`
--
ALTER TABLE `webhooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_webhooks_tenant` (`tenant_id`);

--
-- Indexes for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wl_webhook` (`webhook_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics_test_summary`
--
ALTER TABLE `analytics_test_summary`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidate_groups`
--
ALTER TABLE `candidate_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `integrations`
--
ALTER TABLE `integrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `item_choices`
--
ALTER TABLE `item_choices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `item_media`
--
ALTER TABLE `item_media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_tags`
--
ALTER TABLE `item_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_roles`
--
ALTER TABLE `job_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proctoring_flags`
--
ALTER TABLE `proctoring_flags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proctoring_messages`
--
ALTER TABLE `proctoring_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proctoring_sessions`
--
ALTER TABLE `proctoring_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psychometric_items`
--
ALTER TABLE `psychometric_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psychometric_models`
--
ALTER TABLE `psychometric_models`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psychometric_norm_groups`
--
ALTER TABLE `psychometric_norm_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `psychometric_scales`
--
ALTER TABLE `psychometric_scales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tenant_settings`
--
ALTER TABLE `tenant_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `test_assignments`
--
ALTER TABLE `test_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_attempts`
--
ALTER TABLE `test_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_attempt_items`
--
ALTER TABLE `test_attempt_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_sections`
--
ALTER TABLE `test_sections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `test_section_items`
--
ALTER TABLE `test_section_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `two_factor_tokens`
--
ALTER TABLE `two_factor_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `webhooks`
--
ALTER TABLE `webhooks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analytics_test_summary`
--
ALTER TABLE `analytics_test_summary`
  ADD CONSTRAINT `fk_ats_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ats_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `fk_candidates_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `candidate_groups`
--
ALTER TABLE `candidate_groups`
  ADD CONSTRAINT `fk_candidate_groups_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `candidate_group_members`
--
ALTER TABLE `candidate_group_members`
  ADD CONSTRAINT `fk_cgm_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cgm_group` FOREIGN KEY (`group_id`) REFERENCES `candidate_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `integrations`
--
ALTER TABLE `integrations`
  ADD CONSTRAINT `fk_integrations_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_invoices_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_items_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `item_choices`
--
ALTER TABLE `item_choices`
  ADD CONSTRAINT `fk_item_choices_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_media`
--
ALTER TABLE `item_media`
  ADD CONSTRAINT `fk_item_media_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_tags`
--
ALTER TABLE `item_tags`
  ADD CONSTRAINT `fk_item_tags_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_tag_pivot`
--
ALTER TABLE `item_tag_pivot`
  ADD CONSTRAINT `fk_itp_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_itp_tag` FOREIGN KEY (`tag_id`) REFERENCES `item_tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_roles`
--
ALTER TABLE `job_roles`
  ADD CONSTRAINT `fk_job_roles_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD CONSTRAINT `fk_nl_template` FOREIGN KEY (`template_id`) REFERENCES `notification_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_nl_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD CONSTRAINT `fk_nt_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proctoring_flags`
--
ALTER TABLE `proctoring_flags`
  ADD CONSTRAINT `fk_pf_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pf_session` FOREIGN KEY (`proctoring_session_id`) REFERENCES `proctoring_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proctoring_messages`
--
ALTER TABLE `proctoring_messages`
  ADD CONSTRAINT `fk_pm_session` FOREIGN KEY (`proctoring_session_id`) REFERENCES `proctoring_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proctoring_sessions`
--
ALTER TABLE `proctoring_sessions`
  ADD CONSTRAINT `fk_ps_attempt` FOREIGN KEY (`test_attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `psychometric_items`
--
ALTER TABLE `psychometric_items`
  ADD CONSTRAINT `fk_psi_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_psi_scale` FOREIGN KEY (`scale_id`) REFERENCES `psychometric_scales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `psychometric_models`
--
ALTER TABLE `psychometric_models`
  ADD CONSTRAINT `fk_pm_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pm_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pm_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `psychometric_norm_groups`
--
ALTER TABLE `psychometric_norm_groups`
  ADD CONSTRAINT `fk_png_model` FOREIGN KEY (`model_id`) REFERENCES `psychometric_models` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `psychometric_scales`
--
ALTER TABLE `psychometric_scales`
  ADD CONSTRAINT `fk_ps_model` FOREIGN KEY (`model_id`) REFERENCES `psychometric_models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_parent` FOREIGN KEY (`parent_scale_id`) REFERENCES `psychometric_scales` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `fk_roles_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_subs_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_subs_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `fk_tenants_billing_plan` FOREIGN KEY (`billing_plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tenant_settings`
--
ALTER TABLE `tenant_settings`
  ADD CONSTRAINT `fk_tenant_settings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `fk_tests_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tests_job_role` FOREIGN KEY (`job_role_id`) REFERENCES `job_roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tests_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tests_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `test_assignments`
--
ALTER TABLE `test_assignments`
  ADD CONSTRAINT `fk_ta_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ta_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ta_group` FOREIGN KEY (`candidate_group_id`) REFERENCES `candidate_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ta_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ta_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD CONSTRAINT `fk_test_attempts_assignment` FOREIGN KEY (`test_assignment_id`) REFERENCES `test_assignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_attempt_items`
--
ALTER TABLE `test_attempt_items`
  ADD CONSTRAINT `fk_tai_attempt` FOREIGN KEY (`test_attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tai_graded_by` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tai_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tai_section` FOREIGN KEY (`section_id`) REFERENCES `test_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tai_tsi` FOREIGN KEY (`test_section_item_id`) REFERENCES `test_section_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_sections`
--
ALTER TABLE `test_sections`
  ADD CONSTRAINT `fk_test_sections_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_section_items`
--
ALTER TABLE `test_section_items`
  ADD CONSTRAINT `fk_tsi_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tsi_section` FOREIGN KEY (`test_section_id`) REFERENCES `test_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `two_factor_tokens`
--
ALTER TABLE `two_factor_tokens`
  ADD CONSTRAINT `fk_twofa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webhooks`
--
ALTER TABLE `webhooks`
  ADD CONSTRAINT `fk_webhooks_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD CONSTRAINT `fk_wl_webhook` FOREIGN KEY (`webhook_id`) REFERENCES `webhooks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
