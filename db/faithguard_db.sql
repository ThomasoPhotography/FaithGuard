-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: com-linweb938.srv.combell-ops.net:3306
-- Generation Time: Dec 27, 2025 at 10:44 PM
-- Server version: 8.0.36-28
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ID483117_faithguard`
--

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `content_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_addiction_related` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content_title` varchar(255) NOT NULL,
  `content_text` longtext NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `title`, `slug`, `content_title`, `content_text`, `created_at`, `updated_at`) VALUES
(1, 'Terms of Service', 'terms', 'Terms of Service', '1. Introduction\r\n\r\nWelcome to FaithGuard (the \"Service\"), operated by WWTW (\"we,\" \"us,\" or \"our\"). By accessing our website (https://faithguard.site), you agree to be bound by these Terms of Service. If you disagree with any part of these terms, you may not access the Service.\r\n\r\n1.1 Nature of Service (Medical Disclaimer)\r\n\r\nFaithGuard provides faith-based resources and a community environment for individuals struggling with digitally influenced addictions.\r\n\r\nNot Medical Advice: The content provided is for informational and spiritual support purposes only. It is not a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of a physician or qualified health provider regarding medical conditions.\r\n\r\nNo Professional Relationship: Use of this website does not establish a doctor-patient or counsellor-client relationship.\r\n\r\n1.2 User Accounts\r\n\r\n- You must provide accurate and complete information when creating an account.\r\n- You are responsible for safeguarding your password.\r\n- You agree not to disclose your password to any third party.\r\n\r\n1.3 User Conduct\r\n\r\nYou agree not to use the Service:\r\n- In any way that violates national or international law.\r\n- To transmit any advertising or promotional material (spam).\r\n- To impersonate FaithGuard employees or other users.\r\n- To post content that is abusive, defamatory, or violates the faith-focused nature of the community.\r\n\r\n1.4 Intellectual Property\r\n\r\nThe Service and its original content, features, and functionality are the exclusive property of FaithGuard and its licensors.\r\n\r\n1.5 Governing Law\r\n\r\nThese Terms shall be governed by the laws of Belgium. Any disputes arising from these Terms will be subject to the exclusive jurisdiction of the courts of Kortrijk.', '2025-12-15 17:55:00', '2025-12-22 23:17:05');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_replies`
--

CREATE TABLE `post_replies` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_logs`
--

CREATE TABLE `progress_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `checkin_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `milestone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_category_scores`
--

CREATE TABLE `quiz_category_scores` (
  `id` int NOT NULL,
  `quiz_result_id` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `raw_score` decimal(10,2) NOT NULL,
  `weighted_score` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int NOT NULL,
  `question` text NOT NULL,
  `options` json NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `question`, `options`, `category`, `created_at`) VALUES
