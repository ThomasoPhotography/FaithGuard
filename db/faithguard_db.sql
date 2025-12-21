-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: com-linweb938.srv.combell-ops.net:3306
-- Generation Time: Dec 21, 2025 at 10:20 PM
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
  `content_title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `content_text` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`id`, `title`, `slug`, `content_title`, `content_text`, `created_at`, `updated_at`) VALUES
(1, 'Terms of Service', 'terms', 'Terms of Service', '1. Introduction\r\n\r\nWelcome to FaithGuard. By accessing or using our website (https://faithguard.site), you agree to be bound by these Terms of Service (\"Terms\"). If you disagree with any part of the terms, then you may not access the Service.\r\n\r\n2. Nature of Service (Disclaimer)\r\n\r\nFaithGuard provides faith-based resources for individuals struggling with addiction.\r\n\r\nNot Medical Advice: The content provided on FaithGuard is for informational and spiritual support purposes only. It is not a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of your physician or other qualified health provider with any questions you may have regarding a medical condition.\r\n\r\nNo Professional Relationship: Use of this website does not establish a doctor-patient or counsellor-client relationship.\r\n\r\n3. User Accounts\r\n\r\nWhen you create an account with us, you must provide us with information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.\r\n\r\nYou are responsible for safeguarding the password that you use to access the Service.\r\n\r\nYou agree not to disclose your password to any third party.\r\n\r\n4. Intellectual Property\r\n\r\nThe Service and its original content (excluding content provided by users), features, and functionality are and will remain the exclusive property of FaithGuard and its licensors. The Service is protected by copyright, trademark, and other laws of both Belgium and foreign countries.\r\n\r\n5. User Conduct\r\n\r\nYou agree not to use the Service:\r\n- In any way that violates any applicable national or international law or regulation.\r\n- To transmit, or procure the sending of, any advertising or promotional material, including any \"junk mail\", \"chain letter,\" \"spam,\" or any other similar solicitation.\r\n- To impersonate or attempt to impersonate FaithGuard, a FaithGuard employee, another user, or any other person or entity.\r\n\r\n6. Limitation of Liability\r\n\r\nTo the maximum extent permitted by applicable law, in no event shall FaithGuard, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including, without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from (i) your access to or use of or inability to access or use the Service; (ii) any conduct or content of any third party on the Service.\r\n\r\n7. Governing Law\r\n\r\nThese Terms shall be governed and construed in accordance with the laws of Belgium, without regard to its conflict of law provisions. Any disputes arising from these Terms will be subject to the exclusive jurisdiction of the courts of Kortrijk.\r\n\r\n8. Changes\r\n\r\nWe reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will try to provide at least 30 days\' notice prior to any new terms taking effect.\r\n\r\n9. Contact Us\r\n\r\nIf you have any questions about these Terms, please contact us at info@faithguard.site.', '2025-12-15 21:01:44', '2025-12-15 21:52:49'),
(2, 'Privacy Policy', 'privacy', 'Privacy Policy', '1. Introduction\r\n\r\nFaithGuard (\"we,\" \"us,\" or \"our\") operates the website https://faithguard.site (the \"Service\"). We are committed to protecting your personal data and respecting your privacy in accordance with the General Data Protection Regulation (GDPR) and the Belgian Data Protection Act of 30 July 2018.\r\n\r\nThis Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.\r\n\r\n2. Controller Information\r\n\r\nData Controller:\r\nFaithGuard (WWTW)\r\nBelgium\r\nContact Email: info@faithguard.site\r\n\r\n3. Data We Collect\r\n\r\nWe collect several different types of information for various purposes to provide and improve our service to you.\r\n\r\nA. Personal Data\r\n\r\nWhile using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you (\"Personal Data\"). This may include, but is not limited to:\r\n\r\n- Email address\r\n- First name\r\n- Profile data (e.g., addiction type, progress logs) - Processed based on explicit consent.\r\n\r\nB. Special Categories of Personal Data\r\n\r\nGiven the nature of our service (faith-based recovery), you may voluntarily provide sensitive data regarding your health, religious beliefs, or addiction struggles. We process this data solely based on your explicit consent (Article 9(2)(a) GDPR).\r\n\r\nC. Usage Data\r\n\r\nWe may also collect information on how the Service is accessed and used (\"Usage Data\"). This may include your computer\'s Internet Protocol address (e.g., IP address), browser type, browser version, the pages of our Service that you visit, the time and date of your visit, the time spent on those pages, and other diagnostic data.\r\n\r\n4. Legal Basis for Processing (GDPR Art. 6)\r\n\r\nWe process your personal data under the following legal bases:\r\n\r\nConsent: You have given us clear consent to process your personal data for a specific purpose (e.g., account creation, newsletter).\r\n\r\nLegitimate Interests: Processing is necessary for our legitimate interests (e.g., security, fraud prevention) unless there is a good reason to protect your personal data which overrides those legitimate interests.\r\n\r\n5. How We Use Your Data\r\n\r\nFaithGuard uses the collected data for various purposes:\r\n- To provide and maintain the Service.\r\n- To notify you about changes to our Service.\r\n- To allow you to participate in interactive features (e.g., community, progress tracking) when you choose to do so.\r\n- To provide customer care and support.\r\n- To monitor the usage of the Service to detect, prevent and address technical issues.\r\n\r\n6. Data Retention\r\n\r\nWe will retain your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use your Personal Data to the extent necessary to comply with our legal obligations, resolve disputes, and enforce our legal agreements and policies.\r\n\r\n7. Data Transfer\r\n\r\nYour information, including Personal Data, is stored on servers located within the European Economic Area (EEA). If we transfer data outside the EEA, we ensure appropriate safeguards are in place in compliance with GDPR.\r\n\r\n8. Your Data Protection Rights (Belgium/EU)\r\n\r\nUnder the GDPR, you have the following rights:\r\n- The right to access: You have the right to request copies of your personal data.\r\n- The right to rectification: You have the right to request that we correct any information you believe is inaccurate.\r\n- The right to erasure (\"Right to be forgotten\"): You have the right to request that we erase your personal data, under certain conditions.\r\n- The right to restrict processing: You have the right to request that we restrict the processing of your personal data.\r\n- The right to object to processing: You have the right to object to our processing of your personal data.\r\n- The right to data portability: You have the right to request that we transfer the data that we have collected to another organization, or directly to you.\r\n- To exercise any of these rights, please contact us at [Your Email Address]. We have one month to respond to you.\r\n\r\n9. Security of Data\r\n\r\nThe security of your data is important to us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your Personal Data, we cannot guarantee its absolute security.\r\n\r\n10. Contact Authority\r\n\r\nIf you believe that your rights have been violated, you have the right to lodge a complaint with the Belgian Data Protection Authority:\r\n\r\nAutorité de protection des données (APD) / Gegevensbeschermingsautoriteit (GBA)\r\nRue de la Presse 35, 1000 Brussels\r\nEmail: contact@apd-gba.be\r\nWebsite: https://www.google.com/search?q=https://www.gegevenbeschermingsautoriteit.be                        ', '2025-12-15 21:01:44', '2025-12-15 21:43:49'),
(3, 'Cookie Policy', 'cookie', 'Cookie Policy', '1. What Are Cookies?\r\n\r\nCookies are small text files that are placed on your computer or mobile device by websites that you visit. They are widely used in order to make websites work, or work more efficiently, as well as to provide information to the owners of the site.\r\n\r\n2. How We Use Cookies\r\n\r\nFaithGuard uses cookies to improve your experience on our website. We use cookies for the following purposes:\r\n\r\nA. Strictly Necessary Cookies\r\n\r\nThese cookies are essential for you to browse the website and use its features, such as accessing secure areas of the site. Without these cookies, services you have asked for cannot be provided.\r\n\r\nPHPSESSID: This is a native PHP cookie that enables the website to store serialised state data. It is used to establish a user session and to pass state data via a temporary cookie, which is commonly referred to as a session cookie. Duration: Session (deleted when you close your browser).\r\n\r\nB. Functional Cookies (Optional)\r\n\r\nThese allow the website to remember choices you make (such as your username, language, or the region you are in) and provide enhanced, more personal features.\r\n\r\n[List any functional cookies here, or state \"None currently used\".]\r\n\r\nC. Analytics Cookies (Optional)\r\n\r\nWe may use third-party service providers to monitor and analyse the use of our Service.\r\n\r\n[If using Google Analytics, list _ga and _gid here. If not, state \"We do not currently use analytics cookies.\"]\r\n\r\n3. Your Consent\r\n\r\nWhen you first visit our website, we may ask for your consent to set non-essential cookies. Strictly necessary cookies (like PHPSESSID) do not require consent, as they are required for the site to function securely.\r\n\r\n4. Managing Cookies\r\n\r\nYou can set your browser not to accept cookies, and the website below tells you how to remove cookies from your browser. However, in a few cases, some of our website features may not function as a result.\r\n\r\n- Google Chrome\r\n- Mozilla Firefox\r\n- Microsoft Edge\r\n- Safari\r\n- Opera/Opera GX\r\n\r\n5. Contact Us\r\n\r\nIf you have any questions about our use of cookies, please contact us at info@faithguard.site.', '2025-12-15 21:01:44', '2025-12-15 21:48:29');

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
(1, 'What type of addiction are you currently struggling with?', '\r\n{\r\n  \"type\": \"checkbox\",\r\n  \"name\": \"addiction_type\",\r\n  \"scoring_type\": \"multiplier_base\",\r\n  \"options\": [\r\n    {\"label\": \"Substance (Drugs/Meds)\", \"value\": \"Substance\", \"weight\": 1.4},\r\n    {\"label\": \"Alcohol\", \"value\": \"Alcohol\", \"weight\": 1.3},\r\n    {\"label\": \"Sexual\", \"value\": \"Sexual\", \"weight\": 1.5},\r\n    {\"label\": \"Pornography\", \"value\": \"Pornography\", \"weight\": 1.5},\r\n    {\"label\": \"Gambling\", \"value\": \"Gambling\", \"weight\": 1.3},\r\n    {\"label\": \"Digital/Social Media\", \"value\": \"Digital/Social Media\", \"weight\": 1.1},\r\n    {\"label\": \"Food/Binge Eating\", \"value\": \"Food/Binge Eating\", \"weight\": 1.1},\r\n    {\"label\": \"Smoking\", \"value\": \"Smoking\", \"weight\": 1.2},\r\n    {\"label\": \"Other\", \"value\": \"Other\", \"weight\": 1.0}\r\n  ],\r\n  \"min_required\": 1,\r\n  \"max_allowed\": 3\r\n}\r\n', NULL),
(2, 'How often do you pray or engage in spiritual reflection?', '\r\n{\r\n  \"type\": \"radio\",\r\n  \"name\": \"spiritual_frequency\",\r\n  \"scoring_type\": \"score_modifier\",\r\n  \"options\": [\r\n    {\"label\": \"Daily\", \"score\": -5},\r\n    {\"label\": \"Weekly\", \"score\": -3},\r\n    {\"label\": \"Rarely\", \"score\": 1},\r\n    {\"label\": \"Never\", \"score\": 3}\r\n  ]\r\n}', NULL),
(3, 'Core Addiction and Wellness Assessment', '\r\n{\r\n  \"type\": \"likert_scale\",\r\n  \"name\": \"core_assessment\",\r\n  \"scale\": [\r\n    {\"label\": \"Never\", \"score\": 1},\r\n    {\"label\": \"Rarely\", \"score\": 2},\r\n    {\"label\": \"Sometimes\", \"score\": 3},\r\n    {\"label\": \"Often\", \"score\": 4},\r\n    {\"label\": \"Very Often\", \"score\": 5}\r\n  ],\r\n  \"sections\": [\r\n    {\r\n      \"title\": \"Spiritual Health & Alignment\",\r\n      \"category\": \"Spiritual Health\",\r\n      \"questions\": [\r\n        {\"id\": 1, \"text\": \"I turn to my addiction instead of prayer when I feel stressed.\"},\r\n        {\"id\": 2, \"text\": \"I feel distant from God because of my addictive habits.\"},\r\n        {\"id\": 3, \"text\": \"My addiction makes me avoid church, fellowship, or spiritual practices.\"},\r\n        {\"id\": 4, \"text\": \"I feel convicted by the Holy Spirit when engaging in my addiction.\"},\r\n        {\"id\": 5, \"text\": \"I rely more on my addiction than on God for comfort or relief.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Emotional Control & Coping\",\r\n      \"category\": \"Emotional Control\",\r\n      \"questions\": [\r\n        {\"id\": 6, \"text\": \"I use my addiction to cope with difficult emotions.\"},\r\n        {\"id\": 7, \"text\": \"I feel anxious or irritable when I can’t engage in my addiction.\"},\r\n        {\"id\": 8, \"text\": \"I feel guilt or shame after giving in to my addiction.\"},\r\n        {\"id\": 9, \"text\": \"My addiction feels like something I cannot control.\"},\r\n        {\"id\": 10, \"text\": \"I hide my struggles because I fear judgment from others.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Behavioural Impact\",\r\n      \"category\": \"Behavioural Impact\",\r\n      \"questions\": [\r\n        {\"id\": 11, \"text\": \"I have tried to cut down but failed.\"},\r\n        {\"id\": 12, \"text\": \"I spend more time than intended engaging with my addiction.\"},\r\n        {\"id\": 13, \"text\": \"I neglect responsibilities (school, work, home) because of my addiction.\"},\r\n        {\"id\": 14, \"text\": \"I’ve taken risks or made unwise decisions because of my addiction.\"},\r\n        {\"id\": 15, \"text\": \"I experience cravings that feel overwhelming.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Relational Impact\",\r\n      \"category\": \"Relational Impact\",\r\n      \"questions\": [\r\n        {\"id\": 16, \"text\": \"My relationships have suffered because of my addiction.\"},\r\n        {\"id\": 17, \"text\": \"I become defensive or irritated when someone confronts me about my habits.\"},\r\n        {\"id\": 18, \"text\": \"I isolate myself to hide my addictive behaviour.\"},\r\n        {\"id\": 19, \"text\": \"I avoid accountability from friends, family, or church.\"},\r\n        {\"id\": 20, \"text\": \"My addiction causes conflict with people close to me.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Daily Life & Functioning\",\r\n      \"category\": \"Daily Functioning\",\r\n      \"questions\": [\r\n        {\"id\": 21, \"text\": \"I delay tasks or responsibilities because of my addiction.\"},\r\n        {\"id\": 22, \"text\": \"I prioritize my addiction over healthier activities.\"},\r\n        {\"id\": 23, \"text\": \"My sleep schedule is negatively affected by my addiction.\"},\r\n        {\"id\": 24, \"text\": \"My finances are impacted because of my addiction.\"},\r\n        {\"id\": 25, \"text\": \"My physical health has worsened due to addictive behaviours.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Faith-Based Reflection & Accountability\",\r\n      \"category\": \"Faith Reflection\",\r\n      \"questions\": [\r\n        {\"id\": 26, \"text\": \"I feel that my addiction is harming my relationship with God.\"},\r\n        {\"id\": 27, \"text\": \"I avoid reading Scripture or praying when I slip into addiction.\"},\r\n        {\"id\": 28, \"text\": \"I feel unworthy of God’s forgiveness because of my addiction.\"},\r\n        {\"id\": 29, \"text\": \"I struggle to trust God with my healing and recovery.\"},\r\n        {\"id\": 30, \"text\": \"I believe my addiction is becoming an idol in my life.\"}\r\n      ]\r\n    },\r\n    {\r\n      \"title\": \"Self-Awareness & Desire for Change\",\r\n      \"category\": \"Self-Awareness\",\r\n      \"questions\": [\r\n        {\"id\": 31, \"text\": \"I want to stop, but I feel unable to do so.\"},\r\n        {\"id\": 32, \"text\": \"I recognize patterns in my life that lead me toward my addiction.\"},\r\n        {\"id\": 33, \"text\": \"I worry about long-term consequences of my addictive behaviours.\"},\r\n        {\"id\": 34, \"text\": \"I desire accountability or support to overcome my struggles.\"}\r\n      ]\r\n    }\r\n  ],\r\n  \"conditional_weights\": {\r\n      \"Pornography\": {\r\n          \"Spiritual Health\": 1.3,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.3,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Sexual\": {\r\n          \"Spiritual Health\": 1.3,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.3,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Alcohol\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.1,\r\n          \"Behavioural Impact\": 1.3,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.2,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.2\r\n      },\r\n      \"Substance\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.3,\r\n          \"Relational Impact\": 1.1,\r\n          \"Daily Functioning\": 1.4,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.2\r\n      },\r\n      \"Gambling\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.3,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.1\r\n      },\r\n      \"Smoking\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.3,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Digital/Social Media\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Food/Binge Eating\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.2,\r\n          \"Behavioural Impact\": 1.2,\r\n          \"Relational Impact\": 1.2,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      },\r\n      \"Other\": {\r\n          \"Spiritual Health\": 1.0,\r\n          \"Emotional Control\": 1.0,\r\n          \"Behavioural Impact\": 1.0,\r\n          \"Relational Impact\": 1.0,\r\n          \"Daily Functioning\": 1.0,\r\n          \"Faith Reflection\": 1.0,\r\n          \"Self-Awareness\": 1.0\r\n      }\r\n    }\r\n}', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `addiction_type` json NOT NULL,
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
(1, 'admin@faithguard.com', '$2y$10$ymjkffZvp1ckE/ttWlml2.9fw8jrKCmefQIUft6xIUZF7463qn.sa', 'Admin', 'admin', '2025-11-26 22:42:51'),
(2, 'thomas.deseure@proton.me', '$2y$12$MyyJKs/IDNNQIa.P577Fl.zZHEK4icsdaORfKlHQsp8Hb1q4W/12O', 'Thomas', 'user', '2025-12-19 23:44:13');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
