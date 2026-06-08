# Tuition Management System

A simple DBMS Lab project built with PHP 8+, MySQL, PDO, and Bootstrap 5. The database is the main focus: normalized tables, constraints, foreign keys, indexes, view, trigger, stored procedures, and sample SQL queries are included.

## Setup

1. Create/import the database SQL files in this order:
   - `sql/schema.sql`
   - `sql/views.sql`
   - `sql/triggers.sql`
   - `sql/procedures.sql`
2. Update database credentials in `config/db.php` if your MySQL user is not `root` with an empty password.
3. Serve the project from a PHP-enabled web server.
4. Login with:
   - Email: `admin@tms.test`
   - Password: `password`

## Project Structure

- `auth/` contains login and logout.
- `admin/` contains dashboard and CRUD modules.
- `tutor/` contains tutor dashboard and schedules.
- `student/` contains student dashboard and schedules.
- `includes/` contains shared layout, auth helpers, and reusable UI fragments.
- `sql/` contains schema, view, trigger, procedure, sample queries, and DBMS documentation.

## DBMS Highlights

- Single `users` table with role-based login.
- `tutors` and `students` are 1:1 profiles connected to `users`.
- `schedules` is the core entity connecting tutor, student, and subject.
- `view_schedule_details` shows joined schedule data.
- `trg_after_schedule_insert` logs schedule creation into `schedule_audit`.
- `assign_schedule` and `assign_schedule_with_time` demonstrate stored procedures.

