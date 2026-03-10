-- =========================================
-- STUDENT MANAGEMENT INFORMATION SYSTEM
-- FINAL DATABASE SQL
-- =========================================

-- Create Database
CREATE DATABASE IF NOT EXISTS student_mis;
USE student_mis;

-- =========================================
-- USERS TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL
);

-- =========================================
-- STUDENTS TABLE
-- =========================================
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    course VARCHAR(100) NOT NULL,
    semester VARCHAR(20) NOT NULL
);

-- =========================================
-- ATTENDANCE TABLE (WITH LOCATION SUPPORT)
-- =========================================
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status VARCHAR(10) NOT NULL,
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- SAFE DEFAULT ADMIN USER
-- =========================================
INSERT INTO users (name, email, password, role)
SELECT 'Admin', 'admin@gmail.com', 'admin123', 'admin'
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@gmail.com'
);
