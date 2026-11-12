CREATE DATABASE IF NOT EXISTS portfolio_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE portfolio_db;

-- Admins (dashboard login)
CREATE TABLE IF NOT EXISTS admins (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Projects (portfolio items)
CREATE TABLE IF NOT EXISTS projects (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    slug        VARCHAR(160) NOT NULL UNIQUE,
    description TEXT         NOT NULL,
    category    VARCHAR(50)  NOT NULL,
    tech_stack  VARCHAR(255) DEFAULT NULL,
    image       VARCHAR(255) DEFAULT NULL,
    live_url    VARCHAR(255) DEFAULT NULL,
    github_url  VARCHAR(255) DEFAULT NULL,
    featured    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_featured (featured)
) ENGINE=InnoDB;

-- Services
CREATE TABLE IF NOT EXISTS services (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(100) NOT NULL,
    description TEXT         NOT NULL,
    icon        VARCHAR(50)  NOT NULL DEFAULT 'bi-code-slash',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- Courses (training section)
CREATE TABLE IF NOT EXISTS courses (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    description TEXT         NOT NULL,
    duration    VARCHAR(50)  DEFAULT NULL,
    level       VARCHAR(30)  DEFAULT 'Beginner',
    price       VARCHAR(50)  DEFAULT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Messages (contact form)
CREATE TABLE IF NOT EXISTS messages (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    subject    VARCHAR(200) DEFAULT NULL,
    message    TEXT         NOT NULL,
    is_read    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB;

-- Sample data
INSERT INTO services (title, description, icon, sort_order) VALUES
('Website Development',
 'Custom, responsive websites built from scratch with PHP, MySQL, Bootstrap 5 and JavaScript, tailored to your business or personal brand.',
 'bi-code-slash', 1),
('Website Fixing & Redesign',
 'Broken layout, slow pages, bugs or an outdated look? I correct and modernize existing websites without starting over.',
 'bi-tools', 2),
('Web Development Training',
 'Hands-on lessons in HTML, CSS, Bootstrap, JavaScript, PHP and MySQL for beginners and students, online or in person.',
 'bi-mortarboard', 3);

INSERT INTO courses (title, description, duration, level, price) VALUES
('HTML & CSS Fundamentals',
 'Learn how to structure and style web pages from zero, with practical mini projects.',
 '3 weeks', 'Beginner', 'Contact me'),
('Bootstrap 5 & Responsive Design',
 'Build mobile-friendly layouts quickly using the Bootstrap 5 grid and components.',
 '2 weeks', 'Beginner', 'Contact me'),
('PHP & MySQL Full Stack',
 'Build dynamic, database-driven websites with login systems, forms and admin panels.',
 '8 weeks', 'Intermediate', 'Contact me');

INSERT INTO projects (title, slug, description, category, tech_stack, featured) VALUES
('Sample Business Website',
 'sample-business-website',
 'A responsive company website with a contact form and admin-managed content. Replace this sample with your real project.',
 'Website', 'PHP, MySQL, Bootstrap 5, JavaScript', 1),
('Sample Website Redesign',
 'sample-website-redesign',
 'A before-and-after redesign of an outdated site, improving speed, layout and mobile experience. Replace this sample with your real project.',
 'Redesign', 'HTML, CSS, Bootstrap 5, JavaScript', 1);