-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 10:30 PM
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
-- Database: `faithguard_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `duration` varchar(50) NOT NULL,
  `accountability` varchar(255) NOT NULL,
  `resources_of_interest` text NOT NULL,
  `spiritual_connection` varchar(255) NOT NULL,
  `primary_goal` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `TYPE` varchar(100) NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `tags` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `title`, `description`, `category`, `TYPE`, `difficulty`, `tags`) VALUES
(1, 'Bible Study Guide', 'A comprehensive guide to studying the Bible.', 'Spiritual Growth', 'Book', 'Beginner', 'Bible, Study, Faith'),
(2, 'Prayer Journal', 'Track your daily prayers and reflections.', 'Prayer', 'Tool', 'Intermediate', 'Prayer, Journaling, Reflection'),
(3, 'Christian Apologetics Course', 'Learn to defend your faith logically.', 'Apologetics', 'Online Course', 'Advanced', 'Apologetics, Defense, Logic'),
(4, 'Worship Music Playlist', 'Curated songs for worship and praise.', 'Worship', 'Audio', 'Beginner', 'Music, Worship, Praise'),
(5, 'Community Bible Study', 'Join a local group for shared learning.', 'Community', 'Group', 'Intermediate', 'Bible, Group, Fellowship'),
(6, 'Devotional App', 'Daily devotionals on your phone.', 'Devotion', 'App', 'Beginner', 'Devotional, App, Daily'),
(7, 'Theology Basics', 'Introduction to Christian theology.', 'Education', 'Book', 'Intermediate', 'Theology, Basics, Christian'),
(8, 'Faith-Based Counseling', 'Professional counseling with a Christian perspective.', 'Counseling', 'Service', 'Advanced', 'Counseling, Faith, Mental Health'),
(9, 'Scripture Memorization Tool', 'Apps and methods for memorizing verses.', 'Memory', 'Tool', 'Beginner', 'Scripture, Memorization, Verses'),
(10, 'Evangelism Training', 'Learn how to share the Gospel effectively.', 'Evangelism', 'Workshop', 'Intermediate', 'Evangelism, Gospel, Sharing'),
(11, 'Christian Podcasts', 'Podcasts on faith and life topics.', 'Media', 'Audio', 'Beginner', 'Podcasts, Faith, Life'),
(12, 'Family Bible Time', 'Resources for family-oriented Bible study.', 'Family', 'Guide', 'Beginner', 'Family, Bible, Study'),
(13, 'Spiritual Retreats', 'Organized retreats for spiritual renewal.', 'Retreat', 'Event', 'Advanced', 'Retreat, Renewal, Spiritual'),
(14, 'Online Bible Commentary', 'In-depth commentaries on Bible books.', 'Study', 'Online', 'Intermediate', 'Bible, Commentary, Study'),
(15, 'Youth Ministry Resources', 'Tools for engaging young people in faith.', 'Youth', 'Kit', 'Beginner', 'Youth, Ministry, Engagement');

-- --------------------------------------------------------

--
-- Table structure for table `testimonies`
--

CREATE TABLE `testimonies` (
  `testimony_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quote_text` text NOT NULL,
  `cite_name` varchar(100) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonies`
--

INSERT INTO `testimonies` (`testimony_id`, `user_id`, `quote_text`, `cite_name`, `approved`, `created_at`) VALUES
(1, NULL, 'FaithGuard helped me deepen my spiritual connection and find accountability in my faith journey.', 'Anonymous User', 1, '2025-11-19 21:28:27'),
(2, NULL, 'The resources here transformed my daily prayer life.', 'John Doe', 1, '2025-11-19 21:28:27'),
(3, NULL, 'I feel more connected to my community through these tools.', 'Jane Smith', 0, '2025-11-19 21:28:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

--
-- Indexes for table `testimonies`
--
ALTER TABLE `testimonies`
  ADD PRIMARY KEY (`testimony_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `resource_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `testimonies`
--
ALTER TABLE `testimonies`
  MODIFY `testimony_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `testimonies`
--
ALTER TABLE `testimonies`
  ADD CONSTRAINT `testimonies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
