-- Hospital System schema (MySQL 8+)
-- Run: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS hospital_system CHARACTER SET utf8mb4;
USE hospital_system;

CREATE TABLE IF NOT EXISTS patients (
    patientID   INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(255) NOT NULL UNIQUE,
    `password`  VARCHAR(255) NOT NULL,
    phone       VARCHAR(30),
    dateOfBirth DATE
);

CREATE TABLE IF NOT EXISTS doctor (
    doctorID       INT AUTO_INCREMENT PRIMARY KEY,
    full_name      VARCHAR(100) NOT NULL,
    email          VARCHAR(255) NOT NULL UNIQUE,
    `password`     VARCHAR(255) NOT NULL,
    specialisation VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS admin (
    adminID    INT AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS appointment (
    appointmentID INT AUTO_INCREMENT PRIMARY KEY,
    patientID     INT NOT NULL,
    doctorID      INT NOT NULL,
    `date`        DATE NOT NULL,
    time          TIME NOT NULL,
    status        ENUM('requested', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'requested',
    FOREIGN KEY (patientID) REFERENCES patients(patientID) ON DELETE CASCADE,
    FOREIGN KEY (doctorID)  REFERENCES doctor(doctorID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS medical_record (
    recordID  INT AUTO_INCREMENT PRIMARY KEY,
    patientID INT NOT NULL,
    doctorID  INT NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment TEXT NOT NULL,
    `date`    DATE NOT NULL,
    FOREIGN KEY (patientID) REFERENCES patients(patientID) ON DELETE CASCADE,
    FOREIGN KEY (doctorID)  REFERENCES doctor(doctorID) ON DELETE CASCADE
);

-- Create the first admin by hand, then add everyone else from the Admin Controls page:
-- 1. php -r "echo password_hash('ChangeMe123', PASSWORD_BCRYPT);"
-- 2. INSERT INTO admin (full_name, email, `password`) VALUES ('Admin', 'admin@example.com', '<hash from step 1>');
