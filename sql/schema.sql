CREATE DATABASE IF NOT EXISTS tms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tms_db;

DROP TABLE IF EXISTS schedule_audit;
DROP TABLE IF EXISTS tuition_applications;
DROP TABLE IF EXISTS tutor_profiles;
DROP TABLE IF EXISTS tuition_posts;
DROP TABLE IF EXISTS schedules;
DROP TABLE IF EXISTS tutors;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','tutor','student') NOT NULL,
    phone VARCHAR(30),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    description TEXT
) ENGINE=InnoDB;

CREATE TABLE tutors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    subject_id INT NOT NULL,
    experience INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_tutors_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tutors_subject
        FOREIGN KEY (subject_id) REFERENCES subjects(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_tutor_experience CHECK (experience >= 0)
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    class_name VARCHAR(50) NOT NULL,
    CONSTRAINT fk_students_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    class_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    CONSTRAINT fk_schedules_tutor
        FOREIGN KEY (tutor_id) REFERENCES tutors(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_schedules_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_schedules_subject
        FOREIGN KEY (subject_id) REFERENCES subjects(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_schedule_time CHECK (end_time > start_time)
) ENGINE=InnoDB;

CREATE TABLE schedule_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    tutor_id INT NOT NULL,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    action_type VARCHAR(30) NOT NULL,
    action_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tuition_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    class_name VARCHAR(80) NOT NULL,
    subject VARCHAR(120) NOT NULL,
    area VARCHAR(120) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    teaching_days VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tuition_posts_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_tuition_posts_salary CHECK (salary >= 0)
) ENGINE=InnoDB;

CREATE TABLE tutor_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    university VARCHAR(150) NOT NULL,
    department VARCHAR(150) NOT NULL,
    academic_year VARCHAR(60) NOT NULL,
    cgpa DECIMAL(3,2) NULL,
    teaching_experience TEXT NOT NULL,
    preferred_subjects VARCHAR(255) NOT NULL,
    preferred_areas VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) NULL,
    bio TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tutor_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT chk_tutor_profiles_cgpa CHECK (cgpa IS NULL OR (cgpa >= 0 AND cgpa <= 4))
) ENGINE=InnoDB;

CREATE TABLE tuition_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NULL,
    student_id INT NULL,
    tuition_id INT NOT NULL,
    application_message TEXT NOT NULL,
    status ENUM('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
    applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tuition_applications_tutor
        FOREIGN KEY (tutor_id) REFERENCES tutor_profiles(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tuition_applications_tuition
        FOREIGN KEY (tuition_id) REFERENCES tuition_posts(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_tuition_applications_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_tuition_applications_once UNIQUE (tutor_id, tuition_id),
    CONSTRAINT uq_tuition_applications_student_once UNIQUE (student_id, tuition_id)
) ENGINE=InnoDB;

CREATE INDEX idx_schedules_tutor_id ON schedules(tutor_id);
CREATE INDEX idx_schedules_student_id ON schedules(student_id);
CREATE INDEX idx_schedules_subject_id ON schedules(subject_id);
CREATE INDEX idx_schedules_class_date ON schedules(class_date);
CREATE INDEX idx_tuition_posts_created_at ON tuition_posts(created_at);
CREATE INDEX idx_tuition_posts_subject ON tuition_posts(subject);
CREATE INDEX idx_tuition_posts_area ON tuition_posts(area);
CREATE INDEX idx_tutor_profiles_subjects ON tutor_profiles(preferred_subjects);
CREATE INDEX idx_tutor_profiles_areas ON tutor_profiles(preferred_areas);
CREATE INDEX idx_tutor_profiles_university ON tutor_profiles(university);
CREATE INDEX idx_tuition_applications_status ON tuition_applications(status);
CREATE INDEX idx_tuition_applications_student ON tuition_applications(student_id);

INSERT INTO users (name, email, password, role, phone) VALUES
('Admin User', 'admin@tms.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin', '01700000000');

INSERT INTO subjects (subject_name, description) VALUES
('Mathematics', 'Algebra, geometry, and calculus support'),
('Physics', 'Mechanics, electricity, and modern physics'),
('English', 'Grammar, writing, and literature');
