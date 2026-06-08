USE tms_db;

-- Basic: SELECT all tutors
SELECT t.id, u.name, u.email, u.phone, s.subject_name, t.experience
FROM tutors t
JOIN users u ON t.user_id = u.id
JOIN subjects s ON t.subject_id = s.id;

-- Basic: SELECT all students
SELECT st.id, u.name, u.email, u.phone, st.class_name
FROM students st
JOIN users u ON st.user_id = u.id;

-- Join: schedules with tutor, student, and subject
SELECT *
FROM view_schedule_details
ORDER BY class_date, start_time;

-- Aggregation: count schedules per tutor
SELECT tutor_id, tutor_name, COUNT(*) AS total_schedules
FROM view_schedule_details
GROUP BY tutor_id, tutor_name;

-- Aggregation: count schedules per subject
SELECT subject_id, subject_name, COUNT(*) AS total_schedules
FROM view_schedule_details
GROUP BY subject_id, subject_name;

-- Subquery: tutors with more than X schedules
SET @schedule_limit = 2;
SELECT t.id, u.name, u.email
FROM tutors t
JOIN users u ON t.user_id = u.id
WHERE t.id IN (
    SELECT tutor_id
    FROM schedules
    GROUP BY tutor_id
    HAVING COUNT(*) > @schedule_limit
);

