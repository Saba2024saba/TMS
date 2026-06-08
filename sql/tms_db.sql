-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 09:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tms_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_schedule` (IN `p_tutor_id` INT, IN `p_student_id` INT, IN `p_subject_id` INT, IN `p_class_date` DATE)   BEGIN
    INSERT INTO schedules (tutor_id, student_id, subject_id, class_date, start_time, end_time)
    VALUES (p_tutor_id, p_student_id, p_subject_id, p_class_date, '17:00:00', '18:00:00');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_schedule_with_time` (IN `p_tutor_id` INT, IN `p_student_id` INT, IN `p_subject_id` INT, IN `p_class_date` DATE, IN `p_start_time` TIME, IN `p_end_time` TIME)   BEGIN
    INSERT INTO schedules (tutor_id, student_id, subject_id, class_date, start_time, end_time)
    VALUES (p_tutor_id, p_student_id, p_subject_id, p_class_date, p_start_time, p_end_time);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `tutor_id`, `student_id`, `subject_id`, `class_date`, `start_time`, `end_time`) VALUES
(1, 1, 2, 3, '2026-06-09', '12:07:00', '13:07:00');

--
-- Triggers `schedules`
--
DELIMITER $$
CREATE TRIGGER `trg_after_schedule_insert` AFTER INSERT ON `schedules` FOR EACH ROW BEGIN
    INSERT INTO schedule_audit (schedule_id, tutor_id, student_id, subject_id, action_type)
    VALUES (NEW.id, NEW.tutor_id, NEW.student_id, NEW.subject_id, 'INSERT');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_audit`
--

CREATE TABLE `schedule_audit` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `action_type` varchar(30) NOT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedule_audit`
--

INSERT INTO `schedule_audit` (`id`, `schedule_id`, `tutor_id`, `student_id`, `subject_id`, `action_type`, `action_time`) VALUES
(1, 1, 1, 2, 3, 'INSERT', '2026-06-08 18:07:31');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `class_name`) VALUES
(2, 4, '5');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `description`) VALUES
(1, 'Mathematics', 'Algebra, geometry, and calculus support'),
(2, 'Physics', 'Mechanics, electricity, and modern physics'),
(3, 'English', 'Grammar, writing, and literature');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_applications`
--

CREATE TABLE `tuition_applications` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `tuition_id` int(11) NOT NULL,
  `application_message` text NOT NULL,
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuition_applications`
--