(1, 'Which area are you seeking freedom in today?', '{\"name\": \"addiction_type\", \"type\": \"checkbox\", \"options\": [{\"label\": \"Pornography\", \"value\": \"pornography\", \"weight\": 1.5}, {\"label\": \"Sexual Compulsion (non-porn)\", \"value\": \"sexual_compulsion\", \"weight\": 1.4}, {\"label\": \"Alcohol\", \"value\": \"alcohol\", \"weight\": 1.3}, {\"label\": \"Illegal Drugs\", \"value\": \"drugs_illegal\", \"weight\": 1.5}, {\"label\": \"Prescription Drug Misuse\", \"value\": \"drugs_prescription\", \"weight\": 1.4}, {\"label\": \"Smoking / Vaping\", \"value\": \"smoking\", \"weight\": 1.2}, {\"label\": \"Gambling\", \"value\": \"gambling\", \"weight\": 1.4}, {\"label\": \"Gaming Addiction\", \"value\": \"gaming\", \"weight\": 1.2}, {\"label\": \"Social Media / Digital Consumption\", \"value\": \"digital\", \"weight\": 1.1}, {\"label\": \"Food Addiction / Disordered Eating\", \"value\": \"food\", \"weight\": 1.2}, {\"label\": \"Shopping / Spending\", \"value\": \"shopping\", \"weight\": 1.1}, {\"label\": \"Workaholism\", \"value\": \"work\", \"weight\": 1.1}, {\"label\": \"Emotional Dependency / Codependency\", \"value\": \"emotional_dependency\", \"weight\": 1.3}, {\"label\": \"Anger / Rage\", \"value\": \"anger\", \"weight\": 1.3}, {\"label\": \"Self-Harm Behaviors\", \"value\": \"self_harm\", \"weight\": 1.6}, {\"label\": \"Escapism / Avoidance Behaviors\", \"value\": \"escapism\", \"weight\": 1.2}], \"scoring_type\": \"multiplier_base\"}', 'addiction', '2025-12-22'),
(2, 'How often do you currently engage in spiritual reflection or prayer?', '{\"name\": \"spiritual_frequency\", \"type\": \"radio\", \"options\": [{\"label\": \"Daily\", \"score\": -5}, {\"label\": \"Weekly\", \"score\": -2}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Never at the moment\", \"score\": 5}], \"scoring_type\": \"score_modifier\"}', 'spiritual', '2025-12-24'),
(3, 'I feel close to God in my daily life.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 5}, {\"label\": \"Rarely\", \"score\": 4}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 2}, {\"label\": \"Very Often\", \"score\": 1}], \"category\": \"spiritual\"}', 'spiritual', '2025-12-24'),
(4, 'I turn to prayer when I feel tempted or overwhelmed.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 5}, {\"label\": \"Rarely\", \"score\": 4}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 2}, {\"label\": \"Very Often\", \"score\": 1}], \"category\": \"spiritual\"}', 'spiritual', '2025-12-24'),
(5, 'My struggle has affected how I read Scripture or worship.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"spiritual\"}', 'spiritual', '2025-12-24'),
(6, 'I believe God’s grace is available to me, even in my weakness.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 5}, {\"label\": \"Rarely\", \"score\": 4}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 2}, {\"label\": \"Very Often\", \"score\": 1}], \"category\": \"hope\"}', 'hope', '2025-12-24'),
(7, 'I sometimes feel distant from God because of my habits.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"guilt\"}', 'guilt', '2025-12-24'),
(8, 'I feel torn between what I believe and what I do.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"guilt\"}', 'guilt', '2025-12-24'),
(9, 'I have tried to stop or reduce this behavior and struggled to do so.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"compulsion\"}', 'compulsion', '2025-12-24'),
(10, 'I feel a sense of relief or escape when I give in, followed by regret.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"compulsion\"}', 'compulsion', '2025-12-24'),
(11, 'I feel ashamed when I think about this struggle.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"guilt\"}', 'guilt', '2025-12-24'),
(12, 'I feel in control of this behavior.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 5}, {\"label\": \"Disagree\", \"score\": 4}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 2}, {\"label\": \"Strongly Agree\", \"score\": 1}], \"category\": \"control\"}', 'control', '2025-12-24'),
(13, 'I hide this struggle from people close to me.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"secrecy\"}', 'secrecy', '2025-12-24'),
(14, 'I worry about how others would see me if they knew.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"secrecy\"}', 'secrecy', '2025-12-24'),
(15, 'This struggle has caused me to withdraw from others.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"isolation\"}', 'isolation', '2025-12-24'),
(16, 'I feel alone in this battle.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"isolation\"}', 'isolation', '2025-12-24'),
(17, 'I avoid certain conversations or situations because of this habit.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"secrecy\"}', 'secrecy', '2025-12-24'),
(18, 'This behavior has negatively affected my relationships.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"relationships\"}', 'relationships', '2025-12-24'),
(19, 'I have noticed negative effects on my mental or physical health.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"health\"}', 'health', '2025-12-24'),
(20, 'My behavior has led me to take risks I normally wouldn’t.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 1}, {\"label\": \"Disagree\", \"score\": 2}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 4}, {\"label\": \"Strongly Agree\", \"score\": 5}], \"category\": \"risk\"}', 'risk', '2025-12-24'),
(21, 'I sometimes minimize or justify this behavior to myself.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"control\"}', 'control', '2025-12-24'),
(22, 'I fear where this struggle could lead if it continues.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 1}, {\"label\": \"Rarely\", \"score\": 2}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 4}, {\"label\": \"Very Often\", \"score\": 5}], \"category\": \"risk\"}', 'risk', '2025-12-24'),
(23, 'I believe change is possible for me.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 5}, {\"label\": \"Disagree\", \"score\": 4}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 2}, {\"label\": \"Strongly Agree\", \"score\": 1}], \"category\": \"hope\"}', 'hope', '2025-12-24'),
(24, 'I would be open to talking to someone I trust about this.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 5}, {\"label\": \"Disagree\", \"score\": 4}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 2}, {\"label\": \"Strongly Agree\", \"score\": 1}], \"category\": \"accountability\"}', 'accountability', '2025-12-24'),
(25, 'I feel supported by my faith community.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Strongly Disagree\", \"score\": 5}, {\"label\": \"Disagree\", \"score\": 4}, {\"label\": \"Neutral\", \"score\": 3}, {\"label\": \"Agree\", \"score\": 2}, {\"label\": \"Strongly Agree\", \"score\": 1}], \"category\": \"relationships\"}', 'relationships', '2025-12-24'),
(26, 'I believe healing involves both faith and practical steps.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 5}, {\"label\": \"Rarely\", \"score\": 4}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 2}, {\"label\": \"Very Often\", \"score\": 1}], \"category\": \"spiritual\"}', 'spiritual', '2025-12-24'),
(27, 'I desire freedom and restoration more than temporary relief.', '{\"type\": \"radio\", \"options\": [{\"label\": \"Never\", \"score\": 5}, {\"label\": \"Rarely\", \"score\": 4}, {\"label\": \"Sometimes\", \"score\": 3}, {\"label\": \"Often\", \"score\": 2}, {\"label\": \"Very Often\", \"score\": 1}], \"category\": \"hope\"}', 'hope', '2025-12-24');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_resource_map`
--

CREATE TABLE `quiz_resource_map` (
  `id` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `addiction_type` varchar(100) DEFAULT NULL,
  `resource_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `addiction_type` json NOT NULL,
  `answers_json` json NOT NULL,
  `total_score` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_scripture_map`
--

CREATE TABLE `quiz_scripture_map` (
  `id` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `min_score` decimal(10,2) DEFAULT NULL,
  `max_score` decimal(10,2) DEFAULT NULL,
  `verse_key` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content_text` text NOT NULL,
  `content_visual` text,
  `tags` json DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_tags`
--

CREATE TABLE `resource_tags` (
  `id` int NOT NULL,
  `resource_id` int NOT NULL,
  `tag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `scripture_cache`
