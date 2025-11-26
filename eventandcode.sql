/*
 Navicat Premium Dump SQL

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 90001 (9.0.1)
 Source Host           : localhost:3306
 Source Schema         : eventandcode

 Target Server Type    : MySQL
 Target Server Version : 90001 (9.0.1)
 File Encoding         : 65001

 Date: 26/11/2025 13:37:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for events
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `event_date` datetime NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_user_id_foreign` (`user_id`),
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of events
-- ----------------------------
BEGIN;
INSERT INTO `events` (`id`, `name`, `description`, `event_date`, `location`, `client_name`, `image`, `user_id`, `created_at`, `updated_at`) VALUES (1, 'Owo\'s birthday', 'Owo\'s 50th birthday', '2025-11-07 12:44:00', 'Victoria Island', 'Mr Owolabi', 'events/tMqnw5ejWxP9sIhYOKngUTWewDmmuOdKh1oL7vaP.jpg', 3, '2025-10-30 11:46:35', '2025-10-30 13:30:10');
INSERT INTO `events` (`id`, `name`, `description`, `event_date`, `location`, `client_name`, `image`, `user_id`, `created_at`, `updated_at`) VALUES (2, 'Bukky and Ayoola\'s wedding', 'Bukky Ayoola\'s 2025', '2025-11-05 17:36:00', 'Victoria Island', 'Mr Imisioluwa', 'events/AkvhGa2GVxCu85pCl9V1Wo2ZL3kzS4a54JzjzRBb.jpg', 3, '2025-10-30 16:37:07', '2025-10-30 16:37:07');
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for guests
-- ----------------------------
DROP TABLE IF EXISTS `guests`;
CREATE TABLE `guests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `checked_in` tinyint(1) NOT NULL DEFAULT '0',
  `checked_in_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guests_qr_code_unique` (`qr_code`),
  KEY `guests_event_id_foreign` (`event_id`),
  CONSTRAINT `guests_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of guests
-- ----------------------------
BEGIN;
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (7, 'jeremiah saliu', 'bisola.oke@yabatech.edu.ng', '08057564312', 'a6115548-d679-46fb-ac55-b553b5fe2d4d', 1, 1, '2025-10-30 14:40:37', '2025-10-30 11:54:12', '2025-10-30 14:40:37');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (8, 'Derinsola williams', 'derin@yahoo.com', '08067542134', '580b9485-ac68-45c2-961a-f6ce07ce3a41', 1, 1, '2025-10-30 14:46:30', '2025-10-30 11:54:14', '2025-10-30 14:46:30');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (9, 'monisola', 'monisola@yahoo.com', '09067541234', '17890775-88e4-4738-b444-4be10931b321', 1, 1, '2025-10-30 14:46:30', '2025-10-30 11:56:38', '2025-10-30 14:46:30');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (15, 'Bukunmi Odefunsho', 'bukunmi@yahoo.com', '09078541232', '588d8078-60ca-471d-8585-2428a0dbe643', 1, 1, '2025-10-30 14:46:30', '2025-10-30 12:05:44', '2025-10-30 14:46:30');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (19, 'Sandra', 'Sandra@yahoo.com', '08127093748', 'aef2d65c-ec7c-45d9-9ac7-cf02a021dfe8', 1, 0, NULL, '2025-10-30 12:06:37', '2025-10-30 12:06:37');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (20, 'bisola', 'bisola_oak@yahoo.com', '08057516152', '1751beb3-b156-4114-8709-2473fd1a1b86', 1, 1, '2025-10-30 14:33:18', '2025-10-30 12:06:39', '2025-10-30 14:33:18');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (21, 'John Doe', 'john.doe@example.com', '08012345678', 'a25d46a0-5ef7-4be0-a1ec-4a92bdaf4458', 1, 1, '2025-10-30 16:30:20', '2025-10-30 12:43:21', '2025-10-30 16:30:20');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (22, 'Jane Smith', 'bisolaoe@gmail.com', '08123456789', '717d7a93-1c03-4906-8f66-0d384565b8be', 1, 1, '2025-10-30 16:27:40', '2025-10-30 12:43:25', '2025-10-30 16:27:40');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (23, 'Bob Johnson', 'bob.johnson@example.com', '09087654321', '3ce9f277-a3b3-4db5-893b-aec999cd2cb3', 1, 0, NULL, '2025-10-30 12:43:27', '2025-10-30 12:43:27');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (24, 'Doreen stewart', 'bisola_oak@yahoo.com', '08057516152', '968c5815-aec6-46cc-b16d-ebd4c45e3d03', 2, 0, NULL, '2025-10-30 16:41:34', '2025-11-11 15:48:00');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (25, 'Shola Tolu', 'bisola.oke@yabatech.edu.ng', '08123456789', '5b2899a7-641f-45ed-8540-b3aa34a5c611', 2, 0, NULL, '2025-10-30 16:41:41', '2025-10-30 16:41:41');
INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `qr_code`, `event_id`, `checked_in`, `checked_in_at`, `created_at`, `updated_at`) VALUES (26, 'Joyce Load', 'temi@yahoo.com', '09087654321', 'f7878496-47ce-421a-928b-48a75dc53cdb', 2, 0, NULL, '2025-10-30 16:41:45', '2025-10-30 16:41:45');
COMMIT;

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of job_batches
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4, '2025_10_04_112503_create_events_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5, '2025_10_04_112544_create_guests_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6, '2025_10_04_114519_add_role_to_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7, '2025_10_04_123416_add_client_name_and_image_to_events_table', 1);
COMMIT;

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
BEGIN;
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES ('bisola_oak@yahoo.com', '$2y$12$9pi0wCfQrpcwzeEFhbS6FeBQCBrDjYwa58tZgPj6cn5QtDWCI/ls6', '2025-11-04 14:06:07');
COMMIT;

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('Hv5fYdGrYOasEnJ4xW03BYsvuY8UYunaOBI4MG5z', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWFjS2lsOWR0UDFaSXMyRkZNa0l6b0xtSkt3N2dhNGphUWtlT1RpYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762873912);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('PlcdcbVAfXVHgDbIDNC8HdRXv0qerjaogBWWnHsa', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTg1VTZ3NFZmeWxUT3EyYmhDSXFTdEZST0trcWlWMHo4VUpEc1dmeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762874706);
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('tp9gYo6CqGwN6tmr1aqKZp2zLB2d9OpKlGC8aFGb', 3, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNVBvelhDVUNCWW9ScWUzalgzajJBNUFSUnZVN1NCVVExZ0FBQkZHeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMS9ndWVzdHM/ZXZlbnQ9MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1762877839);
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES (1, 'Tolani', 'tolani@yahoo.com', '2025-10-30 11:39:39', '$2y$12$5MyGbyiEu2svnCyxipKJ5OD4jMFZ55Bl5d4MSJYkCbvAcraChUAH.', 'GeiwsQw1pjBpUg3hyZO3VCeNWhxfk9xZY6VpyiKc0Z1LodW4W5NdOmI7jIZO', '2025-10-30 11:39:39', '2025-11-03 14:54:03', 'admin');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES (3, 'bisola', 'bisola_oak@yahoo.com', NULL, '$2y$12$/wsmbIuGXgECm/AZ/P9pzeOjekeU9/k8cj0xXnrDiBVYow/8CgZom', NULL, '2025-10-30 11:42:42', '2025-11-03 14:53:13', 'superadmin');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
