```sql
-- ============================================
-- LIBRARY MANAGEMENT SYSTEM
-- Database: library_management
-- ============================================


-- Create Database

CREATE DATABASE IF NOT EXISTS library_management;

USE library_management;


-- ============================================
-- USERS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(100) NOT NULL,

    username VARCHAR(50) NOT NULL UNIQUE,

    email VARCHAR(100) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);


-- ============================================
-- BOOKS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS books (

    id INT AUTO_INCREMENT PRIMARY KEY,

    book_name VARCHAR(150) NOT NULL,

    author VARCHAR(100) NOT NULL,

    book_code VARCHAR(50) NOT NULL UNIQUE,

    category VARCHAR(50) NOT NULL,

    status ENUM('Available', 'Issued') NOT NULL DEFAULT 'Available',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);
-- ============================================
-- SAMPLE BOOK DATA
-- ============================================

INSERT INTO books
    (book_name, author, book_code, category, status)
VALUES
    ('Python Programming Basics', 'John Carter', 'LIB001', 'Programming', 'Available'),

    ('Database Systems', 'Robert Smith', 'LIB002', 'Database', 'Available'),

    ('Computer Networks', 'Michael Brown', 'LIB003', 'Networking', 'Issued'),

    ('Operating System Concepts', 'William Davis', 'LIB004', 'Operating Systems', 'Available'),

    ('Engineering Mathematics', 'David Wilson', 'LIB005', 'Mathematics', 'Issued');
