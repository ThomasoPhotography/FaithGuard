-- =====================================================
-- FaithGuard Database
-- Part 1/6: Core User & Authentication Tables
-- MySQL 8.0.36+
-- PHP 7.4 Compatible
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- =====================================================
-- USERS
-- =====================================================

CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,

    `email` VARCHAR(255) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,

    `username` VARCHAR(100) DEFAULT NULL,

    `first_name` VARCHAR(100) DEFAULT NULL,
    `last_name` VARCHAR(100) DEFAULT NULL,

    `avatar_url` VARCHAR(255) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,

    `bible_language` ENUM('en','nl') DEFAULT 'en',
    `bible_version` VARCHAR(20) DEFAULT 'NRSVUE',
    `bible_book` VARCHAR(50) DEFAULT NULL,

    `is_admin` TINYINT(1) DEFAULT 0,
    `is_member` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `email_verified` TINYINT(1) DEFAULT 0,

    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    `last_login` TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `unique_email` (`email`),
    UNIQUE KEY `unique_username` (`username`),

    INDEX `idx_email` (`email`),
    INDEX `idx_username` (`username`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ROLES
-- =====================================================

CREATE TABLE `roles` (
    `id` INT NOT NULL AUTO_INCREMENT,

    `name` VARCHAR(50) NOT NULL,
    `description` TEXT DEFAULT NULL,

    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    UNIQUE KEY `unique_role_name` (`name`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles`
(`name`, `description`)
VALUES
('admin', 'Administrator role'),
('user', 'Standard user role');

-- =====================================================
-- USER PREFERENCES
-- =====================================================

CREATE TABLE `user_preferences` (
    `id` INT NOT NULL AUTO_INCREMENT,

    `user_id` INT NOT NULL,

    `bible_language`
        ENUM('en','nl')
        DEFAULT 'en',

    `bible_version`
        VARCHAR(20)
        DEFAULT 'NRSVUE',

    `bible_book`
        VARCHAR(50)
        DEFAULT 'Matthew',

    `theme`
        ENUM('light','dark')
        DEFAULT 'light',

    `notifications_enabled`
        TINYINT(1)
        DEFAULT 1,

    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),

    UNIQUE KEY `unique_user_preferences`
        (`user_id`),

    CONSTRAINT `fk_preferences_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- USER PROGRESS
-- =====================================================

CREATE TABLE `user_progress` (
    `id` INT NOT NULL AUTO_INCREMENT,

    `user_id` INT NOT NULL,

    `streak_days`
        INT DEFAULT 0,

    `longest_streak`
        INT DEFAULT 0,

    `total_checkins`
        INT DEFAULT 0,

    `last_checkin`
        DATE DEFAULT NULL,

    `sobriety_date`
        DATE DEFAULT NULL,

    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),

    UNIQUE KEY `unique_user_progress`
        (`user_id`),

    CONSTRAINT `fk_progress_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SESSIONS
-- =====================================================

CREATE TABLE `sessions` (
    `id` VARCHAR(128) NOT NULL,

    `user_id` INT NOT NULL,

    `expires_at`
        TIMESTAMP NOT NULL,

    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),

    INDEX `idx_session_user`
        (`user_id`),

    INDEX `idx_session_expiry`
        (`expires_at`),


    CONSTRAINT `fk_sessions_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CSRF TOKENS
-- =====================================================

CREATE TABLE `csrf_tokens` (
    `id` INT NOT NULL AUTO_INCREMENT,

    `user_id` INT DEFAULT NULL,

    `token`
        VARCHAR(255)
        NOT NULL,

    `expires_at`
        TIMESTAMP NOT NULL,

    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),

    UNIQUE KEY `unique_csrf_token`
        (`token`),

    INDEX `idx_csrf_user`
        (`user_id`),

    INDEX `idx_csrf_expiry`
        (`expires_at`),


    CONSTRAINT `fk_csrf_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`)
        ON DELETE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BIBLE BOOKS
-- =====================================================

CREATE TABLE `bible_books` (

    `id` INT NOT NULL AUTO_INCREMENT,

    -- API identifier (e.g. "GEN", "MAT")
    `book_id`
        VARCHAR(50)
        NOT NULL,

    `name`
        VARCHAR(100)
        NOT NULL,

    `abbreviation`
        VARCHAR(20)
        DEFAULT NULL,

    `testament`
        ENUM('old','new')
        DEFAULT NULL,

    `language`
        VARCHAR(10)
        NOT NULL,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    UNIQUE KEY `unique_book_language`
        (`book_id`, `language`),


    INDEX `idx_book_language`
        (`language`)


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BIBLE CHAPTERS
-- =====================================================

CREATE TABLE `bible_chapters` (

    `id` INT NOT NULL AUTO_INCREMENT,


    -- API chapter identifier
    -- Example: "MAT.1"
    `chapter_id`
        VARCHAR(50)
        NOT NULL,


    `book_id`
        VARCHAR(50)
        NOT NULL,


    `chapter_number`
        INT
        NOT NULL,


    `language`
        VARCHAR(10)
        NOT NULL,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    UNIQUE KEY `unique_chapter_language`
        (`chapter_id`, `language`),


    INDEX `idx_chapter_book`
        (`book_id`),

    INDEX `idx_chapter_language`
        (`language`)


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BIBLE VERSES
-- =====================================================

CREATE TABLE `bible_verses` (

    `id` INT NOT NULL AUTO_INCREMENT,


    -- API verse identifier
    `verse_id`
        VARCHAR(100)
        NOT NULL,


    -- Bible API chapter identifier
    `chapter_id`
        VARCHAR(50)
        NOT NULL,


    -- Translation identifier
    -- Required by FaithGuardRepository::getVerseFromCache()
    `bible_id`
        VARCHAR(50)
        NOT NULL,


    `verse_number`
        INT
        NOT NULL,


    `content`
        TEXT
        NOT NULL,


    `language`
        VARCHAR(10)
        NOT NULL,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    `fetched_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    UNIQUE KEY `unique_verse_cache`
        (
            `bible_id`,
            `chapter_id`,
            `verse_number`
        ),


    INDEX `idx_verse_chapter`
        (`chapter_id`),

    INDEX `idx_verse_language`
        (`language`)


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SCRIPTURE CACHE
-- =====================================================

CREATE TABLE `scripture_cache` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `reference`
        VARCHAR(100)
        NOT NULL,


    `version`
        VARCHAR(50)
        NOT NULL,


    `content`
        TEXT
        NOT NULL,


    `fetched_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    UNIQUE KEY `unique_reference_version`
        (
            `reference`,
            `version`
        )


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- POSTS
-- =====================================================

CREATE TABLE `posts` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `user_id`
        INT NOT NULL,


    `title`
        VARCHAR(255)
        DEFAULT NULL,


    `content`
        TEXT
        NOT NULL,


    `is_anonymous`
        TINYINT(1)
        DEFAULT 0,


    `is_pinned`
        TINYINT(1)
        DEFAULT 0,


    `like_count`
        INT
        DEFAULT 0,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    INDEX `idx_posts_user`
        (`user_id`),


    INDEX `idx_posts_created`
        (`created_at`),


    INDEX `idx_posts_pinned`
        (`is_pinned`),


    CONSTRAINT `fk_posts_user`

        FOREIGN KEY (`user_id`)

        REFERENCES `users` (`id`)

        ON DELETE CASCADE


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- COMMENTS
-- =====================================================

CREATE TABLE `comments` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `post_id`
        INT NOT NULL,


    `user_id`
        INT NOT NULL,


    `content`
        TEXT
        NOT NULL,


    `is_anonymous`
        TINYINT(1)
        DEFAULT 0,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    INDEX `idx_comments_post`
        (`post_id`),


    INDEX `idx_comments_user`
        (`user_id`),


    INDEX `idx_comments_created`
        (`created_at`),


    CONSTRAINT `fk_comments_post`

        FOREIGN KEY (`post_id`)

        REFERENCES `posts` (`id`)

        ON DELETE CASCADE,


    CONSTRAINT `fk_comments_user`

        FOREIGN KEY (`user_id`)

        REFERENCES `users` (`id`)

        ON DELETE CASCADE


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PRAYER REQUESTS
-- =====================================================

CREATE TABLE `prayer_requests` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `user_id`
        INT NOT NULL,


    `title`
        VARCHAR(255)
        DEFAULT NULL,


    `content`
        TEXT
        NOT NULL,


    `is_anonymous`
        TINYINT(1)
        DEFAULT 0,


    `is_answered`
        TINYINT(1)
        DEFAULT 0,


    `prayer_count`
        INT
        DEFAULT 0,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    INDEX `idx_prayers_user`
        (`user_id`),


    INDEX `idx_prayers_answered`
        (`is_answered`),


    CONSTRAINT `fk_prayers_user`

        FOREIGN KEY (`user_id`)

        REFERENCES `users` (`id`)

        ON DELETE CASCADE


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TESTIMONIALS
-- =====================================================

CREATE TABLE `testimonials` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `user_id`
        INT NOT NULL,


    `content`
        TEXT
        NOT NULL,


    `is_approved`
        TINYINT(1)
        DEFAULT 0,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    INDEX `idx_testimonials_user`
        (`user_id`),


    INDEX `idx_testimonials_approved`
        (`is_approved`),


    CONSTRAINT `fk_testimonials_user`

        FOREIGN KEY (`user_id`)

        REFERENCES `users` (`id`)

        ON DELETE CASCADE


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- RESOURCES
-- =====================================================

CREATE TABLE `resources` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    -- Friendly URL identifier
    -- Required by getResourceBySlug()
    `slug`
        VARCHAR(255)
        NOT NULL,


    `title`
        VARCHAR(255)
        NOT NULL,


    `description`
        TEXT
        DEFAULT NULL,


    `content`
        LONGTEXT
        DEFAULT NULL,


    `type`
        ENUM(
            'article',
            'video',
            'audio',
            'guide',
            'prayer'
        )
        NOT NULL,


    `category`
        VARCHAR(100)
        DEFAULT NULL,


    `author`
        VARCHAR(100)
        DEFAULT NULL,


    `url`
        VARCHAR(500)
        DEFAULT NULL,


    `thumbnail_url`
        VARCHAR(255)
        DEFAULT NULL,


    `is_featured`
        TINYINT(1)
        DEFAULT 0,


    `view_count`
        INT
        DEFAULT 0,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    UNIQUE KEY `unique_resource_slug`
        (`slug`),


    INDEX `idx_resource_type`
        (`type`),


    INDEX `idx_resource_category`
        (`category`),


    INDEX `idx_resource_featured`
        (`is_featured`),


    INDEX `idx_resource_views`
        (`view_count`),


    INDEX `idx_resource_created`
        (`created_at`)


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DEFAULT RESOURCES
-- =====================================================

INSERT INTO `resources`
(
    `slug`,
    `title`,
    `description`,
    `type`,
    `category`,
    `author`
)

VALUES

(
    'understanding-digital-addiction',
    'Understanding Digital Addiction',
    'A comprehensive guide to understanding how digital addiction affects spiritual life.',
    'article',
    'education',
    'FaithGuard Team'
),

(
    'daily-prayer-for-strength',
    'Daily Prayer for Strength',
    'A morning prayer for strength, discipline, and protection.',
    'prayer',
    'spiritual',
    'FaithGuard Team'
),

(
    'breaking-free-video-series',
    'Breaking Free: Video Series',
    'A video series about overcoming addiction through faith.',
    'video',
    'recovery',
    'FaithGuard Team'
),

(
    'scripture-meditation-guide',
    'Scripture Meditation Guide',
    'A guide for meditating on God’s Word during recovery.',
    'guide',
    'spiritual',
    'FaithGuard Team'
),

(
    'armor-of-god',
    'The Armor of God',
    'Understanding and applying Ephesians 6 in daily life.',
    'article',
    'spiritual',
    'FaithGuard Team'
);

-- =====================================================
-- POLICY CMS
-- =====================================================

CREATE TABLE `policy` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `slug`
        VARCHAR(100)
        NOT NULL,


    `content_title`
        VARCHAR(255)
        DEFAULT NULL,


    `content_text`
        LONGTEXT
        DEFAULT NULL,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    `updated_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    UNIQUE KEY `unique_policy_slug`
        (`slug`)


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;



INSERT INTO `policy`
(
    `slug`,
    `content_title`,
    `content_text`
)

VALUES

(
    'terms',
    'Terms of Service',
    'To be completed.'
),

(
    'privacy',
    'Privacy Policy',
    'To be completed.'
),

(
    'cookie',
    'Cookie Policy',
    'To be completed.'
);

-- =====================================================
-- QUIZ QUESTIONS
-- =====================================================

CREATE TABLE `quiz_questions` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `question`
        TEXT
        NOT NULL,


    -- Addiction area/category
    -- Example:
    -- pornography
    -- alcohol
    -- drugs
    -- gaming
    -- social_media
    `category`
        VARCHAR(100)
        DEFAULT NULL,


    -- Display order
    `order_num`
        INT
        DEFAULT 0,


    `is_active`
        TINYINT(1)
        DEFAULT 1,


    `created_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    INDEX `idx_question_category`
        (`category`),


    INDEX `idx_question_active_order`
        (`is_active`, `order_num`)


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;



-- =====================================================
-- QUIZ ANSWERS
-- =====================================================

CREATE TABLE `quiz_answers` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `question_id`
        INT NOT NULL,


    `answer_text`
        TEXT
        NOT NULL,


    -- Likert scoring value
    -- Example:
    -- Never = 0
    -- Rarely = 1
    -- Sometimes = 2
    -- Often = 3
    -- Always = 4
    `score_value`
        INT
        DEFAULT 0,


    `order_num`
        INT
        DEFAULT 0,


    PRIMARY KEY (`id`),


    INDEX `idx_answer_question`
        (`question_id`),


    CONSTRAINT `fk_answers_question`

        FOREIGN KEY (`question_id`)

        REFERENCES `quiz_questions` (`id`)

        ON DELETE CASCADE


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;



-- =====================================================
-- QUIZ RESULTS
-- =====================================================

CREATE TABLE `quiz_results` (

    `id`
        INT NOT NULL AUTO_INCREMENT,


    `user_id`
        INT NOT NULL,


    `total_score`
        INT NOT NULL,


    -- Stores category breakdown:
    -- {
    --   "pornography": 72,
    --   "alcohol": 25
    -- }
    `category_scores`
        JSON
        DEFAULT NULL,


    `recommendations`
        TEXT
        DEFAULT NULL,


    `taken_at`
        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,


    PRIMARY KEY (`id`),


    INDEX `idx_quiz_user`
        (`user_id`),


    INDEX `idx_quiz_taken`
        (`taken_at`),


    CONSTRAINT `fk_quiz_results_user`

        FOREIGN KEY (`user_id`)

        REFERENCES `users` (`id`)

        ON DELETE CASCADE


) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Foreign Key Constraints
-- --------------------------------------------------------

ALTER TABLE `bible_chapters`
  ADD CONSTRAINT `fk_bible_chapters_book`
  FOREIGN KEY (`book_id`)
  REFERENCES `bible_books` (`book_id`)
  ON DELETE CASCADE;


ALTER TABLE `bible_verses`
  ADD CONSTRAINT `fk_bible_verses_chapter`
  FOREIGN KEY (`chapter_id`)
  REFERENCES `bible_chapters` (`chapter_id`)
  ON DELETE CASCADE;


ALTER TABLE `checkins`
  ADD CONSTRAINT `fk_checkins_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_post`
  FOREIGN KEY (`post_id`)
  REFERENCES `posts` (`id`)
  ON DELETE CASCADE,

  ADD CONSTRAINT `fk_comments_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `prayer_requests`
  ADD CONSTRAINT `fk_prayer_requests_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `fk_quiz_answers_question`
  FOREIGN KEY (`question_id`)
  REFERENCES `quiz_questions` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `quiz_results`
  ADD CONSTRAINT `fk_quiz_results_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_sessions_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `csrf_tokens`
  ADD CONSTRAINT `fk_csrf_tokens_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `user_preferences`
  ADD CONSTRAINT `fk_user_preferences_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `user_progress`
  ADD CONSTRAINT `fk_user_progress_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


ALTER TABLE `testimonials`
  ADD CONSTRAINT `fk_testimonials_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


-- --------------------------------------------------------
-- Additional Performance Indexes
-- --------------------------------------------------------

ALTER TABLE `bible_books`
  ADD INDEX `idx_language`
  (`language`);


ALTER TABLE `bible_chapters`
  ADD INDEX `idx_book_language`
  (`book_id`,`language`);


ALTER TABLE `bible_verses`
  ADD INDEX `idx_chapter_language`
  (`chapter_id`,`language`);


ALTER TABLE `policy`
  ADD INDEX `idx_policy_slug`
  (`slug`);


ALTER TABLE `quiz_results`
  ADD INDEX `idx_quiz_user_date`
  (`user_id`,`taken_at`);


ALTER TABLE `resources`
  ADD INDEX `idx_featured_created`
  (`is_featured`,`created_at`);


ALTER TABLE `csrf_tokens`
  ADD INDEX `idx_token_expiry`
  (`token`,`expires_at`);


COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;