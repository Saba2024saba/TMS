USE tms_db;

DROP PROCEDURE IF EXISTS assign_schedule;
DROP PROCEDURE IF EXISTS assign_schedule_with_time;

DELIMITER $$
CREATE PROCEDURE assign_schedule(
    IN p_tutor_id INT,
    IN p_student_id INT,
    IN p_subject_id INT,
    IN p_class_date DATE
)
BEGIN
    INSERT INTO schedules (tutor_id, student_id, subject_id, class_date, start_time, end_time)
    VALUES (p_tutor_id, p_student_id, p_subject_id, p_class_date, '17:00:00', '18:00:00');
END$$

CREATE PROCEDURE assign_schedule_with_time(
    IN p_tutor_id INT,
    IN p_student_id INT,
    IN p_subject_id INT,
    IN p_class_date DATE,
    IN p_start_time TIME,
    IN p_end_time TIME
)
BEGIN
    INSERT INTO schedules (tutor_id, student_id, subject_id, class_date, start_time, end_time)
    VALUES (p_tutor_id, p_student_id, p_subject_id, p_class_date, p_start_time, p_end_time);
END$$
DELIMITER ;
