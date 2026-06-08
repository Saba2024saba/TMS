<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('tutor');

$active = 'applications';
$pageTitle = 'Student Applications - TMS';
$status = $_GET['status'] ?? 'All';
$allowed = ['All', 'Pending', 'Accepted', 'Rejected'];
if (!in_array($status, $allowed, true)) {
    $status = 'All';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'update' && in_array($_POST['status'] ?? '', ['Pending', 'Accepted', 'Rejected'], true)) {
        $stmt = $pdo->prepare('
            UPDATE tuition_applications a
            JOIN tuition_posts p ON p.id = a.tuition_id
            SET a.status = ?
            WHERE a.id = ? AND p.user_id = ? AND a.student_id IS NOT NULL
        ');
        $stmt->execute([$_POST['status'], (int) $_POST['id'], current_user()['id']]);
        flash('success', 'Application status updated.');
    }
    redirect_to('/tutor/applications.php');
}

$sql = '
    SELECT a.*, p.title, u.name AS student_name, u.email AS student_email
    FROM tuition_applications a
    JOIN tuition_posts p ON p.id = a.tuition_id
    JOIN students s ON s.id = a.student_id
    JOIN users u ON u.id = s.user_id
    WHERE p.user_id = ? AND a.student_id IS NOT NULL
';
$params = [current_user()['id']];
if ($status !== 'All') {
    $sql .= ' AND a.status = ?';
    $params[] = $status;
}
$sql .= ' ORDER BY a.applied_at DESC, a.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Student Applications</h1>
            <a class="btn btn-primary" href="<?= e(url('/tuition/create.php')) ?>">Post Tuition</a>
        </div>
        <?php render_flash_messages(); ?>
        <form method="get" class="mb-3">
            <div class="btn-group">
                <?php foreach ($allowed as $item): ?>
                    <a class="btn btn-sm <?= $status === $item ? 'btn-primary' : 'btn-outline-primary' ?>" href="<?= e(url('/tutor/applications.php?status=' . $item)) ?>"><?= e($item) ?></a>
                <?php endforeach; ?>
            </div>
        </form>
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Tuition Title</th><th>Student</th><th>Application Date</th><th>Status</th><th>Application Message</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><a href="<?= e(url('/tuition/show.php?id=' . (int) $application['tuition_id'])) ?>"><?= e($application['title']) ?></a></td>
                            <td><?= e($application['student_name']) ?><div class="small text-muted"><?= e($application['student_email']) ?></div></td>
                            <td><?= e(date('M d, Y', strtotime($application['applied_at']))) ?></td>
                            <td><?= e($application['status']) ?></td>
                            <td><?= e($application['application_message']) ?></td>
                            <td class="text-end">
                                <form method="post" class="d-inline-flex gap-1 align-items-center">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?= (int) $application['id'] ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (['Pending','Accepted','Rejected'] as $item): ?><option value="<?= e($item) ?>" <?= $application['status'] === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
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
