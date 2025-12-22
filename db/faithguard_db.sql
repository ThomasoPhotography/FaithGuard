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

-- Create roles first so users can reference it
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=InnoDB;

INSERT INTO `roles` (`id`, `name`) VALUES (1, 'admin'), (2, 'user');

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255),
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_user_role` (`role`)
) ENGINE=InnoDB;

CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`token`),
  KEY `idx_session_user` (`user_id`)
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

CREATE TABLE `quiz_category_scores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quiz_result_id` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `raw_score` decimal(10,2) NOT NULL,
  `weighted_score` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_score_result` (`quiz_result_id`)
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

CREATE TABLE `quiz_resource_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `addiction_type` varchar(100),
  `resource_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_map_category` (`category`),
  KEY `idx_map_resource` (`resource_id`)
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
  PRIMARY KEY (`id`),
  KEY `idx_progress_user` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_posts_user` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `post_replies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reply_post` (`post_id`),
  KEY `idx_reply_user` (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_message_sender` (`sender_id`),
  KEY `idx_message_receiver` (`receiver_id`)
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

-- User Role Integrity
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role`
  FOREIGN KEY (`role`) REFERENCES `roles` (`name`) ON UPDATE CASCADE;

-- Session Connections
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Quiz System Connections
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `fk_quiz_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `quiz_category_scores`
  ADD CONSTRAINT `fk_category_result`
  FOREIGN KEY (`quiz_result_id`) REFERENCES `quiz_results` (`id`) ON DELETE CASCADE;

-- Resource Connections
ALTER TABLE `resource_tags`
  ADD CONSTRAINT `fk_resource_tags`
  FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

ALTER TABLE `quiz_resource_map`
  ADD CONSTRAINT `fk_qrm_resource`
  FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

-- Community & Progress Connections
ALTER TABLE `progress_logs`
  ADD CONSTRAINT `fk_progress_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Post Replies linked to both the Parent Post and the User
ALTER TABLE `post_replies`
  ADD CONSTRAINT `fk_reply_post`
  FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reply_user`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Messaging Connections: Link both sender and receiver to user ID
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_sender`
  FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_receiver`
  FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;