-- Student Complaint Management System Database Schema

CREATE DATABASE IF NOT EXISTS student_complaints;
USE student_complaints;

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    department VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Complaint Statuses
CREATE TABLE IF NOT EXISTS statuses (
    status_id INT PRIMARY KEY AUTO_INCREMENT,
    status_name VARCHAR(50) NOT NULL,
    status_color VARCHAR(20) NOT NULL
);

-- Insert default statuses
INSERT INTO statuses (status_name, status_color) VALUES
('Pending', '#ffc107'),
('In Progress', '#17a2b8'),
('Resolved', '#28a745'),
('Rejected', '#dc3545');

-- Complaints Table
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priority VARCHAR(20) DEFAULT 'Medium',
    media_file VARCHAR(255) NULL,
    status_id INT DEFAULT 1,
    assigned_to INT NULL,
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES statuses(status_id),
    FOREIGN KEY (assigned_to) REFERENCES admins(admin_id) ON DELETE SET NULL
);

-- Complaint Feedback Table
CREATE TABLE IF NOT EXISTS complaint_feedback (
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    admin_id INT NOT NULL,
    feedback_message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE CASCADE
);

-- Create default admin account (password: admin123)
-- Using INSERT IGNORE to skip if admin already exists
INSERT IGNORE INTO admins (name, email, password, role) VALUES
('System Administrator', 'admin@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin');

-- Create indexes for better performance
CREATE INDEX idx_student_email ON students(email);
CREATE INDEX idx_complaint_status ON complaints(status_id);
CREATE INDEX idx_complaint_student ON complaints(student_id);
CREATE INDEX idx_complaint_category ON complaints(category);
