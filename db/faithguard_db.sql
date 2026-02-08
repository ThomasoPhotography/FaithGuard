-- FaithGuard Database Schema
-- MySQL 8.0+ compatible

CREATE DATABASE IF NOT EXISTS ID483117_faithguard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ID483117_faithguard;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    avatar_url VARCHAR(255),
    bio TEXT,
    bible_language ENUM('en', 'nl') DEFAULT 'en',
    bible_version VARCHAR(20) DEFAULT 'NRSVUE',
    bible_book VARCHAR(50),
    is_admin BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User roles (for future expansion, currently using is_admin flag)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User accounts
INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `bio`, `bible_language`, `bible_version`, `bible_book`, `is_admin`, `email_verified`, `created_at`) VALUES
(1, 'admin@faithguard.com', '$2y$12$Si3tc0jbzi7SZ85svjKhLeMkVf1aoQcpMaGis/s.obNQoKdAm7YqW', 'admin', 'admin', 'To be Written', 'en', 'NRSVUE', 'Luke', TRUE, TRUE, '2025-12-06 23:03:16'),
(2, 'thomas.deseure@proton.me', '$2y$12$M5By2UvRbwuTvrFfYh42UOzM1tv1KnQGbedtlbWnCqu6wnNGrIATO', 'Thomas', 'user', 'To be Written', 'en', 'NRSVUE', 'Mark', FALSE, TRUE, '2025-12-07 00:04:49');

-- User preferences
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    bible_language ENUM('en', 'nl') DEFAULT 'en',
    bible_version VARCHAR(20) DEFAULT 'NRSVUE',

    bible_book VARCHAR(50) DEFAULT 'Luke',

    theme ENUM('light', 'dark') DEFAULT 'light',
    notifications_enabled BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_bible_book CHECK (
        bible_book IN (
            'Genesis','Exodus','Leviticus','Numbers','Deuteronomy',
            'Joshua','Judges','Ruth','1 Samuel','2 Samuel',
            '1 Kings','2 Kings','1 Chronicles','2 Chronicles',
            'Ezra','Nehemiah','Esther','Job','Psalms','Proverbs',
            'Ecclesiastes','Song of Solomon','Isaiah','Jeremiah',
            'Lamentations','Ezekiel','Daniel','Hosea','Joel',
            'Amos','Obadiah','Jonah','Micah','Nahum','Habakkuk',
            'Zephaniah','Haggai','Zechariah','Malachi',
            'Matthew','Mark','Luke','John','Acts','Romans',
            '1 Corinthians','2 Corinthians','Galatians',
            'Ephesians','Philippians','Colossians',
            '1 Thessalonians','2 Thessalonians',
            '1 Timothy','2 Timothy','Titus','Philemon',
            'Hebrews','James','1 Peter','2 Peter',
            '1 John','2 John','3 John','Jude','Revelation'
        )
    ),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_prefs (user_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Progress tracking
CREATE TABLE IF NOT EXISTS user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    streak_days INT DEFAULT 0,
    longest_streak INT DEFAULT 0,
    total_checkins INT DEFAULT 0,
    last_checkin DATE NULL,
    sobriety_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_progress (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily check-ins
CREATE TABLE IF NOT EXISTS checkins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    checkin_date DATE NOT NULL,
    mood_rating INT CHECK (mood_rating BETWEEN 1 AND 5),
    notes TEXT,
    triggers TEXT,
    victories TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_daily_checkin (user_id, checkin_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resources
CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content TEXT,
    type ENUM('article', 'video', 'audio', 'guide', 'prayer') NOT NULL,
    category VARCHAR(100),
    author VARCHAR(100),
    url VARCHAR(500),
    thumbnail_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quiz/Assessment questions
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    category VARCHAR(100),
    order_num INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quiz answers
CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text TEXT NOT NULL,
    score_value INT DEFAULT 0,
    order_num INT DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User quiz results
CREATE TABLE IF NOT EXISTS quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_score INT NOT NULL,
    category_scores JSON,
    recommendations TEXT,
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages/Community posts
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255),
    content TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    like_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments on posts
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prayer requests
CREATE TABLE IF NOT EXISTS prayer_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255),
    content TEXT NOT NULL,
    is_anonymous BOOLEAN DEFAULT FALSE,
    is_answered BOOLEAN DEFAULT FALSE,
    prayer_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions for authentication
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample resources
INSERT INTO resources (title, description, type, category, author) VALUES
('Understanding Digital Addiction', 'A comprehensive guide to understanding how digital addiction affects our spiritual life.', 'article', 'education', 'FaithGuard Team'),
('Daily Prayer for Strength', 'A morning prayer to start your day with purpose and protection.', 'prayer', 'spiritual', 'Pastor John Davis'),
('Breaking Free: Video Series', '5-part video series on overcoming addiction through faith.', 'video', 'recovery', 'Dr. Sarah Mitchell'),
('Scripture Meditation Guide', 'Learn to meditate on God''s Word for healing and transformation.', 'guide', 'spiritual', 'FaithGuard Team'),
('The Armor of God', 'Understanding and applying Ephesians 6 in your daily battle.', 'article', 'spiritual', 'Pastor Mike Johnson');

-- Insert sample quiz questions
INSERT INTO quiz_questions (question, category, order_num) VALUES
('How often do you find yourself using digital devices without a specific purpose?', 'usage', 1),
('Do you feel anxious or restless when you cannot access your devices?', 'dependence', 2),
('Has your digital usage affected your sleep patterns?', 'health', 3),
('Do you find it difficult to focus on prayer or reading Scripture due to digital distractions?', 'spiritual', 4),
('Have you tried to reduce your screen time but found it difficult?', 'control', 5);

INSERT INTO quiz_answers (question_id, answer_text, score_value, order_num) VALUES
(1, 'Never or rarely', 1, 1),
(1, 'Sometimes', 2, 2),
(1, 'Often', 3, 3),
(1, 'Very frequently', 4, 4),
(2, 'Never', 1, 1),
(2, 'Occasionally', 2, 2),
(2, 'Frequently', 3, 3),
(2, 'Always', 4, 4),
(3, 'Not at all', 1, 1),
(3, 'Slightly', 2, 2),
(3, 'Moderately', 3, 3),
(3, 'Significantly', 4, 4),
(4, 'Never', 1, 1),
(4, 'Rarely', 2, 2),
(4, 'Sometimes', 3, 3),
(4, 'Often', 4, 4),
(5, 'No, I have not tried', 1, 1),
(5, 'Tried but succeeded', 2, 2),
(5, 'Tried with difficulty', 3, 3),
(5, 'Tried multiple times unsuccessfully', 4, 4);

-- Cache for scripture content to minimize API calls
CREATE TABLE IF NOT EXISTS scripture_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) NOT NULL,
    version VARCHAR(20) NOT NULL,
    content TEXT NOT NULL,
    fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_reference_version (reference, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache for book and chapter metadata
CREATE TABLE IF NOT EXISTS bible_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    language VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_book_lang (book_id, language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bible_chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id VARCHAR(50) NOT NULL,
    book_id VARCHAR(50) NOT NULL,
    chapter_number INT NOT NULL,
    language VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_chapter_lang (chapter_id, language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
