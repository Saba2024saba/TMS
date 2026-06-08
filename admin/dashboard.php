<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'dashboard';
$pageTitle = 'Admin Dashboard - TMS';

$counts = [
    'users' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM users'),
    'subjects' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM subjects'),
    'tutors' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM tutors'),
    'tutor profiles' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM tutor_profiles'),
    'tuition posts' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM tuition_posts'),
    'applications' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM tuition_applications'),
    'students' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM students'),
    'schedules' => (int) db_fetch_value($pdo, 'SELECT COUNT(*) FROM schedules'),
];

$recent = db_fetch_all($pdo, 'SELECT * FROM view_schedule_details ORDER BY class_date DESC, start_time DESC LIMIT 5');

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Admin Dashboard</h1>
                <div class="text-muted">Database-first overview of the tuition system.</div>
            </div>
            <a href="<?= e(url('/admin/schedules.php')) ?>" class="btn btn-primary">Assign Schedule</a>
        </div>

        <div class="row g-3 mb-4">
            <?php foreach ($counts as $label => $count): ?>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase"><?= e($label) ?></div>
                            <div class="display-6 fw-semibold"><?= $count ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card table-card">
            <div class="card-header bg-white fw-semibold">Recent Schedules</div>
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
                    <?php foreach ($recent as $row): ?>
                        <tr>
                            <td><?= e($row['class_date']) ?></td>
                            <td><?= e(substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5)) ?></td>
                            <td><?= e($row['tutor_name']) ?></td>
                            <td><?= e($row['student_name']) ?></td>
                            <td><?= e($row['subject_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No schedules yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

