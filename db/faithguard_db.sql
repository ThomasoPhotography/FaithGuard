-- phpMyAdmin SQL Dump
-- version 5.2.2
-- Server version: 8.0.36
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Database: `ID483117_faithguard`
-- --------------------------------------------------------

-- ========================================================
-- CORE TABLES
-- ========================================================

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255),
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB;

CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=InnoDB;

CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`token`)
) ENGINE=InnoDB;

-- ========================================================
-- QUIZ STRUCTURE
-- ========================================================

CREATE TABLE `quiz_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `options` json NOT NULL,
  `weights` json,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `quiz_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `addiction_type` json NOT NULL,
  `answers_json` json NOT NULL,
  `total_score` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_quiz_user` (`user_id`)
) ENGINE=InnoDB;

-- ========================================================
-- PER-CATEGORY SCORES (Progress bars, reports)
-- ========================================================

CREATE TABLE `quiz_category_scores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quiz_result_id` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `raw_score` decimal(10,2) NOT NULL,
  `weighted_score` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB;

-- ========================================================
-- RESOURCES
-- ========================================================

CREATE TABLE `resources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `tags` json,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `resource_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `resource_id` int NOT NULL,
  `tag` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_resource` (`resource_id`)
) ENGINE=InnoDB;

-- ========================================================
-- QUIZ → RESOURCE AUTO-MAPPING
-- ========================================================

CREATE TABLE `quiz_resource_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `addiction_type` varchar(100),
  `resource_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_map_category` (`category`)
) ENGINE=InnoDB;

-- ========================================================
-- SCRIPTURE SUPPORT
-- ========================================================

CREATE TABLE `scripture_cache` (
  `id` int NOT NULL AUTO_INCREMENT,
  `verse_key` varchar(100) NOT NULL,
  `translation` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `fetched_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `verse_translation` (`verse_key`, `translation`)
) ENGINE=InnoDB;

CREATE TABLE `quiz_scripture_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `min_score` decimal(10,2),
  `max_score` decimal(10,2),
  `verse_key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_scripture_category` (`category`)
) ENGINE=InnoDB;

-- ========================================================
-- PROGRESS & COMMUNITY
-- ========================================================

CREATE TABLE `progress_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `checkin_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `milestone` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `post_replies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ========================================================
-- POLICIES
-- ========================================================

CREATE TABLE `policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content_title` varchar(255) NOT NULL,
  `content_text` longtext NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`slug`)
) ENGINE=InnoDB;

-- ========================================================
-- FOREIGN KEYS
-- ========================================================

ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `quiz_results`
  ADD CONSTRAINT `fk_quiz_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `quiz_category_scores`
  ADD CONSTRAINT `fk_category_result`
  FOREIGN KEY (`quiz_result_id`) REFERENCES `quiz_results` (`id`);

ALTER TABLE `resource_tags`
  ADD CONSTRAINT `fk_resource_tags`
  FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`);

ALTER TABLE `quiz_resource_map`
  ADD CONSTRAINT `fk_qrm_resource`
  FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`);

COMMIT;
