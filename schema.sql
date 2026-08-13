-- این فایل رو توی phpMyAdmin ایمپورت کن تا دیتابیس و جدول ساخته بشه

CREATE DATABASE IF NOT EXISTS khaneh_shopping
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE khaneh_shopping;

CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    quantity VARCHAR(100),
    category VARCHAR(100) NOT NULL,
    purchased TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