--

CREATE TABLE `scripture_cache` (
  `id` int NOT NULL,
  `verse_key` varchar(100) NOT NULL,
  `translation` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `fetched_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `role`, `created_at`) VALUES
(1, 'admin@faithguard.com', '$2y$12$Si3tc0jbzi7SZ85svjKhLeMkVf1aoQcpMaGis/s.obNQoKdAm7YqW', 'Admin', 'admin', '2025-12-22 23:03:16'),
(2, 'thomas.deseure@proton.me', '$2y$12$M5By2UvRbwuTvrFfYh42UOzM1tv1KnQGbedtlbWnCqu6wnNGrIATO', 'Thomas', 'user', '2025-12-24 00:04:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_journal_user` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message_sender` (`sender_id`),
  ADD KEY `idx_message_receiver` (`receiver_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_posts_user` (`user_id`);

--
-- Indexes for table `post_replies`
--
ALTER TABLE `post_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reply_post` (`post_id`),
  ADD KEY `idx_reply_user` (`user_id`);

--
-- Indexes for table `progress_logs`
--
ALTER TABLE `progress_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_progress_user` (`user_id`);

--
-- Indexes for table `quiz_category_scores`
--
ALTER TABLE `quiz_category_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_score_result` (`quiz_result_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_resource_map`
--
ALTER TABLE `quiz_resource_map`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_map_category` (`category`),
  ADD KEY `idx_map_resource` (`resource_id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quiz_user` (`user_id`);

--
-- Indexes for table `quiz_scripture_map`
--
ALTER TABLE `quiz_scripture_map`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_scripture_category` (`category`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `resource_tags`
--
ALTER TABLE `resource_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_resource` (`resource_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `scripture_cache`
--
ALTER TABLE `scripture_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `verse_translation` (`verse_key`,`translation`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_session_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_replies`
--
ALTER TABLE `post_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progress_logs`
--
ALTER TABLE `progress_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_category_scores`
--
ALTER TABLE `quiz_category_scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `quiz_resource_map`
--
ALTER TABLE `quiz_resource_map`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_scripture_map`
--
ALTER TABLE `quiz_scripture_map`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_tags`
--
ALTER TABLE `resource_tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `scripture_cache`
--
ALTER TABLE `scripture_cache`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `fk_journal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_replies`
--
ALTER TABLE `post_replies`
  ADD CONSTRAINT `fk_reply_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reply_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress_logs`
--
ALTER TABLE `progress_logs`
  ADD CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_category_scores`
--
ALTER TABLE `quiz_category_scores`
  ADD CONSTRAINT `fk_category_result` FOREIGN KEY (`quiz_result_id`) REFERENCES `quiz_results` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_resource_map`
--
ALTER TABLE `quiz_resource_map`
  ADD CONSTRAINT `fk_qrm_resource` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `fk_quiz_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resource_tags`
--
ALTER TABLE `resource_tags`
  ADD CONSTRAINT `fk_resource_tags` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role`) REFERENCES `roles` (`name`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
