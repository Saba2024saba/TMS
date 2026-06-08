<?php
$selectedTutor = $schedule['tutor_id'] ?? '';
$selectedStudent = $schedule['student_id'] ?? '';
$selectedSubject = $schedule['subject_id'] ?? '';
?>
<div class="mb-3">
    <label class="form-label">Tutor</label>
    <select name="tutor_id" class="form-select" required>
        <?php foreach ($tutors as $tutor): ?>
            <option value="<?= (int) $tutor['id'] ?>" <?= (int) $selectedTutor === (int) $tutor['id'] ? 'selected' : '' ?>>
                <?= e($tutor['name'] . ' - ' . $tutor['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Student</label>
    <select name="student_id" class="form-select" required>
        <?php foreach ($students as $student): ?>
            <option value="<?= (int) $student['id'] ?>" <?= (int) $selectedStudent === (int) $student['id'] ? 'selected' : '' ?>>
                <?= e($student['name'] . ' - ' . $student['class_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Subject</label>
    <select name="subject_id" class="form-select" required>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?= (int) $subject['id'] ?>" <?= (int) $selectedSubject === (int) $subject['id'] ? 'selected' : '' ?>>
                <?= e($subject['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Class Date</label>
        <input type="date" name="class_date" class="form-control" value="<?= e($schedule['class_date'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Start Time</label>
        <input type="time" name="start_time" class="form-control" value="<?= e(isset($schedule['start_time']) ? substr($schedule['start_time'], 0, 5) : '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">End Time</label>
        <input type="time" name="end_time" class="form-control" value="<?= e(isset($schedule['end_time']) ? substr($schedule['end_time'], 0, 5) : '') ?>" required>
    </div>
</div>


