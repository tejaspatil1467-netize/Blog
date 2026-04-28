-- ============================================================
--  TechSync Blog — Database Setup
--  Import this file in phpMyAdmin or run:
--    mysql -u root -p < database/blog.sql
-- ============================================================

-- Create and select database
CREATE DATABASE IF NOT EXISTS techsync_blog
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE techsync_blog;

-- ── Table: users ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id       INT          AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,        -- Stored as password_hash()
    role     ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table: blogs ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blogs (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    content    TEXT         NOT NULL,
    status     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    user_id    INT          NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Seed: Default Admin Account ───────────────────────────────
-- Username: admin | Password: admin123
-- This hash was generated with: password_hash('admin123', PASSWORD_DEFAULT)
-- You can verify by running this in PHP: echo password_hash('admin123', PASSWORD_DEFAULT);
--
-- IMPORTANT: Run this SQL AFTER the tables are created.
-- The hash below is a valid bcrypt hash for 'admin123'.
-- If you want to regenerate, run the PHP line above and update this value.
INSERT IGNORE INTO users (username, password, role)
VALUES (
    'admin',
    '$2y$10$TKh8H1.PfQ0A7cME/q3fCOFQV3hJpOlMpZHQwkwFPmr6lhFz5C5gG',
    'admin'
);

-- ── Alternative: Create admin via PHP ────────────────────────
-- After importing this SQL, open XAMPP phpMyAdmin and run:
--   UPDATE users
--   SET password = '<paste hash from php -r "echo password_hash(\"admin123\",PASSWORD_DEFAULT);">
--   WHERE username = 'admin';
--
-- OR simply register normally and then manually set role='admin' in phpMyAdmin.

