-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: com-linweb938.srv.combell-ops.net:3306
-- Generation Time: Dec 11, 2025 at 08:39 PM
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
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
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
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '1.0',
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `title`, `slug`, `content`, `version`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Privacy Policy', 'privacy-policy', '<h1>Privacy Policy</h1><p>Content here.</p>', '1.0', 1, '2025-12-11 19:14:00', '2025-12-11 19:14:00'),
(2, 'Terms of Service', 'terms-of-service', '<h1>Terms of Service</h1><p>Content here.</p>', '1.0', 1, '2025-12-11 19:14:00', '2025-12-11 19:14:00'),
(3, 'Community Guidelines', 'community-guidelines', '<h1>Community Guidelines</h1><p>Content here.</p>', '1.0', 1, '2025-12-11 19:14:00', '2025-12-11 19:14:00');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_replies`
--

CREATE TABLE `post_replies` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prayers`
--

CREATE TABLE `prayers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress_logs`
--

CREATE TABLE `progress_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `checkin_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `milestone` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int NOT NULL,
  `question` text COLLATE utf8mb4_general_ci NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `weights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `question`, `options`, `weights`) VALUES
(2, 'What type of addiction are you currently struggling with?', '\r\n{\r\n  \"type\": \"radio\",\r\n  \"name\": \"addiction_type\",\r\n  \"scoring_type\": \"multiplier_base\",\r\n  \"options\": [\r\n    {\"label\": \"Substance (Drugs/Meds)\", \"value\": \"Substance\", \"weight\": 1.4},\r\n    {\"label\": \"Alcohol\", \"value\": \"Alcohol\", \"weight\": 1.3},\r\n    {\"label\": \"Sexual\", \"value\": \"Sexual\", \"weight\": 1.5},\r\n    {\"label\": \"Pornography\", \"value\": \"Pornography\", \"weight\": 1.5},\r\n    {\"label\": \"Gambling\", \"value\": \"Gambling\", \"weight\": 1.3},\r\n    {\"label\": \"Digital/Social Media\", \"value\": \"Digital/Social Media\", \"weight\": 1.1},\r\n    {\"label\": \"Food/Binge Eating\", \"value\": \"Food/Binge Eating\", \"weight\": 1.1},\r\n    {\"label\": \"Smoking\", \"value\": \"Smoking\", \"weight\": 1.2},\r\n    {\"label\": \"Other\", \"value\": \"Other\", \"weight\": 1.0}\r\n  ]\r\n}', NULL),
(3, 'How often do you pray or engage in spiritual reflection?', '\r\n{\r\n  \"type\": \"radio\",\r\n  \"name\": \"spiritual_frequency\",\r\n  \"scoring_type\": \"score_modifier\",\r\n  \"options\": [\r\n    {\"label\": \"Daily\", \"score\": -5},\r\n    {\"label\": \"Weekly\", \"score\": -3},\r\n    {\"label\": \"Rarely\", \"score\": 1},\r\n    {\"label\": \"Never\", \"score\": 3}\r\n  ]\r\n}', NULL),
(4, 'Core Addiction and Wellness Assessment', '\r\n{\r\n  \"type\": \"likert_scale\",\r\n  \"name\": \"core_assessment\",\r\n  \"scale\": [\r\n    {\"label\": \"Never\", \"score\": 1},\r\n    {\"label\": \"Rarely\", \"score\": 2},\r\n    {\"label\": \"Sometimes\", \"score\": 3},\r\n    {\"label\": \"Often\", \"score\": 4},\r\n    {\"label\": \"Very Often\", \"score\": 5}\r\n  ],\r\n  \"sections\": [\r\n    {\r\n      \"title\": \"Spiritual Health & Alignment\",\r\n      \"category\": \"Spiritual Health\",\r\n      \"questions\": [\r\n        {\"id\": 1, \"text\": \"I turn to my addiction instead of prayer when I feel stressed.\"},\r\n        {\"id\": 2, \"text\": \"I feel distant from God because of my addictive habits.\"},\r\n        {\"id\": 3, \"text\": \"My addiction makes me avoid church, fellowship, or spiritual practices.\"},\r\n        {\"id\": 4, \"text\": \"I feel convicted by the Holy Spirit when engaging in my addiction.\"},\r\n        {\"id\": 5, \"text\": \"I rely more on my addiction than on God for comfort or relief.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Emotional Control & Coping\",\r\n      \"category\": \"Emotional Control\",\r\n      \"questions\": [\r\n        {\"id\": 6, \"text\": \"I use my addiction to cope with difficult emotions.\"},\r\n        {\"id\": 7, \"text\": \"I feel anxious or irritable when I can’t engage in my addiction.\"},\r\n        {\"id\": 8, \"text\": \"I feel guilt or shame after giving in to my addiction.\"},\r\n        {\"id\": 9, \"text\": \"My addiction feels like something I cannot control.\"},\r\n        {\"id\": 10, \"text\": \"I hide my struggles because I fear judgment from others.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Behavioural Impact\",\r\n      \"category\": \"Behavioural Impact\",\r\n      \"questions\": [\r\n        {\"id\": 11, \"text\": \"I have tried to cut down but failed.\"},\r\n        {\"id\": 12, \"text\": \"I spend more time than intended engaging with my addiction.\"},\r\n        {\"id\": 13, \"text\": \"I neglect responsibilities (school, work, home) because of my addiction.\"},\r\n        {\"id\": 14, \"text\": \"I’ve taken risks or made unwise decisions because of my addiction.\"},\r\n        {\"id\": 15, \"text\": \"I experience cravings that feel overwhelming.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Relational Impact\",\r\n      \"category\": \"Relational Impact\",\r\n      \"questions\": [\r\n        {\"id\": 16, \"text\": \"My relationships have suffered because of my addiction.\"},\r\n        {\"id\": 17, \"text\": \"I become defensive or irritated when someone confronts me about my habits.\"},\r\n        {\"id\": 18, \"text\": \"I isolate myself to hide my addictive behaviour.\"},\r\n        {\"id\": 19, \"text\": \"I avoid accountability from friends, family, or church.\"},\r\n        {\"id\": 20, \"text\": \"My addiction causes conflict with people close to me.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Daily Life & Functioning\",\r\n      \"category\": \"Daily Functioning\",\r\n      \"questions\": [\r\n        {\"id\": 21, \"text\": \"I delay tasks or responsibilities because of my addiction.\"},\r\n        {\"id\": 22, \"text\": \"I prioritize my addiction over healthier activities.\"},\r\n        {\"id\": 23, \"text\": \"My sleep schedule is negatively affected by my addiction.\"},\r\n        {\"id\": 24, \"text\": \"My finances are impacted because of my addiction.\"},\r\n        {\"id\": 25, \"text\": \"My physical health has worsened due to addictive behaviours.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Faith-Based Reflection & Accountability\",\r\n      \"category\": \"Faith Reflection\",\r\n      \"questions\": [\r\n        {\"id\": 26, \"text\": \"I feel that my addiction is harming my relationship with God.\"},\r\n        {\"id\": 27, \"text\": \"I avoid reading Scripture or praying when I slip into addiction.\"},\r\n        {\"id\": 28, \"text\": \"I feel unworthy of God’s forgiveness because of my addiction.\"},\r\n        {\"id\": 29, \"text\": \"I struggle to trust God with my healing and recovery.\"},\r\n        {\"id\": 30, \"text\": \"I believe my addiction is becoming an idol in my life.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Self-Awareness & Desire for Change\",\r\n      \"category\": \"Self-Awareness\",\r\n      \"questions\": [\r\n        {\"id\": 31, \"text\": \"I want to stop, but I feel unable to do so.\"},\r\n        {\"id\": 32, \"text\": \"I recognize patterns in my life that lead me toward my addiction.\"},\r\n        {\"id\": 33, \"text\": \"I worry about long-term consequences of my addictive behaviours.\"},\r\n        {\"id\": 34, \"text\": \"I desire accountability or support to overcome my struggles.\"}\r\n      ]\r\n    }\r\n  ],\r\n  \"conditional_weights\": {\r\n      \"Pornography\": {\r\n          \"Spiritual Health\": 1.3,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.3,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Sexual\": {\r\n          \"Spiritual Health\": 1.3,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.3,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Alcohol\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.1,\r\n          \"Behavioural Impact\": 1.3,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.2,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.2\r\n      },\r\n      \"Substance\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.3,\r\n          \"Relational Impact\": 1.1,\r\n          \"Daily Functioning\": 1.4,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.2\r\n      },\r\n      \"Gambling\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.3,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.1\r\n      },\r\n      \"Smoking\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.3,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Digital/Social Media\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Food/Binge Eating\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Other\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.0,\r\n          \"Behavioural Impact\": 1.0,\r\n          \"Relational Impact\": 1.0,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      }\r\n    }\r\n}', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `addiction_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `answers_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `total_score` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `title`, `content`, `tags`, `created_at`) VALUES
(1, 'Daily Prayer Guide', 'A structured guide for daily prayer and reflection.', '[\"Prayer\", \"Daily\", \"Faith\"]', '2025-12-11 20:27:33'),
(2, 'Overcoming Temptation', 'A guide on how to resist urges using scripture and distraction techniques.', '[\"Sexual\", \"Pornography\", \"Scripture\"]', '2025-12-11 20:24:53'),
(3, 'Accountability Partner Guide', 'How to choose and maintain a healthy relationship with an accountability partner.', '[\"Accountability\", \"Relational\"]', '2025-12-11 20:24:53'),
(4, 'Digital Detox Strategy', 'Practical steps to reduce screen time and block harmful content.', '[\"Digital\", \"Social Media\", \"Habits\"]', '2025-12-11 20:24:53'),
(5, '12 Steps to Freedom', 'A faith-based adaptation of the 12-step recovery program.', '[\"Alcohol\", \"Substance\", \"Recovery\"]', '2025-12-11 20:24:53'),
(6, 'Handling Guilt and Shame', 'Understanding God’s forgiveness and moving past shame.', '[\"Mental Health\", \"Faith\", \"Devotional\"]', '2025-12-11 20:24:53'),
(7, 'Financial Stewardship', 'Recovering from gambling losses and managing money biblically.', '[\"Gambling\", \"Finance\", \"Stewardship\"]', '2025-12-11 20:24:53'),
(8, 'Healthy Coping Mechanisms', 'Replacing addictive behaviors with healthy emotional outlets.', '[\"Emotional Control\", \"Food\", \"Smoking\"]', '2025-12-11 20:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `resource_tags`
--

CREATE TABLE `resource_tags` (
  `id` int NOT NULL,
  `resource_id` int NOT NULL,
  `tag` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource_tags`
