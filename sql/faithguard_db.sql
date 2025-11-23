-- FaithGuard Database Schema
-- Database Name: faithguard_db
-- Run this script to create the database and tables.

CREATE DATABASE IF NOT EXISTS faithguard_db;
USE faithguard_db;

-- Users table for authentication
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE
);

-- Quizzes table for storing user quiz results
CREATE TABLE quizzes (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,  -- Foreign key to users, NULL for guests
    duration VARCHAR(50) NOT NULL,
    accountability VARCHAR(255) NOT NULL,
    resources_of_interest TEXT NOT NULL,  -- Comma-separated or JSON string
    spiritual_connection VARCHAR(255) NOT NULL,
    primary_goal VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Resources table for dynamic resource display
CREATE TABLE resources (
    resource_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,  -- Foreign key to users (creator/admin), NULL for system/initial resources
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    type VARCHAR(100) NOT NULL,
    difficulty VARCHAR(50) NOT NULL,
    tags TEXT NOT NULL,  -- Comma-separated string
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Testimonies table for user impact quotes
CREATE TABLE testimonies (
    testimony_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,  -- Foreign key to users, NULL for anonymous
    quote_text TEXT NOT NULL,
    cite_name VARCHAR(100) NOT NULL,
    approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Sample INSERT statements for resources (based on typical structure from resources.js; 15 entries)
-- Updated to include user_id (NULL for initial/system resources)
INSERT INTO resources (user_id, title, description, category, type, difficulty, tags) VALUES
(NULL, 'Bible Study Guide', 'A comprehensive guide to studying the Bible.', 'Spiritual Growth', 'Book', 'Beginner', 'Bible, Study, Faith'),
(NULL, 'Prayer Journal', 'Track your daily prayers and reflections.', 'Prayer', 'Tool', 'Intermediate', 'Prayer, Journaling, Reflection'),
(NULL, 'Christian Apologetics Course', 'Learn to defend your faith logically.', 'Apologetics', 'Online Course', 'Advanced', 'Apologetics, Defense, Logic'),
(NULL, 'Worship Music Playlist', 'Curated songs for worship and praise.', 'Worship', 'Audio', 'Beginner', 'Music, Worship, Praise'),
(NULL, 'Community Bible Study', 'Join a local group for shared learning.', 'Community', 'Group', 'Intermediate', 'Bible, Group, Fellowship'),
(NULL, 'Devotional App', 'Daily devotionals on your phone.', 'Devotion', 'App', 'Beginner', 'Devotional, App, Daily'),
(NULL, 'Theology Basics', 'Introduction to Christian theology.', 'Education', 'Book', 'Intermediate', 'Theology, Basics, Christian'),
(NULL, 'Faith-Based Counseling', 'Professional counseling with a Christian perspective.', 'Counseling', 'Service', 'Advanced', 'Counseling, Faith, Mental Health'),
(NULL, 'Scripture Memorization Tool', 'Apps and methods for memorizing verses.', 'Memory', 'Tool', 'Beginner', 'Scripture, Memorization, Verses'),
(NULL, 'Evangelism Training', 'Learn how to share the Gospel effectively.', 'Evangelism', 'Workshop', 'Intermediate', 'Evangelism, Gospel, Sharing'),
(NULL, 'Christian Podcasts', 'Podcasts on faith and life topics.', 'Media', 'Audio', 'Beginner', 'Podcasts, Faith, Life'),
(NULL, 'Family Bible Time', 'Resources for family-oriented Bible study.', 'Family', 'Guide', 'Beginner', 'Family, Bible, Study'),
(NULL, 'Spiritual Retreats', 'Organized retreats for spiritual renewal.', 'Retreat', 'Event', 'Advanced', 'Retreat, Renewal, Spiritual'),
(NULL, 'Online Bible Commentary', 'In-depth commentaries on Bible books.', 'Study', 'Online', 'Intermediate', 'Bible, Commentary, Study'),
(NULL, 'Youth Ministry Resources', 'Tools for engaging young people in faith.', 'Youth', 'Kit', 'Beginner', 'Youth, Ministry, Engagement');

-- Sample INSERT statements for testimonies (including initial static one from index.html)
INSERT INTO testimonies (user_id, quote_text, cite_name, approved, created_at) VALUES
(NULL, 'FaithGuard helped me deepen my spiritual connection and find accountability in my faith journey.', 'Anonymous User', TRUE, NOW()),
(NULL, 'The resources here transformed my daily prayer life.', 'John Doe', TRUE, NOW()),
(NULL, 'I feel more connected to my community through these tools.', 'Jane Smith', FALSE, NOW());