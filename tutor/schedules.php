<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('tutor');

$active = 'schedules';
$pageTitle = 'My Schedules - TMS';

$stmt = $pdo->prepare('SELECT id FROM tutors WHERE user_id = ?');
$stmt->execute([current_user()['id']]);
$tutorId = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT * FROM view_schedule_details WHERE tutor_id = ? ORDER BY class_date DESC, start_time DESC');
$stmt->execute([$tutorId]);
$upcoming = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">My Schedules</h1>
        <div class="card table-card">
            <?php include __DIR__ . '/../includes/schedule_table_readonly.php'; ?>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>


