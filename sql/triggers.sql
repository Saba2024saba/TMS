USE tms_db;

DROP TRIGGER IF EXISTS trg_after_schedule_insert;

DELIMITER $$
CREATE TRIGGER trg_after_schedule_insert
AFTER INSERT ON schedules
FOR EACH ROW
BEGIN
    INSERT INTO schedule_audit (schedule_id, tutor_id, student_id, subject_id, action_type)
    VALUES (NEW.id, NEW.tutor_id, NEW.student_id, NEW.subject_id, 'INSERT');
END$$
DELIMITER ;

