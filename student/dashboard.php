<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('student');

$active = 'dashboard';
$pageTitle = 'Student Dashboard - TMS';

$stmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ?');
$stmt->execute([current_user()['id']]);
$studentId = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM schedules WHERE student_id = ?');
$stmt->execute([$studentId]);
$totalSchedules = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM tuition_applications WHERE student_id = ?');
$stmt->execute([$studentId]);
$totalApplications = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT * FROM view_schedule_details WHERE student_id = ? ORDER BY class_date, start_time LIMIT 5');
$stmt->execute([$studentId]);
$upcoming = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Student Dashboard</h1>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card stat-card"><div class="card-body"><div class="text-muted small text-uppercase">My Classes</div><div class="display-6 fw-semibold"><?= $totalSchedules ?></div></div></div></div>
            <div class="col-md-4"><div class="card stat-card"><div class="card-body"><div class="text-muted small text-uppercase">My Applications</div><div class="display-6 fw-semibold"><?= $totalApplications ?></div></div></div></div>
        </div>
        <div class="card table-card">
            <div class="card-header bg-white fw-semibold">Upcoming Classes</div>
            <?php include __DIR__ . '/../includes/schedule_table_readonly.php'; ?>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


