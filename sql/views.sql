USE tms_db;

CREATE OR REPLACE VIEW view_schedule_details AS
SELECT
    sc.id AS schedule_id,
    sc.class_date,
    sc.start_time,
    sc.end_time,
    t.id AS tutor_id,
    tu.name AS tutor_name,
    tu.email AS tutor_email,
    st.id AS student_id,
    su.name AS student_name,
    su.email AS student_email,
    sub.id AS subject_id,
    sub.subject_name
FROM schedules sc
JOIN tutors t ON sc.tutor_id = t.id
JOIN users tu ON t.user_id = tu.id
JOIN students st ON sc.student_id = st.id
JOIN users su ON st.user_id = su.id
JOIN subjects sub ON sc.subject_id = sub.id;

