-- =======================================================
-- Database Schema for Vaccination Management System
-- Database Name: vaccination_management_system
-- Designed by: Antigravity
-- Date: 2026-02-03
-- =======================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------
-- 1. Create Database
-- -------------------------------------------------------
CREATE DATABASE IF NOT EXISTS vaccination_management_system;
USE vaccination_management_system;

-- -------------------------------------------------------
-- 2. Drop existing tables if they exist (Reverse order)
-- -------------------------------------------------------
DROP TABLE IF EXISTS vaccination_records;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS vaccination_schedule;
DROP TABLE IF EXISTS vaccines;
DROP TABLE IF EXISTS children;
DROP TABLE IF EXISTS hospitals;
DROP TABLE IF EXISTS parents;
DROP TABLE IF EXISTS users;

-- -------------------------------------------------------
-- 3. Create Tables
-- -------------------------------------------------------

-- Table: users
-- Stores all system users (Admin, Parent, Hospital)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Should store hashed passwords
    role ENUM('admin', 'parent', 'hospital') NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: parents
-- Stores parent specific details, linked to users
CREATE TABLE parents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: hospitals
-- Stores hospital specific details, linked to users
CREATE TABLE hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    hospital_name VARCHAR(150) NOT NULL,
    registration_no VARCHAR(50) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    status ENUM('approved', 'pending', 'rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: children
-- Stores children details linked to a parent
CREATE TABLE children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    blood_group VARCHAR(5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: vaccines
-- List of available vaccines
CREATE TABLE vaccines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vaccine_name VARCHAR(100) NOT NULL,
    description TEXT,
    availability_status ENUM('available', 'unavailable') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table: vaccination_schedule
-- Schedule of vaccines for each child
CREATE TABLE vaccination_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    vaccine_id INT NOT NULL,
    scheduled_date DATE NOT NULL,
    status ENUM('pending', 'vaccinated', 'missed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: appointments
-- Appointments booked by parents at hospitals
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    hospital_id INT NOT NULL,
    child_id INT NOT NULL,
    vaccine_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('requested', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'requested',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE,
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table: vaccination_records
-- Record of actual vaccination administration
CREATE TABLE vaccination_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL UNIQUE, -- One record per appointment
    vaccinated_date DATE NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------
-- 4. Sample Data Insertion
-- -------------------------------------------------------

-- Users (1 Admin, 1 Parent, 1 Hospital)
INSERT INTO users (name, email, password, role, status) VALUES 
('System Admin', 'admin@vms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active'), -- password: password
('John Parent', 'john@parent.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', 'active'),
('City Hospital', 'contact@cityhospital.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hospital', 'active');

-- Parent Profile
INSERT INTO parents (user_id, phone, address) VALUES 
(2, '1234567890', '123 Main St, New York, NY');

-- Hospital Profile
INSERT INTO hospitals (user_id, hospital_name, registration_no, phone, address, status) VALUES 
(3, 'City General Hospital', 'REG-1001', '0987654321', '456 Medical Ave, New York, NY', 'approved');

-- Children
INSERT INTO children (parent_id, name, date_of_birth, gender, blood_group) VALUES 
(1, 'Baby Doe', '2025-01-01', 'Male', 'O+');

-- Vaccines
INSERT INTO vaccines (vaccine_name, description, availability_status) VALUES 
('BCG', 'Tuberculosis vaccine', 'available'),
('Hepatitis B', 'Prevents Hepatitis B', 'available'),
('Polio', 'Oral Polio Vaccine', 'available');

-- Vaccination Schedule
INSERT INTO vaccination_schedule (child_id, vaccine_id, scheduled_date, status) VALUES 
(1, 1, '2025-01-05', 'pending'), -- BCG
(1, 2, '2025-01-05', 'pending'); -- Hep B

-- Appointments
INSERT INTO appointments (parent_id, hospital_id, child_id, vaccine_id, appointment_date, status) VALUES 
(1, 1, 1, 1, '2025-02-10 10:00:00', 'requested');

-- Vaccination Records (Assuming appointment completed later)
-- INSERT INTO vaccination_records (appointment_id, vaccinated_date, remarks) VALUES (1, '2025-02-10', 'Successfully vaccinated without reactions');
