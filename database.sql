-- FirstStep Job Platform Database
-- Import this file in phpMyAdmin (XAMPP) to create the database

CREATE DATABASE IF NOT EXISTS firststep CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE firststep;

-- ============================
-- Users table (job seekers, employers, admins)
-- ============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('jobseeker','employer','admin') NOT NULL DEFAULT 'jobseeker',
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    status ENUM('active','banned') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- Job seeker profile
-- ============================
CREATE TABLE jobseeker_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    education VARCHAR(255),
    skills TEXT,
    expected_salary_min INT DEFAULT 0,
    expected_salary_max INT DEFAULT 0,
    preferred_location VARCHAR(150),
    resume_file VARCHAR(255),
    bio TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================
-- Companies (owned by employer users)
-- ============================
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    logo VARCHAR(255),
    description TEXT,
    location VARCHAR(150),
    website VARCHAR(150),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================
-- Jobs
-- ============================
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    requirements TEXT,
    location VARCHAR(150),
    job_type ENUM('Full-time','Part-time','Internship','Contract') DEFAULT 'Full-time',
    category VARCHAR(100),
    salary_min INT DEFAULT 0,
    salary_max INT DEFAULT 0,
    status ENUM('open','closed') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- ============================
-- Applications
-- ============================
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    jobseeker_id INT NOT NULL,
    cover_letter TEXT,
    resume_file VARCHAR(255),
    status ENUM('pending','reviewed','accepted','rejected') DEFAULT 'pending',
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (jobseeker_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================
-- Articles (Tips & Insights)
-- ============================
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    category VARCHAR(100),
    image VARCHAR(255),
    content TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- Sample articles data
-- (Sample users/companies/jobs with correctly-hashed passwords are created
--  by running setup-seed.php once in your browser after import — see README)
-- ============================
INSERT INTO articles (title, category, content) VALUES
('5 Tips for Your First Job Interview', 'Career Tips', 'Preparing for your first job interview can be nerve-wracking. Here are five tips to help you succeed: research the company, practice common questions, dress appropriately, arrive early, and follow up with a thank-you email.'),
('How to Write a Resume with No Experience', 'Resume', 'New graduates often struggle to fill a resume. Focus on your education, projects, internships, volunteer work, and transferable skills instead of formal work experience.');
