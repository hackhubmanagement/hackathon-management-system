-- College Hackathons Feature Migration
-- Adds support for student-submitted college hackathons with admin approval

-- Add source column to hackathons table to distinguish between admin-created and approved college hackathons
ALTER TABLE hackathons ADD COLUMN source ENUM('admin', 'college') DEFAULT 'admin' AFTER status;

-- Create college_hackathons table for pending student submissions
CREATE TABLE college_hackathons (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    name VARCHAR(200) NOT NULL,
    theme VARCHAR(200) NOT NULL,
    description TEXT,
    rules TEXT,
    team_size INT(11) DEFAULT 4,
    registration_deadline DATETIME NOT NULL,
    submission_deadline DATETIME NOT NULL,
    prize_pool VARCHAR(100),
    college_name VARCHAR(150),
    location VARCHAR(150),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT(11) NULL,
    rejection_reason TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_student (student_id),
    INDEX idx_theme (theme)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add user preferences tracking for suggested hackathons
CREATE TABLE user_hackathon_preferences (
    id INT(11) NOT NULL AUTO_INCREMENT,
    student_id INT(11) NOT NULL,
    theme VARCHAR(200) NOT NULL,
    registration_count INT(11) DEFAULT 1,
    last_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_theme (student_id, theme),
    INDEX idx_student (student_id),
    INDEX idx_theme (theme)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for testing (optional)
-- INSERT INTO college_hackathons (student_id, name, theme, description, team_size, registration_deadline, submission_deadline, prize_pool, college_name, contact_email) VALUES
-- (1, 'Campus AI Challenge', 'AI', 'Internal college AI competition', 4, '2026-05-01 23:59:59', '2026-05-15 23:59:59', '₹50,000', 'Sample College', 'hackathon@college.edu');