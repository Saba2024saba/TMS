<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('tutor');

$active = 'dashboard';
$pageTitle = 'Tutor Dashboard - TMS';

$stmt = $pdo->prepare('SELECT id FROM tutors WHERE user_id = ?');
$stmt->execute([current_user()['id']]);
$tutorId = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM schedules WHERE tutor_id = ?');
$stmt->execute([$tutorId]);
$totalSchedules = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT id FROM tutor_profiles WHERE user_id = ?');
$stmt->execute([current_user()['id']]);
$profileId = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('
    SELECT COUNT(*)
    FROM tuition_applications a
    JOIN tuition_posts p ON p.id = a.tuition_id
    WHERE p.user_id = ? AND a.student_id IS NOT NULL
');
$stmt->execute([current_user()['id']]);
$totalApplications = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT * FROM view_schedule_details WHERE tutor_id = ? ORDER BY class_date, start_time LIMIT 5');
$stmt->execute([$tutorId]);
$upcoming = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Tutor Dashboard</h1>
        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card stat-card"><div class="card-body"><div class="text-muted small text-uppercase">My Schedules</div><div class="display-6 fw-semibold"><?= $totalSchedules ?></div></div></div></div>
            <div class="col-md-4"><div class="card stat-card"><div class="card-body"><div class="text-muted small text-uppercase">Applications</div><div class="display-6 fw-semibold"><?= $totalApplications ?></div></div></div></div>
        </div>
        <div class="card table-card">
            <div class="card-header bg-white fw-semibold">Upcoming Classes</div>
            <?php include __DIR__ . '/../includes/schedule_table_readonly.php'; ?>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


