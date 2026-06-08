<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('student');

$active = 'applications';
$pageTitle = 'My Applications - TMS';
$status = $_GET['status'] ?? 'All';
$allowed = ['All', 'Pending', 'Accepted', 'Rejected'];
if (!in_array($status, $allowed, true)) {
    $status = 'All';
}

$stmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ?');
$stmt->execute([current_user()['id']]);
$studentId = (int) $stmt->fetchColumn();

$applications = [];
if ($studentId > 0) {
    $sql = '
        SELECT a.*, p.title, p.subject, p.area
        FROM tuition_applications a
        JOIN tuition_posts p ON p.id = a.tuition_id
        WHERE a.student_id = ?
    ';
    $params = [$studentId];
    if ($status !== 'All') {
        $sql .= ' AND a.status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY a.applied_at DESC, a.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">My Applications</h1>
            <a class="btn btn-primary" href="<?= e(url('/tuition/index.php')) ?>">Tuition Posts</a>
        </div>
        <?php render_flash_messages(); ?>
        <div class="btn-group mb-3">
            <?php foreach ($allowed as $item): ?>
                <a class="btn btn-sm <?= $status === $item ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= e(url('/student/applications.php?status=' . $item)) ?>"><?= e($item) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Tuition Title</th><th>Subject</th><th>Area</th><th>Application Date</th><th>Status</th><th>Message</th></tr></thead>
                    <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><a href="<?= e(url('/tuition/show.php?id=' . (int) $application['tuition_id'])) ?>"><?= e($application['title']) ?></a></td>
                            <td><?= e($application['subject']) ?></td>
                            <td><?= e($application['area']) ?></td>
                            <td><?= e(date('M d, Y', strtotime($application['applied_at']))) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e($application['status']) ?></span></td>
                            <td><?= e($application['application_message']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$applications): ?><tr><td colspan="6" class="text-center text-muted py-4">No applications found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