--

INSERT INTO `resource_tags` (`id`, `resource_id`, `tag`) VALUES
(30, 1, 'Prayer'),
(31, 1, 'Daily'),
(32, 1, 'Faith'),
(33, 2, 'Sexual'),
(34, 2, 'Pornography'),
(35, 2, 'Scripture'),
(36, 2, 'Self-Control'),
(37, 3, 'Accountability'),
(38, 3, 'Relational'),
(39, 3, 'Community'),
(40, 4, 'Digital'),
(41, 4, 'Social Media'),
(42, 4, 'Habits'),
(43, 4, 'Discipline'),
(44, 5, 'Alcohol'),
(45, 5, 'Substance'),
(46, 5, 'Recovery'),
(47, 5, 'Steps'),
(48, 6, 'Mental Health'),
(49, 6, 'Faith'),
(50, 6, 'Devotional'),
(51, 6, 'Healing'),
(52, 7, 'Gambling'),
(53, 7, 'Finance'),
(54, 7, 'Stewardship'),
(55, 8, 'Emotional Control'),
(56, 8, 'Food'),
(57, 8, 'Smoking'),
(58, 8, 'Wellness');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `role`, `created_at`) VALUES
(1, 'admin@faithguard.com', '$2y$10$ymjkffZvp1ckE/ttWlml2.9fw8jrKCmefQIUft6xIUZF7463qn.sa', 'Admin', 'admin', '2025-11-26 22:42:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `idx_messages_receiver` (`receiver_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `created_by` (`created_by`);

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
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `prayers`
--
ALTER TABLE `prayers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `progress_logs`
--
ALTER TABLE `progress_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resource_tags`
--
ALTER TABLE `resource_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `fk_users_role_name` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `prayers`
--
ALTER TABLE `prayers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progress_logs`
--
ALTER TABLE `progress_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analytics`
--
ALTER TABLE `analytics`
  ADD CONSTRAINT `analytics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `policies`
--
ALTER TABLE `policies`
  ADD CONSTRAINT `policies_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_replies`
--
ALTER TABLE `post_replies`
  ADD CONSTRAINT `post_replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `prayers`
--
ALTER TABLE `prayers`
  ADD CONSTRAINT `prayers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `progress_logs`
--
ALTER TABLE `progress_logs`
  ADD CONSTRAINT `progress_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `resource_tags`
--
ALTER TABLE `resource_tags`
  ADD CONSTRAINT `resource_tags_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`);

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role_name` FOREIGN KEY (`role`) REFERENCES `roles` (`name`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