INSERT INTO `tuition_applications` (`id`, `tutor_id`, `student_id`, `tuition_id`, `application_message`, `status`, `applied_at`) VALUES
(2, NULL, 2, 2, 'hi', 'Accepted', '2026-06-08 19:47:20'),
(3, NULL, 2, 4, 'Thanks', 'Accepted', '2026-06-08 19:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_posts`
--

CREATE TABLE `tuition_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `class_name` varchar(80) NOT NULL,
  `subject` varchar(120) NOT NULL,
  `area` varchar(120) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `teaching_days` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `tuition_posts`
--

INSERT INTO `tuition_posts` (`id`, `user_id`, `title`, `class_name`, `subject`, `area`, `salary`, `teaching_days`, `description`, `created_at`) VALUES
(2, 1, 'ict', '5', 'phython', 'gulshan', 5000.00, '10', 'tuition chai', '2026-06-08 19:26:37'),
(4, 2, 'coding', 'lab1', 'MySQL', 'gulshan', 10000.00, '30', 'Lets learn SQL', '2026-06-08 19:50:28');

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `experience` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`id`, `user_id`, `subject_id`, `experience`) VALUES
(1, 2, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tutor_profiles`
--

CREATE TABLE `tutor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `university` varchar(150) NOT NULL,
  `department` varchar(150) NOT NULL,
  `academic_year` varchar(60) NOT NULL,
  `cgpa` decimal(3,2) DEFAULT NULL,
  `teaching_experience` text NOT NULL,
  `preferred_subjects` varchar(255) NOT NULL,
  `preferred_areas` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `tutor_profiles`
--

INSERT INTO `tutor_profiles` (`id`, `user_id`, `full_name`, `phone`, `gender`, `university`, `department`, `academic_year`, `cgpa`, `teaching_experience`, `preferred_subjects`, `preferred_areas`, `profile_picture`, `bio`, `created_at`) VALUES
(1, 2, 'Teacher1', '12345678901', 'Male', 'uiu', 'cse', '2028', 3.00, '5yrs', 'phython', 'gulshan', 'uploads/tutors/tutor_2_97fb8d54ccd3a8ba.jpg', 'im a tuitor', '2026-06-08 19:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tutor','student') NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `created_at`) VALUES
(1, 'Admin User', 'admin@tms.test', '$2y$10$VNsOm57g0Y7lpRWYzFHDAOMgOuQDbMeob5f08EpyuTgGMxLzCy/2O', 'admin', '01700000000', '2026-06-08 17:55:51'),
(2, 'Teacher1', 'teacher1@gmail.com', '$2y$10$ZM/SDD2ITW..JEMs0luEvOHtYSRkNdws0BQ7r.K5mfFdbK42H4G8G', 'tutor', '01627272722', '2026-06-08 18:06:09'),
(4, 'Student1', 'student1@gmail.com', '$2y$10$7s0ClRPspM0denJ59uaX7OCxqsHKXjz8Xe21tOdB9ZJF48ZO6lMO2', 'student', '01818181817', '2026-06-08 18:07:12');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_schedule_details`
-- (See below for the actual view)
--
CREATE TABLE `view_schedule_details` (
`schedule_id` int(11)
,`class_date` date
,`start_time` time
,`end_time` time
,`tutor_id` int(11)
,`tutor_name` varchar(100)
,`tutor_email` varchar(150)
,`student_id` int(11)
,`student_name` varchar(100)
,`student_email` varchar(150)
,`subject_id` int(11)
,`subject_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Structure for view `view_schedule_details`
--
DROP TABLE IF EXISTS `view_schedule_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_schedule_details`  AS SELECT `sc`.`id` AS `schedule_id`, `sc`.`class_date` AS `class_date`, `sc`.`start_time` AS `start_time`, `sc`.`end_time` AS `end_time`, `t`.`id` AS `tutor_id`, `tu`.`name` AS `tutor_name`, `tu`.`email` AS `tutor_email`, `st`.`id` AS `student_id`, `su`.`name` AS `student_name`, `su`.`email` AS `student_email`, `sub`.`id` AS `subject_id`, `sub`.`subject_name` AS `subject_name` FROM (((((`schedules` `sc` join `tutors` `t` on(`sc`.`tutor_id` = `t`.`id`)) join `users` `tu` on(`t`.`user_id` = `tu`.`id`)) join `students` `st` on(`sc`.`student_id` = `st`.`id`)) join `users` `su` on(`st`.`user_id` = `su`.`id`)) join `subjects` `sub` on(`sc`.`subject_id` = `sub`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_schedules_tutor_id` (`tutor_id`),
  ADD KEY `idx_schedules_student_id` (`student_id`),
  ADD KEY `idx_schedules_subject_id` (`subject_id`),
  ADD KEY `idx_schedules_class_date` (`class_date`);

--
-- Indexes for table `schedule_audit`
--
ALTER TABLE `schedule_audit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tuition_applications`
--
ALTER TABLE `tuition_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tuition_applications_once` (`tutor_id`,`tuition_id`),
  ADD UNIQUE KEY `uq_tuition_applications_student_once` (`student_id`,`tuition_id`),
  ADD KEY `fk_tuition_applications_tuition` (`tuition_id`),
  ADD KEY `idx_tuition_applications_status` (`status`),
  ADD KEY `idx_tuition_applications_student` (`student_id`);

--
-- Indexes for table `tuition_posts`
--
ALTER TABLE `tuition_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tuition_posts_user` (`user_id`),
  ADD KEY `idx_tuition_posts_created_at` (`created_at`),
  ADD KEY `idx_tuition_posts_subject` (`subject`),
  ADD KEY `idx_tuition_posts_area` (`area`);

--
-- Indexes for table `tutors`
--
ALTER TABLE `tutors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_tutors_subject` (`subject_id`);

--
-- Indexes for table `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_tutor_profiles_subjects` (`preferred_subjects`),
  ADD KEY `idx_tutor_profiles_areas` (`preferred_areas`),
  ADD KEY `idx_tutor_profiles_university` (`university`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `schedule_audit`
--
ALTER TABLE `schedule_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tuition_applications`
--
ALTER TABLE `tuition_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tuition_posts`
--
ALTER TABLE `tuition_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tutors`
--
ALTER TABLE `tutors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedules_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedules_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tuition_applications`
--
ALTER TABLE `tuition_applications`
  ADD CONSTRAINT `fk_tuition_applications_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tuition_applications_tuition` FOREIGN KEY (`tuition_id`) REFERENCES `tuition_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tuition_applications_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `tutor_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tuition_posts`
--
ALTER TABLE `tuition_posts`
  ADD CONSTRAINT `fk_tuition_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tutors`
--
ALTER TABLE `tutors`
  ADD CONSTRAINT `fk_tutors_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tutors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  ADD CONSTRAINT `fk_tutor_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
