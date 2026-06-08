<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login('admin');

$active = 'tuition_applications';
$pageTitle = 'Tuition Applications - TMS';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        if (($_POST['action'] ?? '') === 'update') {
            $stmt = $pdo->prepare('UPDATE tuition_applications SET status=? WHERE id=?');
            $stmt->execute([$_POST['status'], (int) $_POST['id']]);
            flash('success', 'Application status updated.');
        } elseif (($_POST['action'] ?? '') === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM tuition_applications WHERE id=?');
            $stmt->execute([(int) $_POST['id']]);
            flash('success', 'Application deleted.');
        }
        redirect_to('/admin/tuition_applications.php');
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
$status = $_GET['status'] ?? '';
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(p.title LIKE ? OR student_user.name LIKE ? OR tp.full_name LIKE ?)';
    array_push($params, "%$q%", "%$q%", "%$q%");
}
if (in_array($status, ['Pending', 'Accepted', 'Rejected'], true)) {
    $where[] = 'a.status = ?';
    $params[] = $status;
}
$sql = '
    SELECT a.*, p.title, student_user.name AS student_name, student_user.email AS student_email, tp.full_name AS tutor_name
    FROM tuition_applications a
    JOIN tuition_posts p ON p.id = a.tuition_id
    LEFT JOIN students s ON s.id = a.student_id
    LEFT JOIN users student_user ON student_user.id = s.user_id
    LEFT JOIN tutor_profiles tp ON tp.id = a.tutor_id
' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY a.applied_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="app-shell">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="content">
        <h1 class="h3 mb-4">Tuition Applications</h1>
        <?php render_flash_messages(); ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form class="card table-card mb-3" method="get"><div class="card-body row g-3">
            <div class="col-md-6"><input name="q" class="form-control" placeholder="Search tuition or applicant" value="<?= e($q) ?>"></div>
            <div class="col-md-3"><select name="status" class="form-select"><option value="">All statuses</option><?php foreach (['Pending','Accepted','Rejected'] as $item): ?><option value="<?= e($item) ?>" <?= $status === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><button class="btn btn-primary">Filter</button> <a class="btn btn-light" href="<?= e(url('/admin/tuition_applications.php')) ?>">Clear</a></div>
        </div></form>
        <div class="card table-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Tuition</th><th>Applicant</th><th>Date</th><th>Status</th><th>Message</th><th class="text-end">Actions</th></tr></thead><tbody>
            <?php foreach ($applications as $application): ?>
                <?php $applicantName = $application['student_name'] ?: ($application['tutor_name'] ?: 'Unknown'); ?>
                <tr><td><?= e($application['title']) ?></td><td><?= e($applicantName) ?><?php if ($application['student_email']): ?><div class="small text-muted"><?= e($application['student_email']) ?></div><?php endif; ?></td><td><?= e(date('M d, Y', strtotime($application['applied_at']))) ?></td><td><?= e($application['status']) ?></td><td><?= e($application['application_message']) ?></td><td class="text-end">
                    <form method="post" class="d-inline-flex gap-1 align-items-center"><?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int) $application['id'] ?>"><select name="status" class="form-select form-select-sm"><?php foreach (['Pending','Accepted','Rejected'] as $item): ?><option value="<?= e($item) ?>" <?= $application['status'] === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select><button class="btn btn-sm btn-outline-primary">Save</button></form>
                    <form method="post" class="d-inline" onsubmit="return confirm('Delete this application?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $application['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                </td></tr>
            <?php endforeach; ?>
            <?php if (!$applications): ?><tr><td colspan="6" class="text-center text-muted py-4">No applications found.</td></tr><?php endif; ?>
            </tbody></table></div></div>
    </main>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
