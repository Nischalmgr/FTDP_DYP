DROP DATABASE IF EXISTS fashionhub;
CREATE DATABASE fashionhub;
USE fashionhub;

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
full_name VARCHAR(100) NOT NULL,
email VARCHAR(150) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,

role ENUM(
'designer',
'company',
'admin'
) NOT NULL,

profile_picture VARCHAR(255) DEFAULT NULL,
cover_picture VARCHAR(255) DEFAULT NULL,

is_verified ENUM('Y','N') DEFAULT 'Y',

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE designers (
id INT AUTO_INCREMENT PRIMARY KEY,

user_id INT NOT NULL,

headline VARCHAR(255),
bio TEXT,

location VARCHAR(150),

website VARCHAR(255),

portfolio_link VARCHAR(255),

experience_years INT DEFAULT 0,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE
);

CREATE TABLE designer_specializations (
id INT AUTO_INCREMENT PRIMARY KEY,

designer_id INT NOT NULL,

specialization_name VARCHAR(100) NOT NULL,

FOREIGN KEY (designer_id)
REFERENCES designers(id)
ON DELETE CASCADE
);

CREATE TABLE companies (
id INT AUTO_INCREMENT PRIMARY KEY,

user_id INT NOT NULL,

company_name VARCHAR(150) NOT NULL,

industry VARCHAR(100),

website VARCHAR(255),

company_size VARCHAR(50),

location VARCHAR(150),

description TEXT,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE
);

CREATE TABLE design_types (
id INT AUTO_INCREMENT PRIMARY KEY,

name VARCHAR(100) NOT NULL UNIQUE,

description TEXT,

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE designs (
id INT AUTO_INCREMENT PRIMARY KEY,

user_id INT NOT NULL,

title VARCHAR(255) NOT NULL,

description TEXT,

image VARCHAR(255),

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE
);

CREATE TABLE design_type_mapping (
design_id INT NOT NULL,

design_type_id INT NOT NULL,

PRIMARY KEY (
design_id,
design_type_id
),

FOREIGN KEY (design_id)
REFERENCES designs(id)
ON DELETE CASCADE,

FOREIGN KEY (design_type_id)
REFERENCES design_types(id)
ON DELETE CASCADE
);

CREATE TABLE design_likes (
id INT AUTO_INCREMENT PRIMARY KEY,

design_id INT NOT NULL,

user_id INT NOT NULL,

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (design_id)
REFERENCES designs(id)
ON DELETE CASCADE,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE,

UNIQUE KEY unique_like (
design_id,
user_id
)
);

CREATE TABLE design_comments (
id INT AUTO_INCREMENT PRIMARY KEY,

design_id INT NOT NULL,

user_id INT NOT NULL,

comment TEXT NOT NULL,

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (design_id)
REFERENCES designs(id)
ON DELETE CASCADE,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE
);

CREATE TABLE jobs (
id INT AUTO_INCREMENT PRIMARY KEY,

company_id INT NOT NULL,

title VARCHAR(200) NOT NULL,

description TEXT NOT NULL,

location VARCHAR(150),

salary VARCHAR(100),

employment_type ENUM(
'Full-Time',
'Part-Time',
'Internship',
'Contract',
'Freelance'
),

deadline DATE,

status ENUM('open','closed') NOT NULL DEFAULT 'open',

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (company_id)
REFERENCES companies(id)
ON DELETE CASCADE,

INDEX idx_jobs_company_status (company_id, status)
);

CREATE TABLE job_applications (
id INT AUTO_INCREMENT PRIMARY KEY,

job_id INT NOT NULL,

designer_id INT NOT NULL,

cover_letter TEXT,

resume VARCHAR(255),

application_status ENUM(
'pending',
'reviewed',
'shortlisted',
'rejected',
'hired'
) DEFAULT 'pending',

applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (job_id)
REFERENCES jobs(id)
ON DELETE CASCADE,

FOREIGN KEY (designer_id)
REFERENCES designers(id)
ON DELETE CASCADE,

UNIQUE KEY unique_application (
job_id,
designer_id
),

INDEX idx_applications_job_status (job_id, application_status, applied_at)
);

CREATE TABLE saved_designs (
id INT AUTO_INCREMENT PRIMARY KEY,

user_id INT NOT NULL,

design_id INT NOT NULL,

saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE,

FOREIGN KEY (design_id)
REFERENCES designs(id)
ON DELETE CASCADE,

UNIQUE KEY unique_saved_design (
user_id,
design_id
)
);

CREATE TABLE saved_jobs (
id INT AUTO_INCREMENT PRIMARY KEY,

user_id INT NOT NULL,

job_id INT NOT NULL,

saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE,

FOREIGN KEY (job_id)
REFERENCES jobs(id)
ON DELETE CASCADE,

UNIQUE KEY unique_saved_job (
user_id,
job_id
)
);

CREATE TABLE messages (
id INT AUTO_INCREMENT PRIMARY KEY,

sender_id INT NOT NULL,

receiver_id INT NOT NULL,

message TEXT NOT NULL,

is_read ENUM('Y','N') DEFAULT 'N',

sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (sender_id)
REFERENCES users(id)
ON DELETE CASCADE,

FOREIGN KEY (receiver_id)
REFERENCES users(id)
ON DELETE CASCADE,

INDEX idx_messages_pair (sender_id, receiver_id, id),

INDEX idx_messages_receiver_unread (receiver_id, is_read, sender_id)
);

CREATE TABLE notifications (
id INT AUTO_INCREMENT PRIMARY KEY,

user_id INT NOT NULL,

type VARCHAR(30) NOT NULL DEFAULT 'general',

title VARCHAR(255),

message TEXT NOT NULL,

link VARCHAR(255) DEFAULT NULL,

is_read ENUM('Y','N') DEFAULT 'N',

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE CASCADE,

INDEX idx_notifications_user_read (user_id, is_read, created_at)
);

INSERT INTO design_types (name,description) VALUES
('Fashion Design','General fashion design'),
('Streetwear','Streetwear collections'),
('Luxury Fashion','Luxury fashion collections'),
('Footwear Design','Footwear and sneakers'),
('Accessories Design','Accessories and jewellery'),
('Textile Design','Textile and fabric design'),
('Pattern Design','Pattern making'),
('Graphic Design','Fashion graphics'),
('3D Fashion','3D visualization'),
('Sustainable Fashion','Eco-friendly fashion');

INSERT INTO users (full_name, email, password, role, is_verified)
SELECT 'Admin', 'admin@ftdp.com', '$2y$10$oRz2jt8OqsR1wKNi6mLsRebiQI1eNjgqBTLCsQAYM2rT7X4QvP2JG', 'admin', 'Y'
WHERE NOT EXISTS (
SELECT 1 FROM users WHERE email = 'admin@ftdp.com'
);
