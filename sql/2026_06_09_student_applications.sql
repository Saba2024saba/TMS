USE tms_db;

ALTER TABLE tuition_applications MODIFY tutor_id INT NULL;

DELIMITER //
CREATE PROCEDURE add_student_application_schema()
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'tuition_applications'
          AND column_name = 'student_id'
    ) THEN
        ALTER TABLE tuition_applications ADD COLUMN student_id INT NULL AFTER tutor_id;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'tuition_applications'
          AND index_name = 'idx_tuition_applications_student'
    ) THEN
        CREATE INDEX idx_tuition_applications_student ON tuition_applications(student_id);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.statistics
        WHERE table_schema = DATABASE()
          AND table_name = 'tuition_applications'
          AND index_name = 'uq_tuition_applications_student_once'
    ) THEN
        CREATE UNIQUE INDEX uq_tuition_applications_student_once ON tuition_applications(student_id, tuition_id);
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.table_constraints
        WHERE table_schema = DATABASE()
          AND table_name = 'tuition_applications'
          AND constraint_name = 'fk_tuition_applications_student'
    ) THEN
        ALTER TABLE tuition_applications
            ADD CONSTRAINT fk_tuition_applications_student
            FOREIGN KEY (student_id) REFERENCES students(id)
            ON DELETE CASCADE ON UPDATE CASCADE;
    END IF;
END//
DELIMITER ;

CALL add_student_application_schema();
DROP PROCEDURE add_student_application_schema;
