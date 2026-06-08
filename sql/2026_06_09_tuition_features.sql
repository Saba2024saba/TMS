USE tms_db;

CREATE TABLE IF NOT EXISTS tuition_posts (
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

CREATE TABLE IF NOT EXISTS tutor_profiles (
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

CREATE TABLE IF NOT EXISTS tuition_applications (
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

DELIMITER //
CREATE PROCEDURE add_index_if_missing(
    IN p_table_name VARCHAR(64),
    IN p_index_name VARCHAR(64),
    IN p_index_columns VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = p_table_name
          AND index_name = p_index_name
    ) THEN
        SET @sql = CONCAT('CREATE INDEX ', p_index_name, ' ON ', p_table_name, '(', p_index_columns, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//
DELIMITER ;

CALL add_index_if_missing('tuition_posts', 'idx_tuition_posts_created_at', 'created_at');
CALL add_index_if_missing('tuition_posts', 'idx_tuition_posts_subject', 'subject');
CALL add_index_if_missing('tuition_posts', 'idx_tuition_posts_area', 'area');
CALL add_index_if_missing('tutor_profiles', 'idx_tutor_profiles_subjects', 'preferred_subjects');
CALL add_index_if_missing('tutor_profiles', 'idx_tutor_profiles_areas', 'preferred_areas');
CALL add_index_if_missing('tutor_profiles', 'idx_tutor_profiles_university', 'university');
CALL add_index_if_missing('tuition_applications', 'idx_tuition_applications_status', 'status');
CALL add_index_if_missing('tuition_applications', 'idx_tuition_applications_student', 'student_id');

DROP PROCEDURE add_index_if_missing;
