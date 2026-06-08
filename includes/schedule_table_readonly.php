<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Tutor</th>
            <th>Student</th>
            <th>Subject</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($upcoming as $row): ?>
            <tr>
                <td><?= e($row['class_date']) ?></td>
                <td><?= e(substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5)) ?></td>
                <td><?= e($row['tutor_name']) ?></td>
                <td><?= e($row['student_name']) ?></td>
                <td><?= e($row['subject_name']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$upcoming): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No schedules found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


